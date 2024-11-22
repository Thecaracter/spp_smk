<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            // Get total students and active students count
            $total_siswa = User::where('role', 'siswa')->count();
            $siswa_aktif = User::where('role', 'siswa')
                ->where('status_siswa', 'aktif')
                ->count();

            // Calculate total bills and payments
            $total_tagihan = Tagihan::sum('total_tagihan');
            $total_terbayar = Tagihan::sum('total_terbayar');

            // Get latest pending payments with relationships
            $pending_payments = Pembayaran::where('status_transaksi', 'pending')
                ->with(['tagihan.user', 'tagihan.jenis_pembayaran'])
                ->latest()
                ->take(5)
                ->get();

            // Get students with highest outstanding payments
            $siswa_tunggakan = User::where('role', 'siswa')
                ->where('total_tunggakan', '>', 0)
                ->orderBy('total_tunggakan', 'desc')
                ->take(5)
                ->get();

            // Chart Data Preparations

            // 1. Payment Status Distribution
            $status_pembayaran = Tagihan::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [
                        ucfirst(str_replace('_', ' ', $item->status)) => $item->total
                    ];
                });

            // 2. Monthly Payment Trends (Last 6 months)
            $monthly_payments = Pembayaran::where('status_transaksi', 'settlement')
                ->where('created_at', '>=', now()->subMonths(6))
                ->select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('SUM(jumlah_bayar) as total_payments')
                )
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get()
                ->map(function ($item) {
                    return [
                        'month' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                        'total' => $item->total_payments
                    ];
                });

            // 3. Class Distribution of Outstanding Payments
            $class_tunggakan = User::where('role', 'siswa')
                ->where('total_tunggakan', '>', 0)
                ->select('kelas', DB::raw('SUM(total_tunggakan) as total_tunggakan'))
                ->groupBy('kelas')
                ->orderBy('kelas')
                ->get()
                ->mapWithKeys(function ($item) {
                    return ["Kelas {$item->kelas}" => $item->total_tunggakan];
                });

            // 4. Analisis Keterlambatan
            $keterlambatan_pembayaran = DB::table('tagihan')
                ->select(
                    DB::raw('
                       CASE 
                           WHEN DATEDIFF(CURDATE(), tanggal_jatuh_tempo) <= 30 THEN "1-30 hari"
                           WHEN DATEDIFF(CURDATE(), tanggal_jatuh_tempo) <= 60 THEN "31-60 hari" 
                           WHEN DATEDIFF(CURDATE(), tanggal_jatuh_tempo) <= 90 THEN "61-90 hari"
                           ELSE "Lebih dari 90 hari"
                       END as rentang_keterlambatan'),
                    DB::raw('COUNT(*) as jumlah'),
                    DB::raw('SUM(total_tagihan - total_terbayar) as total_tunggakan')
                )
                ->where('status', '!=', 'lunas')
                ->where('tanggal_jatuh_tempo', '<', now())
                ->groupBy('rentang_keterlambatan')
                ->orderByRaw("
                   CASE rentang_keterlambatan 
                       WHEN '1-30 hari' THEN 1
                       WHEN '31-60 hari' THEN 2
                       WHEN '61-90 hari' THEN 3
                       ELSE 4
                   END
               ")
                ->get()
                ->mapWithKeys(function ($item) {
                    return [
                        $item->rentang_keterlambatan => [
                            'jumlah' => $item->jumlah,
                            'total' => $item->total_tunggakan
                        ]
                    ];
                });

            return view('pages.dashboard', compact(
                'total_siswa',
                'siswa_aktif',
                'total_tagihan',
                'total_terbayar',
                'pending_payments',
                'siswa_tunggakan',
                'status_pembayaran',
                'monthly_payments',
                'class_tunggakan',
                'keterlambatan_pembayaran'
            ));
        }

        if ($user->role === 'siswa') {
            $tagihan_aktif = Tagihan::with(['jenis_pembayaran', 'pembayaran'])
                ->where('user_id', $user->id)
                ->orderBy('tanggal_jatuh_tempo', 'desc')
                ->get();

            $total_tagihan = $tagihan_aktif->sum('total_tagihan');
            $total_terbayar = $tagihan_aktif->sum('total_terbayar');
            $total_tunggakan = $total_tagihan - $total_terbayar;
            $tagihan_terbaru = $tagihan_aktif->take(5);

            // Student's Payment History Chart
            $history_pembayaran = Pembayaran::whereHas('tagihan', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('status_transaksi', 'settlement')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(jumlah_bayar) as total_bayar')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => Carbon::parse($item->date)->format('d M Y'),
                        'total' => $item->total_bayar
                    ];
                });

            // Payment Status Distribution for Student
            $status_tagihan = $tagihan_aktif
                ->groupBy('status')
                ->map(function ($group) {
                    return count($group);
                });

            if ($user->total_tunggakan != $total_tunggakan) {
                $user->total_tunggakan = $total_tunggakan;
                $user->save();
            }

            $pembayaran_count = Pembayaran::whereHas('tagihan', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status_transaksi', 'settlement')->count();

            return view('pages.dashboard', compact(
                'tagihan_terbaru',
                'total_tagihan',
                'total_terbayar',
                'total_tunggakan',
                'user',
                'history_pembayaran',
                'status_tagihan',
                'pembayaran_count'
            ));
        }

        return view('pages.dashboard', [
            'tagihan_terbaru' => collect(),
            'total_tagihan' => 0,
            'total_terbayar' => 0,
            'total_tunggakan' => 0
        ]);
    }
}