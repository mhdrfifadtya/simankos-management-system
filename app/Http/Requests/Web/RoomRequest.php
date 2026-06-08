<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_number'    => ['required', 'string', 'max:20'],
            'type'           => ['required', 'in:standard,deluxe,vip'],
            'price'          => ['required', 'numeric', 'min:0'],
            'area'           => ['nullable', 'numeric', 'min:0'],
            'floor'          => ['nullable', 'integer', 'min:0'],
            'status'         => ['required', 'in:tersedia,terisi,maintenance'],
            'description'    => ['nullable', 'string'],
            'facility_ids'   => ['nullable', 'array'],
            'facility_ids.*' => ['integer', 'exists:facilities,id'],
            'photos'         => ['nullable', 'array'],
            'photos.*'       => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'room_number.required' => 'Nomor kamar wajib diisi.',
            'price.required'       => 'Harga sewa wajib diisi.',
            'photos.*.image'       => 'Setiap file foto harus berupa gambar (jpg/png).',
            'photos.*.max'         => 'Ukuran setiap foto maksimal 2MB.',
        ];
    }
}
