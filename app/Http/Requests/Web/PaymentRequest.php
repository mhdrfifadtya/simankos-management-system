<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id'       => ['required', 'exists:invoices,id'],
            'amount'           => ['required', 'numeric', 'min:1000'],
            'payment_date'     => ['required', 'date'],
            'payment_method'   => ['required', 'string', 'max:50'],
            'proof_of_payment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:2048'],
            'notes'            => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_id.required'     => 'Pilih invoice yang akan dibayar.',
            'amount.required'         => 'Jumlah pembayaran wajib diisi.',
            'amount.min'              => 'Jumlah minimal Rp 1.000.',
            'payment_date.required'   => 'Tanggal pembayaran wajib diisi.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'proof_of_payment.file'   => 'File bukti pembayaran tidak valid.',
            'proof_of_payment.mimes'  => 'Bukti pembayaran harus berupa gambar atau PDF.',
            'proof_of_payment.max'    => 'Ukuran file maksimal 2MB.',
        ];
    }
}
