<?php

namespace App\Http\Controllers\User;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class UserTagihanController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

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

    private function createMidtransParams($tagihan, $jumlahBayar, $kodeTransaksi)
    {
        return [
            'transaction_details' => [
                'order_id' => $kodeTransaksi,
                'gross_amount' => (int) $jumlahBayar,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => auth()->user()->no_telepon ?? '08123456789',
            ],
            'item_details' => [
                [
                    'id' => $tagihan->jenis_pembayaran->id,
                    'price' => (int) $jumlahBayar,
                    'quantity' => 1,
                    'name' => $tagihan->jenis_pembayaran->nama,
                ]
            ],
            'expiry' => [
                'unit' => 'days',
                'duration' => 1
            ]
        ];
    }

    public function bayar(Request $request, Tagihan $tagihan)
    {
        try {
            Log::info('Starting payment process', [
                'tagihan_id' => $tagihan->id,
                'amount' => $request->jumlah_bayar
            ]);

            $request->validate([
                'jumlah_bayar' => 'required|numeric|min:1',
            ]);

            if ($tagihan->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke tagihan ini'
                ], 403);
            }

            // Cek pembayaran pending yang sudah ada
            $existingPayment = Pembayaran::where('tagihan_id', $tagihan->id)
                ->where('status_transaksi', 'pending')
                ->first();

            if ($existingPayment) {
                Log::info('Using existing pending payment', [
                    'payment_id' => $existingPayment->id,
                    'snap_token' => $existingPayment->snap_token
                ]);

                return response()->json([
                    'success' => true,
                    'snap_token' => $existingPayment->snap_token,
                    'kode_transaksi' => $existingPayment->kode_transaksi
                ]);
            }

            if ($request->jumlah_bayar > ($tagihan->total_tagihan - $tagihan->total_terbayar)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah pembayaran melebihi sisa tagihan'
                ], 422);
            }

            if (!$tagihan->jenis_pembayaran->dapat_dicicil) {
                if ($request->jumlah_bayar != ($tagihan->total_tagihan - $tagihan->total_terbayar)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pembayaran ini harus dilunasi sekaligus'
                    ], 422);
                }
            }

            DB::beginTransaction();

            $kodeTransaksi = 'TRX-' . strtoupper(Str::random(8)) . '-' . time();

            // Buat pembayaran baru
            $pembayaran = Pembayaran::create([
                'tagihan_id' => $tagihan->id,
                'jumlah_bayar' => $request->jumlah_bayar,
                'kode_transaksi' => $kodeTransaksi,
                'status_transaksi' => 'pending',
                'detail_pembayaran' => []
            ]);

            $params = $this->createMidtransParams($tagihan, $request->jumlah_bayar, $kodeTransaksi);
            $snapToken = Snap::getSnapToken($params);
            $pembayaran->update(['snap_token' => $snapToken]);

            DB::commit();

            Log::info('Payment created successfully', [
                'payment_id' => $pembayaran->id,
                'kode_transaksi' => $kodeTransaksi,
                'snap_token' => $snapToken
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'kode_transaksi' => $kodeTransaksi
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in payment process', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus($kodeTransaksi)
    {
        try {
            Log::info('Updating payment status', ['kode_transaksi' => $kodeTransaksi]);

            DB::beginTransaction();

            // Cari pembayaran berdasarkan kode transaksi
            $pembayaran = Pembayaran::where('kode_transaksi', $kodeTransaksi)
                ->where('status_transaksi', 'pending')
                ->firstOrFail();

            Log::info('Found payment', [
                'payment_id' => $pembayaran->id,
                'current_status' => $pembayaran->status_transaksi
            ]);

            // Update status pembayaran
            $pembayaran->status_transaksi = 'settlement';
            $pembayaran->save();

            // Update tagihan terkait
            $tagihan = $pembayaran->tagihan;
            $tagihan->total_terbayar = $tagihan->total_terbayar + $pembayaran->jumlah_bayar;

            // Update status tagihan
            if ($tagihan->total_terbayar >= $tagihan->total_tagihan) {
                $tagihan->status = 'lunas';
            } else {
                $tagihan->status = 'cicilan';
            }

            $tagihan->save();

            DB::commit();

            Log::info('Status updated successfully', [
                'payment_status' => $pembayaran->status_transaksi,
                'tagihan_status' => $tagihan->status,
                'total_terbayar' => $tagihan->total_terbayar
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diupdate',
                'data' => [
                    'status_pembayaran' => $pembayaran->status_transaksi,
                    'status_tagihan' => $tagihan->status,
                    'total_terbayar' => $tagihan->total_terbayar
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating status', [
                'kode_transaksi' => $kodeTransaksi,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkStatus($kodeTransaksi)
    {
        try {
            Log::info('Checking payment status', ['kode_transaksi' => $kodeTransaksi]);

            $pembayaran = Pembayaran::where('kode_transaksi', $kodeTransaksi)
                ->whereHas('tagihan', function ($q) {
                    $q->where('user_id', auth()->id());
                })
                ->firstOrFail();

            Log::info('Payment status', [
                'current_status' => $pembayaran->status_transaksi,
                'has_snap_token' => !empty($pembayaran->snap_token)
            ]);

            if ($pembayaran->status_transaksi === 'pending' && $pembayaran->snap_token) {
                return response()->json([
                    'success' => true,
                    'status' => 'pending',
                    'snap_token' => $pembayaran->snap_token,
                    'kode_transaksi' => $pembayaran->kode_transaksi
                ]);
            }

            return response()->json([
                'success' => true,
                'status' => $pembayaran->status_transaksi,
                'data' => [
                    'status_pembayaran' => $pembayaran->status_transaksi,
                    'total_terbayar' => $pembayaran->tagihan->total_terbayar,
                    'status_tagihan' => $pembayaran->tagihan->status
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking payment status', [
                'kode_transaksi' => $kodeTransaksi,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek status pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function notification(Request $request)
    {
        try {
            Log::info('Received Midtrans notification', ['payload' => $request->all()]);

            $notif = new \Midtrans\Notification();
            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $orderId = $notif->order_id;
            $fraud = $notif->fraud_status;

            Log::info('Parsed notification', [
                'transaction_status' => $transaction,
                'payment_type' => $type,
                'order_id' => $orderId,
                'fraud_status' => $fraud
            ]);

            DB::beginTransaction();

            $pembayaran = Pembayaran::where('kode_transaksi', $orderId)->firstOrFail();
            $tagihan = $pembayaran->tagihan;

            if ($transaction == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $pembayaran->status_transaksi = 'challenge';
                    } else {
                        $pembayaran->status_transaksi = 'settlement';
                    }
                }
            } else if ($transaction == 'settlement') {
                $pembayaran->status_transaksi = 'settlement';

                // Update tagihan
                $tagihan->total_terbayar = $tagihan->total_terbayar + $pembayaran->jumlah_bayar;

                if ($tagihan->total_terbayar >= $tagihan->total_tagihan) {
                    $tagihan->status = 'lunas';
                } else {
                    $tagihan->status = 'cicilan';
                }

                $tagihan->save();
            } else if ($transaction == 'pending') {
                $pembayaran->status_transaksi = 'pending';
            } else if ($transaction == 'deny') {
                $pembayaran->status_transaksi = 'deny';
            } else if ($transaction == 'expire') {
                $pembayaran->status_transaksi = 'expire';
            } else if ($transaction == 'cancel') {
                $pembayaran->status_transaksi = 'cancel';
            }

            $pembayaran->detail_pembayaran = $request->all();
            $pembayaran->save();

            DB::commit();

            Log::info('Notification processed successfully', [
                'payment_id' => $pembayaran->id,
                'new_status' => $pembayaran->status_transaksi,
                'tagihan_status' => $tagihan->status ?? null
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error processing notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing notification: ' . $e->getMessage()
            ], 500);
        }
    }
}