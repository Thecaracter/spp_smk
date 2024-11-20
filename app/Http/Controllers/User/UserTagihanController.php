<?php

namespace App\Http\Controllers\User;

use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UserTagihanController extends Controller
{
    public function index()
    {
        $tagihan = Tagihan::with([
            'jenis_pembayaran',
            'pembayaran' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }
        ])
            ->where('user_id', auth()->id())
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->get();

        return view('pages.user-tagihan', compact('tagihan'));
    }

    public function bayar(Request $request, Tagihan $tagihan)
    {
        try {
            $request->validate([
                'jumlah_bayar' => 'required|numeric|min:1',
                'bukti_pembayaran' => 'required|string',
            ]);

            // Validate ownership
            if ($tagihan->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke tagihan ini'
                ], 403);
            }

            // Validate payment amount
            if ($request->jumlah_bayar > ($tagihan->total_tagihan - $tagihan->total_terbayar)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah pembayaran melebihi sisa tagihan'
                ], 422);
            }

            // Check if payment can be paid in installments
            if (!$tagihan->jenis_pembayaran->dapat_dicicil) {
                if ($request->jumlah_bayar != ($tagihan->total_tagihan - $tagihan->total_terbayar)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pembayaran ini harus dilunasi sekaligus'
                    ], 422);
                }
            }

            // Create payment record
            $pembayaran = Pembayaran::create([
                'tagihan_id' => $tagihan->id,
                'jumlah_bayar' => $request->jumlah_bayar,
                'bukti_pembayaran' => $request->bukti_pembayaran, // Already in base64
                'status' => 'menunggu'
            ]);

            // Update tagihan status ke menunggu
            $tagihan->update(['status' => 'menunggu']);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil disimpan dan sedang menunggu verifikasi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pembayaran'
            ], 500);
        }
    }

    public function updatePembayaran(Request $request, Tagihan $tagihan, Pembayaran $pembayaran)
    {
        try {
            $request->validate([
                'jumlah_bayar' => 'required|numeric|min:1',
                'bukti_pembayaran' => 'required|string',
            ]);

            // Validate ownership and permissions
            if ($tagihan->user_id !== auth()->id() || $pembayaran->tagihan_id !== $tagihan->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke pembayaran ini'
                ], 403);
            }

            // Only rejected payments can be updated
            if ($pembayaran->status !== 'ditolak') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pembayaran yang ditolak yang dapat diupdate'
                ], 422);
            }

            // Calculate new remaining amount
            $sisaTagihan = $tagihan->total_tagihan - $tagihan->total_terbayar;

            // Validate payment amount
            if ($request->jumlah_bayar > $sisaTagihan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah pembayaran melebihi sisa tagihan'
                ], 422);
            }

            // Check if payment can be paid in installments
            if (!$tagihan->jenis_pembayaran->dapat_dicicil && $request->jumlah_bayar != $sisaTagihan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran ini harus dilunasi sekaligus'
                ], 422);
            }

            // Update the payment record
            $pembayaran->update([
                'jumlah_bayar' => $request->jumlah_bayar,
                'bukti_pembayaran' => $request->bukti_pembayaran,
                'status' => 'menunggu',
                'catatan' => null
            ]);

            // Update status tagihan ke menunggu
            $tagihan->update(['status' => 'menunggu']);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diupdate dan sedang menunggu verifikasi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pembayaran'
            ], 500);
        }
    }
}