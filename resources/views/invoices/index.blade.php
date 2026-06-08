@extends('layouts.app')
@section('title','Manajemen Invoice')
@section('page-title','Manajemen Invoice')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Tagihan (Invoice)</h2>
        <p class="text-sm text-slate-400 mt-0.5">Total {{ $invoices->total() }} invoice</p>
    </div>
    <a href="{{ route('payments.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Input Pembayaran
    </a>
</div>

{{-- Status Tabs --}}
<div class="flex gap-2 mb-4 overflow-x-auto pb-1">
    @foreach([''=> 'Semua', 'paid'=>'Lunas', 'pending'=>'Pending', 'overdue'=>'Overdue', 'sent'=>'Terkirim', 'draft'=>'Draft'] as $s => $label)
    <a href="{{ route('invoices.index', array_merge(request()->except('status','page'), $s ? ['status'=>$s] : [])) }}"
        class="btn btn-sm whitespace-nowrap {{ request('status','')===$s ? 'btn-primary' : 'btn-secondary' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="card mb-5">
    <form method="GET" class="p-4 flex flex-wrap gap-3 items-end">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <div>
            <label class="form-label">Bulan</label>
            <select name="month" class="form-input">
                <option value="">Semua Bulan</option>
                @foreach(range(1,12) as $m)
                <option value="{{ $m }}" {{ request('month')==$m?'selected':'' }}>{{ \Carbon\Carbon::create()->month($m)->locale('id')->monthName }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Tahun</label>
            <select name="year" class="form-input">
                <option value="">Semua</option>
                @foreach(range(now()->year, 2024) as $y)
                <option value="{{ $y }}" {{ request('year')==$y?'selected':'' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Filter</button>
        @if(request()->hasAny(['month','year']))
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>
</div>

<div class="card overflow-hidden">
    <div class="table-wrap">
        <table>
            <thead><tr>
                <th>No. Invoice</th><th>Penyewa</th><th>Kamar</th><th>Periode</th><th>Jatuh Tempo</th><th class="text-right">Total</th><th class="text-center">Status</th><th class="text-center">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td><a href="{{ route('invoices.show', $invoice) }}" class="text-sky-kos hover:underline font-semibold">{{ $invoice->invoice_number }}</a></td>
                    <td>
                        <p class="font-medium text-slate-700">{{ $invoice->tenant?->full_name }}</p>
                    </td>
                    <td class="text-slate-500">{{ $invoice->rental?->room?->room_number }}</td>
                    <td class="text-slate-500 text-sm">{{ $invoice->period_label }}</td>
                    <td>
                        @php $daysLeft = now()->diffInDays($invoice->due_date, false); @endphp
                        <p class="text-sm {{ $daysLeft < 0 ? 'text-red-600' : ($daysLeft<=3?'text-amber-600':'text-slate-600') }}">
                            {{ $invoice->due_date?->format('d/m/Y') }}
                        </p>
                    </td>
                    <td class="text-right font-bold text-slate-800">Rp {{ number_format($invoice->total,0,',','.') }}</td>
                    <td class="text-center">
                        @php $c=['paid'=>'badge-green','overdue'=>'badge-red','pending'=>'badge-yellow','partial'=>'badge-orange','sent'=>'badge-sky','draft'=>'badge-gray']; @endphp
                        <span class="badge {{ $c[$invoice->status]??'badge-gray' }}">{{ $invoice->status }}</span>
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary btn-sm" title="Detail">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-secondary btn-sm" title="PDF" target="_blank">
                                <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </a>
                            @if($invoice->status !== 'paid')
                            <a href="{{ route('payments.create', ['invoice_id'=>$invoice->id]) }}" class="btn btn-primary btn-sm" title="Bayar">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-14 text-slate-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Tidak ada invoice ditemukan
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-slate-50">{{ $invoices->withQueryString()->links() }}</div>
</div>
@endsection
