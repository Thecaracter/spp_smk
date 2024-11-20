<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HasTunggakan
{
    public static function bootHasTunggakan()
    {
        // Saat membuat tagihan baru
        static::created(function ($model) {
            try {
                DB::transaction(function () use ($model) {
                    // Saat membuat tagihan baru, tambahkan ke total tunggakan
                    $model->user->increment('total_tunggakan', $model->total_tagihan);
                    Log::info('Tunggakan ditambahkan pada pembuatan tagihan', [
                        'tagihan_id' => $model->id,
                        'jumlah' => $model->total_tagihan,
                        'total_tunggakan_baru' => $model->user->total_tunggakan
                    ]);
                });
            } catch (\Exception $e) {
                Log::error('Error updating tunggakan on create: ' . $e->getMessage());
                throw $e;
            }
        });

        // Saat mengupdate tagihan
        static::updated(function ($model) {
            try {
                DB::transaction(function () use ($model) {
                    $changes = [];

                    // Update tunggakan jika total_tagihan berubah
                    if ($model->wasChanged('total_tagihan')) {
                        $selisihTagihan = $model->total_tagihan - $model->getOriginal('total_tagihan');
                        if ($selisihTagihan != 0) {
                            $model->user->increment('total_tunggakan', $selisihTagihan);
                            $changes['perubahan_total_tagihan'] = $selisihTagihan;
                        }
                    }

                    // Update tunggakan jika total_terbayar berubah
                    if ($model->wasChanged('total_terbayar')) {
                        $selisihPembayaran = $model->total_terbayar - $model->getOriginal('total_terbayar');
                        if ($selisihPembayaran > 0) {
                            // Jika ada penambahan pembayaran, kurangi tunggakan
                            $model->user->decrement('total_tunggakan', $selisihPembayaran);
                            $changes['pengurangan_tunggakan'] = $selisihPembayaran;
                        }
                    }

                    if (!empty($changes)) {
                        Log::info('Tunggakan diupdate', [
                            'tagihan_id' => $model->id,
                            'perubahan' => $changes,
                            'total_tunggakan_baru' => $model->user->total_tunggakan
                        ]);
                    }
                });
            } catch (\Exception $e) {
                Log::error('Error updating tunggakan on update: ' . $e->getMessage());
                throw $e;
            }
        });

        // Saat menghapus tagihan
        static::deleted(function ($model) {
            try {
                DB::transaction(function () use ($model) {
                    $sisaTagihan = $model->total_tagihan - $model->total_terbayar;
                    if ($sisaTagihan > 0) {
                        $model->user->decrement('total_tunggakan', $sisaTagihan);
                        Log::info('Tunggakan dikurangi saat tagihan dihapus', [
                            'tagihan_id' => $model->id,
                            'pengurangan' => $sisaTagihan,
                            'total_tunggakan_baru' => $model->user->total_tunggakan
                        ]);
                    }
                });
            } catch (\Exception $e) {
                Log::error('Error updating tunggakan on delete: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Hitung ulang total tunggakan untuk user tertentu
     */
    public function recalculateTotalTunggakan(): float
    {
        try {
            return DB::transaction(function () {
                $totalTunggakan = $this->where('user_id', $this->user_id)
                    ->where('status', '!=', 'lunas')
                    ->get()
                    ->sum(function ($tagihan) {
                        return $tagihan->total_tagihan - $tagihan->total_terbayar;
                    });

                $this->user->update(['total_tunggakan' => $totalTunggakan]);

                Log::info('Total tunggakan dihitung ulang', [
                    'user_id' => $this->user_id,
                    'total_tunggakan_baru' => $totalTunggakan
                ]);

                return $totalTunggakan;
            });
        } catch (\Exception $e) {
            Log::error('Error recalculating tunggakan: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mendapatkan semua tunggakan yang masih aktif
     */
    public function getActiveTunggakan()
    {
        return $this->where('user_id', $this->user_id)
            ->where('status', '!=', 'lunas')
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->get();
    }

    /**
     * Mendapatkan total tunggakan per jenis pembayaran
     */
    public function getTunggakanByJenis()
    {
        return $this->where('user_id', $this->user_id)
            ->where('status', '!=', 'lunas')
            ->select(
                'jenis_pembayaran_id',
                DB::raw('SUM(total_tagihan - total_terbayar) as total_tunggakan'),
                DB::raw('COUNT(*) as jumlah_tagihan')
            )
            ->groupBy('jenis_pembayaran_id')
            ->with('jenis_pembayaran')
            ->get();
    }

    /**
     * Mendapatkan ringkasan tunggakan
     */
    public function getTunggakanSummary()
    {
        $tunggakan = $this->where('user_id', $this->user_id)
            ->where('status', '!=', 'lunas')
            ->get();

        return [
            'total_tunggakan' => $tunggakan->sum(function ($tagihan) {
                return $tagihan->total_tagihan - $tagihan->total_terbayar;
            }),
            'jumlah_tagihan' => $tunggakan->count(),
            'tagihan_belum_bayar' => $tunggakan->where('status', 'belum_bayar')->count(),
            'tagihan_cicilan' => $tunggakan->where('status', 'cicilan')->count(),
            'tagihan_jatuh_tempo' => $tunggakan->where('tanggal_jatuh_tempo', '<', now())->count()
        ];
    }

    /**
     * Cek apakah mahasiswa memiliki tunggakan
     */
    public function hasTunggakan(): bool
    {
        return $this->user->total_tunggakan > 0;
    }

    /**
     * Mendapatkan history pembayaran tunggakan
     */
    public function getTunggakanPaymentHistory()
    {
        return $this->where('user_id', $this->user_id)
            ->with([
                'pembayaran' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'jenis_pembayaran'
            ])
            ->get()
            ->map(function ($tagihan) {
                return [
                    'id' => $tagihan->id,
                    'jenis_pembayaran' => $tagihan->jenis_pembayaran->nama,
                    'total_tagihan' => $tagihan->total_tagihan,
                    'sisa_tagihan' => $tagihan->total_tagihan - $tagihan->total_terbayar,
                    'status' => $tagihan->status,
                    'tanggal_jatuh_tempo' => $tagihan->tanggal_jatuh_tempo,
                    'pembayaran' => $tagihan->pembayaran->map(function ($pembayaran) {
                        return [
                            'tanggal' => $pembayaran->created_at,
                            'jumlah' => $pembayaran->jumlah_bayar,
                            'status' => $pembayaran->status,
                            'verifikasi_oleh' => optional($pembayaran->verifikator)->name,
                            'tanggal_verifikasi' => $pembayaran->tanggal_verifikasi
                        ];
                    })
                ];
            });
    }

    /**
     * Cek status kelayakan untuk pendaftaran semester
     */
    public function checkRegistrationEligibility(): array
    {
        $tunggakan = $this->getTunggakanSummary();
        $eligible = $tunggakan['total_tunggakan'] <= 0;

        return [
            'eligible' => $eligible,
            'tunggakan' => $tunggakan,
            'message' => $eligible
                ? 'Mahasiswa dapat mendaftar semester baru'
                : 'Mahasiswa memiliki tunggakan yang harus diselesaikan sebelum pendaftaran'
        ];
    }

    /**
     * Mendapatkan detail tunggakan untuk laporan
     */
    public function getTunggakanReport()
    {
        return [
            'mahasiswa' => [
                'nim' => $this->user->nim,
                'nama' => $this->user->name,
                'semester_aktif' => $this->user->semester_aktif,
                'status' => $this->user->status_mahasiswa
            ],
            'tunggakan' => $this->getTunggakanSummary(),
            'detail_per_jenis' => $this->getTunggakanByJenis(),
            'history_pembayaran' => $this->getTunggakanPaymentHistory(),
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];
    }
}