<?php
namespace Database\Seeders;
use App\Models\Jurusan;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $jurusan = [
            ['nama_jurusan' => 'Nautika Kapal Niaga'],
            ['nama_jurusan' => 'Tehnika Kapal Niaga'],
            ['nama_jurusan' => 'Teknik Kendaraan Ringan'],
            ['nama_jurusan' => 'Desain Komunikasi Visual']
        ];

        Jurusan::insert($jurusan);
    }
}