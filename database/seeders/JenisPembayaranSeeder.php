<?php

namespace Database\Seeders;

use App\Models\JenisPembayaran;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JenisPembayaranSeeder extends Seeder
{
    public function run(): void
    {
        $jenisPembayaran = [
            [
                'nama' => 'SPP',
                'keterangan' => 'Biaya SPP per semester',
                'nominal' => 5000000,
                'dapat_dicicil' => true,
            ],
            [
                'nama' => 'Uang Gedung',
                'keterangan' => 'Biaya pembangunan dan pemeliharaan gedung',
                'nominal' => 15000000,
                'dapat_dicicil' => true,
            ],
            [
                'nama' => 'Biaya Lab',
                'keterangan' => 'Biaya penggunaan laboratorium',
                'nominal' => 1000000,
                'dapat_dicicil' => false,
            ],
        ];

        foreach ($jenisPembayaran as $jp) {
            JenisPembayaran::create($jp);
        }
    }
}
