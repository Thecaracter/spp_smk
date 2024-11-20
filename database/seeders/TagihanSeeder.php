<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tagihan;
use App\Models\JenisPembayaran;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TagihanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mahasiswa = User::where('role', 'mahasiswa')->get();
        $jenisPembayaran = JenisPembayaran::all();

        foreach ($mahasiswa as $mhs) {
            foreach ($jenisPembayaran as $jp) {
                Tagihan::create([
                    'user_id' => $mhs->id,
                    'jenis_pembayaran_id' => $jp->id,
                    'semester' => '1',
                    'tahun_ajaran' => '2024/2025',
                    'jumlah_tagihan' => $jp->nominal,
                    'jumlah_terbayar' => 0,
                    'sisa_tagihan' => $jp->nominal,
                    'cicilan_ke' => $jp->dapat_dicicil ? 1 : null,
                    'total_cicilan' => $jp->dapat_dicicil ? 6 : null,
                    'status' => 'belum_bayar',
                ]);
            }
        }
    }
}
