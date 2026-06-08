@extends('layouts.app')
@section('title','Laporan Pembayaran')
@section('page-title','Laporan Pembayaran')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div class="flex gap-2">
        <a href="{{ route('reports.payments') }}" class="btn btn-primary btn-sm">Pembayaran</a>
        <a href="{{ route('reports.rooms') }}" class="btn btn-secondary btn-sm">Hunian Kamar</a>
    </div>
    <a href="{{ route('reports.payments.pdf') }}?{{ http_build_query(request()->all()) }}" target="_blank" class="btn btn-danger btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export PDF
    </a>
</div>

<div class="card mb-5">
    <form method="GET" class="p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="start_date" class="form-input" value="{{ request('start_date') }}">
        </div>
        <div>
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-input" value="{{ request('end_date') }}">
        </div>
        <button type="submit" class="btn btn-primary">Tampilkan</button>
        <a href="{{ route('reports.payments') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
    <div class="stat-card">
        <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Total Transaksi</p>
        <p class="text-2xl font-extrabold text-slate-800">{{ $invoices->count() }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Total Pendapatan</p>
        <p class="text-2xl font-extrabold text-green-600">Rp {{ number_format($total,0,',','.') }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Rata-rata/Invoice</p>
        <p class="text-2xl font-extrabold text-slate-800">Rp {{ $invoices->count() ? number_format($total/$invoices->count(),0,',','.') : '0' }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Periode</p>
        <p class="text-sm font-bold text-slate-700">{{ request('start_date','Semua') }}</p>
        <p class="text-xs text-slate-400">s/d {{ request('end_date','Sekarang') }}</p>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="card-header"><h3 class="font-semibold text-slate-700">Detail Pembayaran Lunas</h3></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>No. Invoice</th><th>Penyewa</th><th>Kamar</th><th>Periode</th><th>Tgl Lunas</th><th class="text-right">Total</th></tr></thead>
            <tbody>
                @forelse($invoices as $i => $inv)
                <tr>
                    <td class="text-slate-400">{{ $i+1 }}</td>
                    <td><a href="{{ route('invoices.show', $inv) }}" class="text-sky-kos hover:underline font-medium">{{ $inv->invoice_number }}</a></td>
                    <td class="font-medium text-slate-700">{{ $inv->tenant?->full_name }}</td>
                    <td class="text-slate-500">{{ $inv->rental?->room?->room_number }}</td>
                    <td>{{ $inv->period_label }}</td>
                    <td class="text-slate-500 text-sm">{{ $inv->paid_date?->format('d/m/Y') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($inv->total,0,',','.') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-12 text-slate-400">Tidak ada data pembayaran</td></tr>
                @endforelse
            </tbody>
            @if($invoices->count())
            <tfoot>
                <tr class="border-t-2 border-slate-200">
                    <td colspan="6" class="py-3 px-4 font-bold text-slate-800">Total</td>
                    <td class="py-3 px-4 text-right font-extrabold text-slate-900">Rp {{ number_format($total,0,',','.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
