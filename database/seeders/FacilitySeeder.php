<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [
            ['name' => 'AC', 'icon' => 'ac'],
            ['name' => 'WiFi', 'icon' => 'wifi'],
            ['name' => 'Kamar Mandi Dalam', 'icon' => 'bathroom'],
            ['name' => 'Kasur', 'icon' => 'bed'],
            ['name' => 'Lemari', 'icon' => 'wardrobe'],
            ['name' => 'Meja Belajar', 'icon' => 'desk'],
            ['name' => 'Kursi', 'icon' => 'chair'],
            ['name' => 'TV', 'icon' => 'tv'],
        ];

        foreach ($facilities as $f) {
            Facility::firstOrCreate(['name' => $f['name']], $f);
        }
    }
}
