<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTunggakan;
use App\Traits\AutoCalculateSisaTagihan;

class Tagihan extends Model
{
    use HasFactory, HasTunggakan, AutoCalculateSisaTagihan, SoftDeletes;

    protected $table = 'tagihan';

    protected $fillable = [
        'user_id',
        'jenis_pembayaran_id',
        'total_tagihan',
        'total_terbayar',
        'status',
        'tanggal_jatuh_tempo'
    ];

    protected $casts = [
        'total_tagihan' => 'decimal:2',
        'total_terbayar' => 'decimal:2',
        'tanggal_jatuh_tempo' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jenis_pembayaran()
    {
        return $this->belongsTo(JenisPembayaran::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    /**
     * Get sisa tagihan
     */
    public function getSisaTagihanAttribute()
    {
        return $this->total_tagihan - $this->total_terbayar;
    }
}