<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            FacilitySeeder::class,
            RoomSeeder::class,
            AdminSeeder::class,
            TenantSeeder::class,
            RentalInvoiceSeeder::class,
        ]);
    }
}
