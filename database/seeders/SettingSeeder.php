<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Informasi Kos
            ['key' => 'app_name',      'value' => 'SiManKos',              'type' => 'string',  'group' => 'general',      'description' => 'Nama aplikasi'],
            ['key' => 'kos_name',      'value' => 'Kos Skyline Residence', 'type' => 'string',  'group' => 'general',      'description' => 'Nama kos'],
            ['key' => 'kos_address',   'value' => 'Yogyakarta, DIY',       'type' => 'string',  'group' => 'general',      'description' => 'Alamat lengkap kos'],
            ['key' => 'kos_phone',     'value' => '08123456789',           'type' => 'string',  'group' => 'general',      'description' => 'Nomor telepon kos'],
            ['key' => 'kos_maps_link', 'value' => '',                      'type' => 'string',  'group' => 'general',      'description' => 'Link Google Maps kos'],
            // Pengingat Pembayaran
            ['key' => 'reminder_h7',      'value' => '1', 'type' => 'boolean', 'group' => 'notification', 'description' => 'Kirim reminder H-7'],
            ['key' => 'reminder_h3',      'value' => '1', 'type' => 'boolean', 'group' => 'notification', 'description' => 'Kirim reminder H-3'],
            ['key' => 'reminder_h1',      'value' => '1', 'type' => 'boolean', 'group' => 'notification', 'description' => 'Kirim reminder H-1'],
            ['key' => 'reminder_overdue', 'value' => '1', 'type' => 'boolean', 'group' => 'notification', 'description' => 'Kirim reminder overdue'],
            // API
            ['key' => 'fonnte_token', 'value' => '', 'type' => 'string', 'group' => 'fonnte', 'description' => 'Token API Fonnte WhatsApp'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
