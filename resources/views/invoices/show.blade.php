@extends('layouts.app')
@section('title','Invoice '.$invoice->invoice_number)
@section('page-title','Detail Invoice')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
    <div class="flex-1"></div>
    <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-secondary btn-sm" target="_blank">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download PDF
    </a>
    <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-primary btn-sm" @if($invoice->status==='paid') style="pointer-events:none;opacity:.5" @endif>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Input Pembayaran
    </a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
    <div class="xl:col-span-2 space-y-5">
        {{-- Invoice Header --}}
        <div class="card overflow-hidden">
            @php $statusColors=['paid'=>'from-green-500 to-emerald-600','overdue'=>'from-red-500 to-rose-600','pending'=>'from-amber-400 to-yellow-500','sent'=>'from-sky-kos to-sky-dark','draft'=>'from-slate-400 to-slate-500','partial'=>'from-orange-400 to-orange-500']; @endphp
            <div class="bg-gradient-to-r {{ $statusColors[$invoice->status]??'from-slate-400 to-slate-500' }} px-6 py-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-white/70 text-sm font-medium">Nomor Invoice</p>
                        <p class="text-white text-2xl font-extrabold mt-1 tracking-tight">{{ $invoice->invoice_number }}</p>
                        <p class="text-white/70 text-sm mt-1">Periode: {{ $invoice->period_label }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-3 py-1.5 bg-white/20 text-white text-sm font-bold rounded-lg uppercase">{{ $invoice->status }}</span>
                        <p class="text-white/70 text-xs mt-2">Dibuat {{ $invoice->created_at?->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-slate-400 font-semibold uppercase mb-2">Ditagihkan Kepada</p>
                        <p class="font-bold text-slate-800">{{ $invoice->tenant?->full_name }}</p>
                        <p class="text-sm text-slate-500 mt-0.5">{{ $invoice->tenant?->whatsapp_number }}</p>
                        <p class="text-sm text-slate-500">Kamar {{ $invoice->rental?->room?->room_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold uppercase mb-2">Informasi Pembayaran</p>
                        <div class="space-y-1">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Jatuh Tempo</span>
                                <span class="font-semibold text-slate-700">{{ $invoice->due_date?->format('d M Y') }}</span>
                            </div>
                            @if($invoice->paid_date)
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Tgl Lunas</span>
                                <span class="font-semibold text-green-600">{{ $invoice->paid_date?->format('d M Y') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-6 border-t border-slate-50 pt-5">
                    <table class="w-full">
                        <thead><tr>
                            <th class="text-left text-xs text-slate-400 font-semibold uppercase pb-3">Keterangan</th>
                            <th class="text-right text-xs text-slate-400 font-semibold uppercase pb-3">Jumlah</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr>
                                <td class="py-3 text-sm text-slate-700">Sewa Kamar — {{ $invoice->period_label }}</td>
                                <td class="py-3 text-right text-sm font-semibold">Rp {{ number_format($invoice->rent_amount,0,',','.') }}</td>
                            </tr>
                            @if($invoice->extra_charges > 0)
                            <tr>
                                <td class="py-3 text-sm text-slate-700">Biaya Tambahan</td>
                                <td class="py-3 text-right text-sm font-semibold">Rp {{ number_format($invoice->extra_charges,0,',','.') }}</td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-slate-200">
                                <td class="pt-3 font-bold text-slate-800">Total Tagihan</td>
                                <td class="pt-3 text-right font-extrabold text-xl text-slate-900">Rp {{ number_format($invoice->total,0,',','.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Payments --}}
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="font-semibold text-slate-700">Riwayat Pembayaran</h3>
                <span class="text-xs text-slate-400">{{ $invoice->payments->count() }} transaksi</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Tanggal</th><th>Metode</th><th>Verifikasi</th><th class="text-right">Jumlah</th><th class="text-center">Status</th><th>Bukti</th></tr></thead>
                    <tbody>
                        @forelse($invoice->payments as $pay)
                        <tr>
                            <td>{{ $pay->payment_date?->format('d M Y') }}</td>
                            <td class="capitalize text-slate-500">{{ $pay->payment_method ?? '—' }}</td>
                            <td class="text-slate-500">{{ $pay->verifiedBy?->name ?? '—' }}</td>
                            <td class="text-right font-semibold">Rp {{ number_format($pay->amount,0,',','.') }}</td>
                            <td class="text-center">
                                @php $c=['lunas'=>'badge-green','ditolak'=>'badge-red','menunggu_konfirmasi'=>'badge-yellow','pending'=>'badge-gray']; @endphp
                                <span class="badge {{ $c[$pay->status]??'badge-gray' }} text-xs">{{ str_replace('_',' ',$pay->status) }}</span>
                            </td>
                            <td>
                                @if($pay->proof_of_payment)
                                <a href="{{ $pay->proof_url ?? asset('storage/'.$pay->proof_of_payment) }}" target="_blank" class="text-sky-kos hover:underline text-xs">Lihat</a>
                                @else —
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-8 text-slate-400">Belum ada pembayaran</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">
        {{-- Status Update --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Update Status</h3></div>
            <div class="card-body">
                <form action="{{ route('invoices.update-status', $invoice) }}" method="POST">
                    @csrf @method('PATCH')
                    <select name="status" class="form-input mb-3">
                        @foreach(['draft','sent','pending','partial','paid','overdue'] as $s)
                        <option value="{{ $s }}" {{ $invoice->status===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-secondary w-full btn-sm">Perbarui Status</button>
                </form>
            </div>
        </div>

        {{-- Room & Rental Info --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Info Kamar</h3></div>
            <div class="card-body space-y-3">
                <div>
                    <p class="text-xs text-slate-400">Kamar</p>
                    <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ $invoice->rental?->room?->room_number }} ({{ $invoice->rental?->room?->type }})</p>
                </div>
                <a href="{{ route('rentals.show', $invoice->rental_id) }}" class="btn btn-secondary w-full btn-sm mt-2">Lihat Kontrak</a>
            </div>
        </div>
    </div>
</div>
@endsection
