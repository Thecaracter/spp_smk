<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait AutoCalculateSisaTagihan
{
    public static function bootAutoCalculateSisaTagihan()
    {
        static::saving(function (Model $model) {
            try {
                DB::transaction(function () use ($model) {
                    // Log nilai sebelum kalkulasi
                    Log::info('Sebelum kalkulasi sisa tagihan', [
                        'tagihan_id' => $model->id,
                        'total_tagihan' => $model->total_tagihan,
                        'total_terbayar_original' => $model->getOriginal('total_terbayar'),
                        'total_terbayar_new' => $model->total_terbayar
                    ]);

                    // Hitung sisa tagihan
                    $sisa = $model->total_tagihan - $model->total_terbayar;

                    // Update status berdasarkan kondisi pembayaran tanpa modifikasi nilai
                    $model->status = match (true) {
                        $sisa <= 0 => 'lunas',
                        $model->total_terbayar > 0 => 'cicilan',
                        default => 'belum_bayar'
                    };

                    Log::info('Setelah update status', [
                        'tagihan_id' => $model->id,
                        'sisa_tagihan' => $sisa,
                        'status_baru' => $model->status,
                        'total_terbayar' => $model->total_terbayar
                    ]);

                });
            } catch (\Exception $e) {
                Log::error('Error calculating sisa tagihan: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    // Hapus method updateCicilanInfo() karena ini yang menyebabkan perubahan nilai pembayaran

    /**
     * Cek status tagihan
     */
    public function isLunas(): bool
    {
        return $this->status === 'lunas';
    }

    public function isCicilan(): bool
    {
        return $this->status === 'cicilan';
    }

    public function isBelumBayar(): bool
    {
        return $this->status === 'belum_bayar';
    }

    /**
     * Mendapatkan persentase pembayaran
     */
    public function getPersentasePembayaran(): float
    {
        if ($this->total_tagihan <= 0) {
            return 0;
        }

        return round(($this->total_terbayar / $this->total_tagihan) * 100, 2);
    }

    /**
     * Mendapatkan informasi cicilan
     */
    public function getCicilanInfo(): array
    {
        return [
            'total_tagihan' => $this->total_tagihan,
            'total_terbayar' => $this->total_terbayar,
            'sisa_tagihan' => $this->total_tagihan - $this->total_terbayar,
            'persentase_terbayar' => $this->getPersentasePembayaran()
        ];
    }

    /**
     * Validasi pembayaran cicilan
     */
    public function validatePembayaranCicilan($jumlahBayar): bool
    {
        if (!$this->jenis_pembayaran->dapat_dicicil) {
            throw new \Exception('Pembayaran ini tidak dapat dicicil');
        }

        $sisaTagihan = $this->total_tagihan - $this->total_terbayar;
        return $jumlahBayar <= $sisaTagihan;
    }
}