<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WilayahFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_wilayah' => $this->faker->city(),
            'status_wilayah' => $this->faker->randomElement(['aman', 'siaga', 'bahaya']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
