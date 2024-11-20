<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisPembayaran extends Model
{
    use SoftDeletes;

    protected $table = 'jenis_pembayaran';

    protected $fillable = [
        'nama',
        'keterangan',
        'nominal',
        'dapat_dicicil',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'dapat_dicicil' => 'boolean',
    ];

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }
}
