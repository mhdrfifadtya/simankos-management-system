<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $penyewaRole = Role::firstOrCreate(['name' => 'penghuni', 'guard_name' => 'web']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@simanakos.com'],
            [
                'name' => 'Admin SiManKos',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
                'phone' => '08123456789',
                'whatsapp_number' => '628123456789',
                'status' => 'aktif',
            ]
        );
        $admin->assignRole($adminRole);
    }
}
