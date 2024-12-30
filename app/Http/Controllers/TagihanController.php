<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use App\Models\JenisPembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\TagihanNotification;

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
            $jurusan = Jurusan::all();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'users' => $users,
                        'jenisPembayaran' => $jenisPembayaran,
                        'jurusan' => $jurusan
                    ]
                ]);
            }

            return view('pages.tagihan', compact('users', 'jenisPembayaran', 'jurusan'));
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

                // Debug log
                Log::info('Mencoba kirim notifikasi', [
                    'user_id' => $user->id,
                    'no_telepon' => $user->no_telepon
                ]);

                // Tambahkan notifikasi SMS disini
                if ($user->no_telepon) {
                    try {
                        $user->notify(new TagihanNotification($tagihan));
                        Log::info('Notifikasi berhasil dikirim');
                    } catch (\Exception $e) {
                        Log::error('Error saat kirim notifikasi: ' . $e->getMessage());
                        // Tidak throw error agar transaksi tetap berjalan
                    }
                }

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

    public function checkAffectedStudents(Request $request)
    {
        try {
            $validated = $request->validate([
                'jurusan_id' => 'required|exists:jurusan,id',
                'kelas' => 'required|in:10,11,12',
            ]);

            $count = User::where('role', 'siswa')
                ->where('jurusan_id', $validated['jurusan_id'])
                ->where('kelas', $validated['kelas'])
                ->where('status_siswa', 'aktif')
                ->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (Exception $e) {
            Log::error('Error in TagihanController@checkAffectedStudents: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek jumlah siswa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'jurusan_id' => 'required|exists:jurusan,id',
                'kelas' => 'required|in:10,11,12',
                'jenis_pembayaran_id' => 'required|exists:jenis_pembayaran,id',
                'tanggal_jatuh_tempo' => 'required|date|after:today',
            ]);

            DB::beginTransaction();
            try {
                $jenisPembayaran = JenisPembayaran::findOrFail($validated['jenis_pembayaran_id']);

                // Ambil semua siswa yang sesuai kriteria
                $users = User::where('role', 'siswa')
                    ->where('jurusan_id', $validated['jurusan_id'])
                    ->where('kelas', $validated['kelas'])
                    ->where('status_siswa', 'aktif')
                    ->get();

                if ($users->isEmpty()) {
                    throw new Exception('Tidak ada siswa yang sesuai dengan kriteria');
                }

                $notificationErrors = [];
                $successCount = 0;

                // Buat tagihan untuk setiap siswa
                foreach ($users as $user) {
                    $tagihan = new Tagihan([
                        'user_id' => $user->id,
                        'jenis_pembayaran_id' => $jenisPembayaran->id,
                        'total_tagihan' => $jenisPembayaran->nominal,
                        'tanggal_jatuh_tempo' => $validated['tanggal_jatuh_tempo'],
                        'status' => 'belum_bayar'
                    ]);
                    $tagihan->save();

                    // Kirim notifikasi jika user punya nomor telepon
                    if ($user->no_telepon) {
                        try {
                            $user->notify(new TagihanNotification($tagihan));
                            $successCount++;
                        } catch (\Exception $e) {
                            // Catat error notifikasi tapi jangan hentikan prosesnya
                            $notificationErrors[] = "Gagal mengirim notifikasi ke {$user->name}: {$e->getMessage()}";
                            Log::error("Gagal mengirim notifikasi bulk tagihan: {$e->getMessage()}", [
                                'user_id' => $user->id,
                                'no_telepon' => $user->no_telepon
                            ]);
                        }
                    }
                }

                DB::commit();

                // Siapkan pesan response
                $message = 'Tagihan massal berhasil dibuat untuk ' . $users->count() . ' siswa. ';
                if ($successCount > 0) {
                    $message .= "Berhasil mengirim {$successCount} notifikasi. ";
                }
                if (!empty($notificationErrors)) {
                    $message .= "Terdapat " . count($notificationErrors) . " gagal kirim notifikasi.";
                }

                return response()->json([
                    'message' => $message,
                    'success' => true,
                    'notification_errors' => $notificationErrors,
                    'total_tagihan' => $users->count(),
                    'notification_sent' => $successCount
                ]);

            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            Log::error('Error in TagihanController@bulkStore: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal membuat tagihan massal: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    public function update(Request $request, User $user, Tagihan $tagihan)
    {
        try {
            $validated = $request->validate([
                'tanggal_jatuh_tempo' => 'required|date|after:today',
            ]);

            DB::beginTransaction();
            try {
                $tagihan->update([
                    'tanggal_jatuh_tempo' => $validated['tanggal_jatuh_tempo']
                ]);

                DB::commit();

                return response()->json([
                    'message' => 'Tagihan berhasil diperbarui',
                    'success' => true
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            Log::error('Error in TagihanController@update: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui tagihan: ' . $e->getMessage(),
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