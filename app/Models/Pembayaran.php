<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'tagihan_id',
        'jumlah_bayar',
        'snap_token',
        'kode_transaksi',
        'status_transaksi',
        'detail_pembayaran'
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'detail_pembayaran' => 'json'
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }
}