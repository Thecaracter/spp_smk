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
                // Hitung sisa tagihan
                $sisa = $model->total_tagihan - $model->total_terbayar;

                // Update status based on conditions
                if ($sisa <= 0) {
                    $model->status = 'lunas';
                } elseif ($model->total_terbayar > 0) {
                    $model->status = 'cicilan';
                } else {
                    $model->status = 'belum_bayar';
                }

                Log::info('AutoCalculateSisaTagihan: Updating status', [
                    'tagihan_id' => $model->id,
                    'total_tagihan' => $model->total_tagihan,
                    'total_terbayar' => $model->total_terbayar,
                    'sisa' => $sisa,
                    'new_status' => $model->status
                ]);

            } catch (\Exception $e) {
                Log::error('Error in AutoCalculateSisaTagihan: ' . $e->getMessage());
                throw $e;
            }
        });
    }

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

    /**
     * Get sisa tagihan
     */
    public function getSisaTagihanAttribute()
    {
        return $this->total_tagihan - $this->total_terbayar;
    }
}