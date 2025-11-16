<?php

namespace Database\Seeders;

use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Wilayah::factory()->create([
            'nama_wilayah' => 'Provinsi Kalimantan Barat',
            'status_wilayah' => 'aman',
        ]);
    }
}
