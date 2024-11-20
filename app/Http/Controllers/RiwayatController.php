<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Pembayaran::with(['tagihan.user', 'tagihan.jenis_pembayaran', 'verifikator'])
            ->where('status', 'terverifikasi');

        if ($search) {
            $query->whereHas('tagihan.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");

            });
        }

        $riwayat = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('pages.riwayat', compact('riwayat'));
        }

        return view('pages.riwayat', compact('riwayat'));
    }

    public function show(Pembayaran $pembayaran)
    {
        // Pastikan hanya pembayaran terverifikasi yang bisa diakses
        if ($pembayaran->status !== 'terverifikasi') {
            return response()->json(['error' => 'Pembayaran tidak ditemukan'], 404);
        }

        $pembayaran->load(['tagihan.user', 'tagihan.jenis_pembayaran', 'verifikator']);

        // Pastikan semua relasi terload sebelum mengirim response
        return response()->json([
            'pembayaran' => $pembayaran,
            'mahasiswa' => $pembayaran->tagihan->user,
            'jenis_pembayaran' => $pembayaran->tagihan->jenis_pembayaran,
            'verifikator' => $pembayaran->verifikator,
            'tagihan' => [
                'total_tagihan' => $pembayaran->tagihan->total_tagihan,
                'total_terbayar' => $pembayaran->tagihan->total_terbayar,
                'sisa_tagihan' => $pembayaran->tagihan->total_tagihan - $pembayaran->tagihan->total_terbayar,
                'status' => $pembayaran->tagihan->status
            ]
        ]);
    }

    public function showBukti(Pembayaran $pembayaran)
    {
        // Pastikan hanya pembayaran terverifikasi yang bisa diakses
        if ($pembayaran->status !== 'terverifikasi') {
            return response()->json(['error' => 'Bukti pembayaran tidak ditemukan'], 404);
        }

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
            return response()->json(['error' => 'Terjadi kesalahan saat menampilkan bukti pembayaran'], 500);
        }
    }
}