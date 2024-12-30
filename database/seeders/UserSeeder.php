<?php
namespace Database\Seeders;
use App\Models\User;
use App\Models\Jurusan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'nit' => 'ADMIN001',
            'alamat' => 'Jl. Admin No. 1',
            'no_telepon' => '08123456789',
            'tahun_masuk' => 2024,
            'kelas' => 1,
            'jurusan_id' => 1,
            'status_siswa' => 'aktif',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // 5 Siswa
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Siswa $i",
                'email' => "siswa$i@gmail.com",
                'nit' => "2024" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'alamat' => "Jl. Siswa No. $i",
                'no_telepon' => "08" . rand(100000000, 999999999),
                'tahun_masuk' => 2024,
                'kelas' => 11,
                'jurusan_id' => 1,
                'status_siswa' => 'aktif',
                'role' => 'siswa',
                'password' => Hash::make('password'),
            ]);
        }
    }
}