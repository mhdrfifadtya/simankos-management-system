<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            ['room_number' => 'A01', 'type' => 'standard', 'price' => 800000,  'area' => 3.0, 'floor' => 1],
            ['room_number' => 'A02', 'type' => 'standard', 'price' => 800000,  'area' => 3.0, 'floor' => 1],
            ['room_number' => 'A03', 'type' => 'standard', 'price' => 800000,  'area' => 3.0, 'floor' => 1],
            ['room_number' => 'B01', 'type' => 'deluxe',   'price' => 1200000, 'area' => 4.0, 'floor' => 2],
            ['room_number' => 'B02', 'type' => 'deluxe',   'price' => 1200000, 'area' => 4.0, 'floor' => 2],
            ['room_number' => 'B03', 'type' => 'deluxe',   'price' => 1200000, 'area' => 4.0, 'floor' => 2],
            ['room_number' => 'C01', 'type' => 'vip',      'price' => 1800000, 'area' => 6.0, 'floor' => 3],
            ['room_number' => 'C02', 'type' => 'vip',      'price' => 1800000, 'area' => 6.0, 'floor' => 3],
        ];

        $facilityMap = [
            'standard' => ['Kasur', 'Lemari', 'Meja Belajar', 'Kursi'],
            'deluxe'   => ['WiFi', 'Kamar Mandi Dalam', 'Kasur', 'Lemari', 'Meja Belajar', 'Kursi'],
            'vip'      => ['AC', 'WiFi', 'Kamar Mandi Dalam', 'Kasur', 'Lemari', 'Meja Belajar', 'Kursi', 'TV'],
        ];

        $allFacilities = Facility::all()->keyBy('name');

        foreach ($rooms as $data) {
            $room = Room::firstOrCreate(
                ['room_number' => $data['room_number']],
                array_merge($data, ['status' => 'tersedia'])
            );

            $names = $facilityMap[$data['type']] ?? [];
            $ids   = collect($names)
                ->map(fn($n) => $allFacilities->get($n)?->id)
                ->filter()
                ->values()
                ->all();

            $room->facilities()->sync($ids);
        }
    }
}
