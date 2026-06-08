@extends('layouts.app')
@section('title','Detail Pembayaran')
@section('page-title','Detail Pembayaran')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('payments.index') }}" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
    <div class="flex-1"></div>
    @if($payment->status === 'menunggu_konfirmasi')
    <form action="{{ route('payments.verify', $payment) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Verifikasi pembayaran ini?')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Konfirmasi Lunas
        </button>
    </form>
    @endif
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
    <div class="xl:col-span-2 space-y-5">
        <div class="card">
            <div class="card-body">
                @php $c=['lunas'=>'from-green-500 to-emerald-600','ditolak'=>'from-red-500 to-rose-600','menunggu_konfirmasi'=>'from-amber-400 to-yellow-500','pending'=>'from-slate-400 to-slate-500']; @endphp
                <div class="bg-gradient-to-r {{ $c[$payment->status]??'from-slate-400 to-slate-500' }} rounded-xl px-5 py-4 mb-5">
                    <p class="text-white/70 text-sm">Status Pembayaran</p>
                    <p class="text-white text-xl font-extrabold mt-1 uppercase">{{ str_replace('_',' ',$payment->status) }}</p>
                    @if($payment->verified_at)
                    <p class="text-white/70 text-xs mt-1">Diverifikasi: {{ $payment->verified_at->format('d M Y H:i') }}</p>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-semibold">Penyewa</p>
                        <p class="font-semibold text-slate-800 mt-1">{{ $payment->tenant?->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-semibold">Kamar</p>
                        <p class="font-semibold text-slate-800 mt-1">{{ $payment->room?->room_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-semibold">Tanggal Bayar</p>
                        <p class="font-semibold text-slate-800 mt-1">{{ $payment->payment_date?->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-semibold">Metode</p>
                        <p class="font-semibold text-slate-800 mt-1 capitalize">{{ $payment->payment_method ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-semibold">Invoice</p>
                        <a href="{{ route('invoices.show', $payment->invoice_id) }}" class="font-semibold text-sky-kos hover:underline mt-1 block">{{ $payment->invoice?->invoice_number }}</a>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-semibold">Verifikasi Oleh</p>
                        <p class="font-semibold text-slate-800 mt-1">{{ $payment->verifiedBy?->name ?? '—' }}</p>
                    </div>
                    <div class="col-span-2 border-t border-slate-50 pt-4 mt-2">
                        <p class="text-xs text-slate-400 uppercase font-semibold">Jumlah Dibayar</p>
                        <p class="text-3xl font-extrabold text-slate-900 mt-1">Rp {{ number_format($payment->amount,0,',','.') }}</p>
                    </div>
                </div>

                @if($payment->notes)
                <div class="mt-5 pt-5 border-t border-slate-50">
                    <p class="text-xs text-slate-400 uppercase font-semibold mb-2">Catatan</p>
                    <p class="text-sm text-slate-600">{{ $payment->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($payment->proof_of_payment)
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Bukti Pembayaran</h3></div>
            <div class="card-body text-center">
                @php $proofUrl = $payment->proof_url; @endphp
                @if($proofUrl && !str_ends_with(strtolower($payment->proof_of_payment), '.pdf'))
                    <img src="{{ $proofUrl }}" class="max-h-80 rounded-xl mx-auto border border-slate-100" alt="Bukti pembayaran">
                @else
                    <div class="flex items-center justify-center gap-2 p-8 bg-slate-50 rounded-xl">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="text-slate-500 font-medium">File PDF</span>
                    </div>
                @endif
                @if($proofUrl)
                <a href="{{ $proofUrl }}" target="_blank" class="btn btn-secondary btn-sm mt-3 mx-auto inline-flex">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Buka File Bukti
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Action Panel --}}
    <div class="space-y-5">
        @if($payment->status === 'menunggu_konfirmasi')
        <div class="card border-amber-200 bg-amber-50">
            <div class="card-body">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-amber-800">Menunggu Verifikasi</p>
                        <p class="text-xs text-amber-600">Periksa bukti transfer</p>
                    </div>
                </div>
                <form action="{{ route('payments.verify', $payment) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-success w-full" onclick="return confirm('Konfirmasi pembayaran ini sebagai lunas?')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Konfirmasi Lunas
                    </button>
                </form>
                <form action="{{ route('payments.reject', $payment) }}" method="POST" x-data="{ show: false }" @submit.prevent="if(!$el.querySelector('textarea').value.trim()){alert('Masukkan alasan penolakan');return;} $el.submit()">
                    @csrf
                    <button type="button" @click="show=!show" class="btn btn-danger w-full mb-2">Tolak Pembayaran</button>
                    <div x-show="show" x-cloak>
                        <textarea name="reason" class="form-input mb-2" rows="2" placeholder="Alasan penolakan (wajib)..."></textarea>
                        <button type="submit" class="btn btn-danger w-full btn-sm">Konfirmasi Tolak</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Invoice Terkait</h3></div>
            <div class="card-body space-y-3">
                <div>
                    <p class="text-xs text-slate-400">Nomor Invoice</p>
                    <p class="font-semibold text-slate-700 mt-0.5">{{ $payment->invoice?->invoice_number }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Periode</p>
                    <p class="font-semibold text-slate-700 mt-0.5">{{ $payment->invoice?->period_label }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Total Tagihan</p>
                    <p class="font-bold text-slate-800 mt-0.5">Rp {{ number_format($payment->invoice?->total??0,0,',','.') }}</p>
                </div>
                <a href="{{ route('invoices.show', $payment->invoice_id) }}" class="btn btn-secondary w-full btn-sm">Lihat Invoice</a>
            </div>
        </div>
    </div>
</div>
@endsection
