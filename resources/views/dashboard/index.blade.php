@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')
{{-- Stats Row --}}
<div class="grid grid-cols-2 xl:grid-cols-6 gap-4 mb-6">
    <div class="stat-card xl:col-span-1">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-slate-100">
                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-slate-800">{{ $stats['total_rooms'] }}</p>
        <p class="text-xs text-slate-400 mt-0.5">Total Kamar</p>
    </div>
    <div class="stat-card xl:col-span-1">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-blue-50">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-blue-600">{{ $stats['occupied_rooms'] }}</p>
        <p class="text-xs text-slate-400 mt-0.5">Kamar Terisi</p>
    </div>
    <div class="stat-card xl:col-span-1">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-green-50">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-green-600">{{ $stats['available_rooms'] }}</p>
        <p class="text-xs text-slate-400 mt-0.5">Kamar Tersedia</p>
    </div>
    <div class="stat-card xl:col-span-1">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-indigo-50">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-indigo-600">{{ $stats['active_tenants'] }}</p>
        <p class="text-xs text-slate-400 mt-0.5">Penyewa Aktif</p>
    </div>
    <div class="stat-card xl:col-span-1">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-amber-50">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-amber-600">{{ $stats['pending_invoices'] }}</p>
        <p class="text-xs text-slate-400 mt-0.5">Invoice Belum Lunas</p>
    </div>
    <div class="stat-card xl:col-span-1 bg-gradient-to-br from-[#0F172A] to-[#1E293B] border-0">
        <div class="stat-icon bg-sky-kos/20 mb-3">
            <svg class="w-5 h-5 text-sky-kos" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-2xl font-extrabold text-white">Rp {{ number_format($stats['monthly_revenue']/1000000,1) }}jt</p>
        <p class="text-xs text-slate-400 mt-0.5">Pendapatan Bulan Ini</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">
    {{-- Revenue Chart --}}
    <div class="card p-5 xl:col-span-2">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-slate-800">Grafik Pendapatan</h3>
                <p class="text-xs text-slate-400 mt-0.5">6 bulan terakhir</p>
            </div>
            <span class="badge badge-sky">{{ now()->locale('id')->monthName }}</span>
        </div>
        <canvas id="revenueChart" height="110"></canvas>
    </div>

    {{-- Pending Payments --}}
    <div class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Pembayaran Pending</h3>
            <a href="{{ route('payments.index', ['status'=>'menunggu_konfirmasi']) }}" class="text-sky-kos text-xs hover:underline">Semua →</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($pendingPayments->take(6) as $payment)
            <div class="px-5 py-3 flex items-center gap-3 hover:bg-slate-50/50 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $payment->tenant?->full_name }}</p>
                    <p class="text-xs text-slate-400">Rp {{ number_format($payment->amount,0,',','.') }}</p>
                </div>
                <a href="{{ route('payments.show', $payment) }}" class="btn btn-primary btn-sm py-1">Cek</a>
            </div>
            @empty
            <div class="px-5 py-10 text-center">
                <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-slate-400 text-sm">Semua pembayaran sudah diverifikasi</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Due Soon Table --}}
<div class="card overflow-hidden">
    <div class="card-header flex items-center justify-between">
        <div>
            <h3 class="font-bold text-slate-800">Tagihan Jatuh Tempo (7 Hari ke Depan)</h3>
            <p class="text-xs text-slate-400 mt-0.5">{{ $dueSoonInvoices->count() }} invoice perlu perhatian</p>
        </div>
        <a href="{{ route('invoices.index') }}" class="text-sky-kos text-xs hover:underline">Lihat semua →</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr>
                <th>Penyewa</th><th>No. Invoice</th><th>Kamar</th><th>Jatuh Tempo</th><th class="text-right">Total</th><th class="text-center">Status</th><th class="text-center">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($dueSoonInvoices as $invoice)
                <tr>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-sky-kos/10 flex items-center justify-center text-xs font-bold text-sky-dark flex-shrink-0">
                                {{ strtoupper(substr($invoice->tenant?->full_name??'P',0,1)) }}
                            </div>
                            <span class="font-medium text-slate-700">{{ $invoice->tenant?->full_name }}</span>
                        </div>
                    </td>
                    <td><a href="{{ route('invoices.show', $invoice) }}" class="text-sky-kos hover:underline font-medium text-sm">{{ $invoice->invoice_number }}</a></td>
                    <td class="text-slate-500 text-sm">{{ $invoice->rental?->room?->room_number }}</td>
                    <td>
                        @php $daysLeft = now()->diffInDays($invoice->due_date, false); @endphp
                        <span class="{{ $daysLeft <= 1 ? 'text-red-600' : ($daysLeft <= 3 ? 'text-amber-600' : 'text-slate-600') }} font-medium text-sm">
                            {{ $invoice->due_date?->format('d M Y') }}
                        </span>
                        <p class="text-xs {{ $daysLeft <= 1 ? 'text-red-400' : 'text-slate-400' }}">{{ $daysLeft }} hari lagi</p>
                    </td>
                    <td class="text-right font-bold">Rp {{ number_format($invoice->total,0,',','.') }}</td>
                    <td class="text-center">
                        @php $c=['paid'=>'badge-green','overdue'=>'badge-red','pending'=>'badge-yellow','sent'=>'badge-sky','draft'=>'badge-gray']; @endphp
                        <span class="badge {{ $c[$invoice->status]??'badge-gray' }}">{{ $invoice->status }}</span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('payments.create', ['invoice_id'=>$invoice->id]) }}" class="btn btn-primary btn-sm">Bayar</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-12 text-slate-400">
                    <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Tidak ada tagihan jatuh tempo dalam 7 hari ke depan
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($chartData['labels']),
        datasets: [{
            label: 'Pendapatan',
            data: @json($chartData['data']),
            backgroundColor: 'rgba(56,189,248,0.15)',
            borderColor: '#38BDF8',
            borderWidth: 2,
            borderRadius: 8,
            hoverBackgroundColor: 'rgba(56,189,248,0.25)',
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => 'Rp ' + Number(ctx.raw).toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#F8FAFC' },
                ticks: {
                    color: '#94A3B8',
                    font: { size: 11 },
                    callback: v => v >= 1e6 ? 'Rp ' + (v/1e6).toFixed(1) + 'jt' : 'Rp ' + v.toLocaleString('id-ID')
                }
            },
            x: {
                grid: { display: false },
                ticks: { color: '#94A3B8', font: { size: 11 } }
            }
        }
    }
});
</script>
@endpush
