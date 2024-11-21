<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'nisn' => 'ADMIN001',
            'alamat' => 'Jl. Admin No. 1',
            'no_telepon' => '08123456789',
            'tahun_masuk' => 2024,
            'kelas' => 1,
            'status_siswa' => 'aktif',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
        User::create([
            'name' => 'Rizqi',
            'email' => 'semuamana@gmail.com',
            'nisn' => 'ADMIN002',
            'alamat' => 'Jl. Admin No. 1',
            'no_telepon' => '08123456831',
            'tahun_masuk' => 2024,
            'kelas' => 1,
            'status_siswa' => 'aktif',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Siswa $i",
                'email' => "siswa$i@gmail.com",
                'nisn' => "2024" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'alamat' => "Jl. Siswa No. $i",
                'no_telepon' => "08" . rand(100000000, 999999999),
                'tahun_masuk' => 2024,
                'kelas' => 1,
                'status_siswa' => 'aktif',
                'role' => 'siswa',
                'password' => Hash::make('password'),
            ]);
        }
    }
}