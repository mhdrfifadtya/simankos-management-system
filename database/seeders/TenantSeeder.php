<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = [
            ['name' => 'Budi Santoso', 'email' => 'penyewa1@simanakos.com', 'wa' => '628111000001', 'nik' => '3404011234560001'],
            ['name' => 'Siti Rahayu', 'email' => 'penyewa2@simanakos.com', 'wa' => '628111000002', 'nik' => '3404011234560002'],
            ['name' => 'Ahmad Fauzi', 'email' => 'penyewa3@simanakos.com', 'wa' => '628111000003', 'nik' => '3404011234560003'],
            ['name' => 'Dewi Lestari', 'email' => 'penyewa4@simanakos.com', 'wa' => '628111000004', 'nik' => '3404011234560004'],
            ['name' => 'Eko Prasetyo', 'email' => 'penyewa5@simanakos.com', 'wa' => '628111000005', 'nik' => '3404011234560005'],
            ['name' => 'Fitri Handayani', 'email' => 'penyewa6@simanakos.com', 'wa' => '628111000006', 'nik' => '3404011234560006'],
        ];

        foreach ($tenants as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('Penyewa@123'),
                    'role' => 'penghuni',
                    'phone' => $data['wa'],
                    'whatsapp_number' => $data['wa'],
                    'status' => 'aktif',
                ]
            );

            $user->assignRole('penghuni');

            Tenant::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'full_name' => $data['name'],
                    'nik' => $data['nik'],
                    'whatsapp_number' => $data['wa'],
                    'address' => 'Yogyakarta, DIY',
                    'emergency_contact' => 'Keluarga ' . $data['name'],
                    'emergency_phone' => '628100000000',
                    'notify_via_whatsapp' => true,
                    'notify_via_email' => false,
                    'status' => 'aktif',
                ]
            );
        }
    }
}
