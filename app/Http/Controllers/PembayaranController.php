<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Pembayaran::with(['tagihan.user', 'tagihan.jenis_pembayaran'])
            ->where('status', 'menunggu')
        ;

        if ($search) {
            $query->whereHas('tagihan.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $pembayaran = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('pages.pembayaran', compact('pembayaran'));
        }

        return view('pages.pembayaran', compact('pembayaran'));
    }

    public function store(Request $request, Tagihan $tagihan)
    {
        try {
            DB::beginTransaction();

            Log::info('Mulai proses pembayaran', [
                'tagihan_id' => $tagihan->id,
                'request' => $request->all()
            ]);

            $request->validate([
                'jumlah_bayar' => 'required|numeric|min:1',
                'bukti_pembayaran' => 'required|string'
            ]);

            // Validasi sisa tagihan
            $sisaTagihan = $tagihan->total_tagihan - $tagihan->total_terbayar;
            if ($request->jumlah_bayar > $sisaTagihan) {
                return response()->json([
                    'message' => 'Jumlah pembayaran melebihi sisa tagihan',
                    'sisa_tagihan' => $sisaTagihan
                ], 422);
            }

            // Buat pembayaran baru
            $pembayaran = Pembayaran::create([
                'tagihan_id' => $tagihan->id,
                'jumlah_bayar' => $request->jumlah_bayar,
                'bukti_pembayaran' => $request->bukti_pembayaran,
                'status' => 'menunggu'
            ]);

            Log::info('Pembayaran berhasil dibuat', [
                'pembayaran_id' => $pembayaran->id,
                'jumlah_bayar' => $pembayaran->jumlah_bayar
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Pembayaran berhasil disimpan dan menunggu verifikasi',
                'pembayaran' => $pembayaran->load('tagihan')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saat membuat pembayaran', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load(['tagihan.user', 'tagihan.jenis_pembayaran', 'verifikator']);
        return response()->json([
            'pembayaran' => $pembayaran,
            'tagihan' => [
                'total_tagihan' => $pembayaran->tagihan->total_tagihan,
                'total_terbayar' => $pembayaran->tagihan->total_terbayar,
                'sisa_tagihan' => $pembayaran->tagihan->total_tagihan - $pembayaran->tagihan->total_terbayar,
                'status' => $pembayaran->tagihan->status
            ],
            'mahasiswa' => $pembayaran->tagihan->user,
            'jenis_pembayaran' => $pembayaran->tagihan->jenis_pembayaran,
            'verifikator' => $pembayaran->verifikator
        ]);
    }

    public function showBukti(Pembayaran $pembayaran)
    {
        try {
            if (strpos($pembayaran->bukti_pembayaran, ';base64,') !== false) {
                list($type, $data) = explode(';', $pembayaran->bukti_pembayaran);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);

                return response($data)
                    ->header('Content-Type', str_replace('data:', '', $type));
            }
            return response()->json(['error' => 'Format bukti pembayaran tidak valid'], 400);
        } catch (\Exception $e) {
            Log::error('Error menampilkan bukti pembayaran', [
                'pembayaran_id' => $pembayaran->id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Terjadi kesalahan saat menampilkan bukti pembayaran'], 500);
        }
    }

    public function verifikasi(Request $request, Pembayaran $pembayaran)
    {
        try {
            Log::info('Mulai verifikasi pembayaran', [
                'pembayaran_id' => $pembayaran->id,
                'request' => $request->all()
            ]);

            DB::beginTransaction();

            $request->validate([
                'status' => 'required|in:terverifikasi,ditolak',
                'catatan' => 'required_if:status,ditolak|string|nullable',
            ]);

            if ($pembayaran->status !== 'menunggu') {
                Log::warning('Pembayaran sudah diverifikasi', [
                    'pembayaran_id' => $pembayaran->id,
                    'status' => $pembayaran->status
                ]);
                return response()->json([
                    'message' => 'Pembayaran ini sudah diverifikasi sebelumnya',
                    'status' => $pembayaran->status,
                ], 422);
            }

            $tagihan = $pembayaran->tagihan;

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Nonaktifkan sementara auto-calculating traits
            $tagihan->unsetEventDispatcher();

            // Update pembayaran terlebih dahulu
            $pembayaran->update([
                'status' => $request->status,
                'catatan' => $request->catatan,
                'tanggal_verifikasi' => Carbon::now(),
                'verifikasi_oleh' => Auth::id(),
            ]);

            if ($request->status === 'terverifikasi') {
                $jumlahBayar = $pembayaran->jumlah_bayar;

                $tagihan->total_terbayar += $jumlahBayar;

                $user = $tagihan->user;
                $user->total_tunggakan = $user->total_tunggakan - $jumlahBayar;
                $user->save();
            }

            // Update status tagihan tanpa trigger auto-calculate
            $tagihan->timestamps = false; // Hindari updated_at berubah
            $tagihan->status = $request->status === 'terverifikasi' && $tagihan->total_terbayar >= $tagihan->total_tagihan
                ? 'lunas'
                : 'cicilan';
            $tagihan->save();
            $tagihan->timestamps = true;

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            DB::commit();

            return response()->json([
                'message' => 'Pembayaran berhasil ' . ($request->status === 'terverifikasi' ? 'diverifikasi' : 'ditolak'),
                'status' => $request->status,
                'pembayaran' => $pembayaran->fresh(['tagihan.user', 'tagihan.jenis_pembayaran']),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saat verifikasi pembayaran', [
                'pembayaran_id' => $pembayaran->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $query = Pembayaran::with(['tagihan.user', 'tagihan.jenis_pembayaran', 'verifikator']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('tagihan.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%");
                });
            }

            if ($request->filled(['start_date', 'end_date'])) {
                $query->whereBetween('created_at', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay(),
                ]);
            }

            $pembayaran = $query->get();

            $exportData = $pembayaran->map(function ($item) {
                return [
                    'Tanggal' => $item->created_at->format('d/m/Y H:i'),
                    'NIM' => $item->tagihan->user->nim,
                    'Nama Mahasiswa' => $item->tagihan->user->name,
                    'Jenis Pembayaran' => $item->tagihan->jenis_pembayaran->nama,
                    'Total Tagihan' => $this->formatRupiah($item->tagihan->total_tagihan),
                    'Jumlah Bayar' => $this->formatRupiah($item->jumlah_bayar),
                    'Sisa Tagihan' => $this->formatRupiah($item->tagihan->total_tagihan - $item->tagihan->total_terbayar),
                    'Status' => ucfirst($item->status),
                    'Verifikator' => $item->verifikator ? $item->verifikator->name : '-',
                    'Tanggal Verifikasi' => $item->tanggal_verifikasi ? $item->tanggal_verifikasi->format('d/m/Y H:i') : '-'
                ];
            });

            return response()->json([
                'data' => $exportData,
                'filename' => 'Laporan_Pembayaran_' . now()->format('dmY_His')
            ]);

        } catch (\Exception $e) {
            Log::error('Error saat export pembayaran', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Terjadi kesalahan saat mengexport data'
            ], 500);
        }
    }

    protected function getStatusBadgeClass($status)
    {
        return match ($status) {
            'terverifikasi' => 'bg-green-100 text-green-800',
            'ditolak' => 'bg-red-100 text-red-800',
            default => 'bg-yellow-100 text-yellow-800'
        };
    }

    protected function formatRupiah($nominal)
    {
        return "Rp " . number_format($nominal, 0, ',', '.');
    }
    public function search(Request $request)
    {
        $search = $request->get('search');

        $pembayaran = Pembayaran::with(['tagihan.user', 'tagihan.jenis_pembayaran'])
            ->where('status', 'menunggu')
            ->whereHas('tagihan.user', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('pages.pembayaran', compact('pembayaran'))->render(),
                'pagination' => $pembayaran->links()->toHtml()
            ]);
        }

        return view('pages.pembayaran', compact('pembayaran'));
    }
}