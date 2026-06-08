<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class TenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('tenant')?->user_id;

        return [
            // Data user (untuk tabel users)
            'full_name'           => ['required', 'string', 'max:255'],
            'email'               => [
                'required', 'email',
                'unique:users,email' . ($userId ? ",{$userId}" : ''),
            ],
            'password'            => ['nullable', 'string', 'min:8'],

            // Data tenant (untuk tabel tenants)
            'nik'                 => ['nullable', 'string', 'max:20'],
            'whatsapp_number'     => ['required', 'string', 'max:20'],
            'address'             => ['nullable', 'string'],
            'emergency_contact'   => ['nullable', 'string', 'max:255'],
            'emergency_phone'     => ['nullable', 'string', 'max:20'],

            // Checkbox — pakai nullable boolean, default false jika tidak tercentang
            'notify_via_whatsapp' => ['nullable', 'boolean'],
            'notify_via_email'    => ['nullable', 'boolean'],

            // Upload foto — nullable, hanya gambar, max 2MB
            'id_card_photo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'profile_photo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'notes'               => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'       => 'Nama lengkap wajib diisi.',
            'email.required'           => 'Email wajib diisi.',
            'email.unique'             => 'Email sudah digunakan.',
            'whatsapp_number.required' => 'Nomor WhatsApp wajib diisi.',
            'id_card_photo.image'      => 'File KTP harus berupa gambar (jpg/png).',
            'id_card_photo.max'        => 'Ukuran foto KTP maksimal 2MB.',
            'profile_photo.image'      => 'File foto profil harus berupa gambar (jpg/png).',
            'profile_photo.max'        => 'Ukuran foto profil maksimal 2MB.',
        ];
    }
}
