<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tagihan;
use App\Models\JenisPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $users = User::where('role', 'siswa')
                ->withCount([
                    'tagihan as tagihan_belum_lunas' => function ($query) {
                        $query->where('status', '!=', 'lunas');
                    }
                ])
                ->with([
                    'tagihan' => function ($query) {
                        $query->withSum('pembayaran', 'jumlah_bayar');
                    }
                ])
                ->orderBy('total_tunggakan', 'desc')
                ->paginate(10);

            $jenisPembayaran = JenisPembayaran::all();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'users' => $users,
                        'jenisPembayaran' => $jenisPembayaran
                    ]
                ]);
            }

            return view('pages.tagihan', compact('users', 'jenisPembayaran'));
        } catch (Exception $e) {
            Log::error('Error in TagihanController@index: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memuat data'
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }

    public function getDetail(User $user)
    {
        if (!request()->ajax()) {
            return abort(404);
        }

        try {
            $tagihan = Tagihan::where('user_id', $user->id)
                ->with([
                    'jenis_pembayaran',
                    'pembayaran' => function ($query) {
                        $query->orderBy('created_at', 'desc');
                    }
                ])
                ->get();

            $tunggakanByJenis = $user->tagihan()
                ->where('status', '!=', 'lunas')
                ->select('jenis_pembayaran_id', DB::raw('SUM(total_tagihan - total_terbayar) as total_tunggakan'))
                ->groupBy('jenis_pembayaran_id')
                ->with('jenis_pembayaran')
                ->get();

            $tunggakanSummary = [
                'total_tunggakan' => $user->total_tunggakan,
                'tagihan_aktif' => $user->tagihan()->where('status', '!=', 'lunas')->count(),
                'belum_bayar' => $user->tagihan()->where('status', 'belum_bayar')->count(),
                'cicilan' => $user->tagihan()->where('status', 'cicilan')->count(),
            ];

            return response()->json([
                'success' => true,
                'user' => $user,
                'tagihan' => $tagihan,
                'tunggakan_by_jenis' => $tunggakanByJenis,
                'tunggakan_summary' => $tunggakanSummary
            ]);
        } catch (Exception $e) {
            Log::error('Error in TagihanController@getDetail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat detail tagihan'
            ], 500);
        }
    }

    public function store(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'jenis_pembayaran_id' => 'required|exists:jenis_pembayaran,id',
                'tanggal_jatuh_tempo' => 'required|date|after:today',
            ]);

            DB::beginTransaction();
            try {
                $jenisPembayaran = JenisPembayaran::findOrFail($validated['jenis_pembayaran_id']);

                $tagihan = new Tagihan([
                    'user_id' => $user->id,
                    'jenis_pembayaran_id' => $jenisPembayaran->id,
                    'total_tagihan' => $jenisPembayaran->nominal,
                    'tanggal_jatuh_tempo' => $validated['tanggal_jatuh_tempo'],
                    'status' => 'belum_bayar'
                ]);

                $tagihan->save();
                DB::commit();

                return response()->json([
                    'message' => 'Tagihan berhasil dibuat',
                    'success' => true
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            Log::error('Error in TagihanController@store: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal membuat tagihan: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    public function destroy(User $user, Tagihan $tagihan)
    {
        try {
            DB::beginTransaction();
            try {
                if ($tagihan->pembayaran()->exists()) {
                    throw new Exception('Tidak dapat menghapus tagihan yang sudah memiliki pembayaran');
                }

                $tagihan->delete();
                DB::commit();

                return response()->json([
                    'message' => 'Tagihan berhasil dihapus',
                    'success' => true
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            Log::error('Error in TagihanController@destroy: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menghapus tagihan: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }
}