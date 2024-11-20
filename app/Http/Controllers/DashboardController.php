<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $total_mahasiswa = User::where('role', 'mahasiswa')->count();
            $mahasiswa_aktif = User::where('role', 'mahasiswa')
                ->where('status_siswa', 'aktif')
                ->count();

            $total_tagihan = Tagihan::sum('total_tagihan');
            $total_terbayar = Tagihan::sum('total_terbayar');

            $pending_payments = Pembayaran::where('status_transaksi', 'pending')
                ->with(['tagihan.user', 'tagihan.jenis_pembayaran'])
                ->latest()
                ->take(5)
                ->get();

            $mahasiswa_tunggakan = User::where('role', 'mahasiswa')
                ->where('total_tunggakan', '>', 0)
                ->orderBy('total_tunggakan', 'desc')
                ->take(5)
                ->get();

            return view('pages.dashboard', compact(
                'total_mahasiswa',
                'mahasiswa_aktif',
                'total_tagihan',
                'total_terbayar',
                'pending_payments',
                'mahasiswa_tunggakan'
            ));
        }

        if ($user->role === 'mahasiswa') {
            $tagihan_aktif = Tagihan::with(['jenis_pembayaran', 'pembayaran'])
                ->where('user_id', $user->id)
                ->orderBy('tanggal_jatuh_tempo', 'desc')
                ->get();

            $total_tagihan = $tagihan_aktif->sum('total_tagihan');
            $total_terbayar = $tagihan_aktif->sum('total_terbayar');
            $total_tunggakan = $total_tagihan - $total_terbayar;
            $tagihan_terbaru = $tagihan_aktif->take(5);

            $pembayaran_settlement = Pembayaran::whereHas('tagihan', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('status_transaksi', 'settlement')
                ->sum('jumlah_bayar');

            $pembayaran_count = Pembayaran::whereHas('tagihan', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('status_transaksi', 'settlement')
                ->count();

            if ($user->total_tunggakan != $total_tunggakan) {
                $user->total_tunggakan = $total_tunggakan;
                $user->save();
            }

            return view('pages.dashboard', compact(
                'tagihan_terbaru',
                'total_tagihan',
                'total_terbayar',
                'total_tunggakan',
                'pembayaran_settlement',
                'pembayaran_count',
                'user'
            ));
        }

        return view('pages.dashboard', [
            'tagihan_terbaru' => collect(),
            'total_tagihan' => 0,
            'total_terbayar' => 0,
            'pembayaran_count' => 0
        ]);
    }
}