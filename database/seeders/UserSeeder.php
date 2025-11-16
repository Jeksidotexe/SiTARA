<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create(
            [
                'id_wilayah' => 1,
                'nama' => 'Admin',
                'email' => 'admin@gmail.com',
                'username' => 'admin',
                'alamat' => 'Provinsi Kalimantan Barat',
                'no_telepon' => '081234567890',
                'password' => Hash::make('admin123'),
                'foto' => 'uploads/pengguna/images/default.png',
                'role' => 'Admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }
}
