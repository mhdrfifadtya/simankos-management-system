@extends('layouts.app')
@section('title','Manajemen Pembayaran')
@section('page-title','Manajemen Pembayaran')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Pembayaran</h2>
        <p class="text-sm text-slate-400 mt-0.5">Total {{ $payments->total() }} transaksi</p>
    </div>
    <a href="{{ route('payments.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Input Pembayaran
    </a>
</div>

{{-- Status Tabs --}}
<div class="flex gap-2 mb-5 overflow-x-auto pb-1">
    @foreach([''=> ['Semua','btn-secondary'], 'menunggu_konfirmasi'=>['Menunggu Verifikasi','bg-amber-400 text-amber-900'], 'lunas'=>['Lunas','btn-secondary'], 'ditolak'=>['Ditolak','btn-secondary']] as $s => $cfg)
    <a href="{{ route('payments.index', array_merge(request()->except('status','page'), $s ? ['status'=>$s] : [])) }}"
        class="btn btn-sm whitespace-nowrap {{ request('status','')===$s ? 'btn-primary' : 'btn-secondary' }}">
        {{ $cfg[0] }}
        @if($s === 'menunggu_konfirmasi')
            @php $pendingCount = \App\Models\Payment::where('status','menunggu_konfirmasi')->count(); @endphp
            @if($pendingCount > 0)
            <span class="ml-1 bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">{{ $pendingCount }}</span>
            @endif
        @endif
    </a>
    @endforeach
</div>

<div class="card mb-5">
    <form method="GET" class="p-4 flex flex-wrap gap-3 items-end">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <div class="flex-1 min-w-48">
            <label class="form-label">Cari Penyewa</label>
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" class="form-input pl-9" placeholder="Nama penyewa..." value="{{ request('search') }}">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Cari</button>
        @if(request('search'))
        <a href="{{ route('payments.index', request()->except('search','page')) }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>
</div>

<div class="card overflow-hidden">
    <div class="table-wrap">
        <table>
            <thead><tr>
                <th>Penyewa</th><th>Invoice</th><th>Kamar</th><th>Tgl Bayar</th><th>Metode</th><th class="text-right">Jumlah</th><th class="text-center">Status</th><th class="text-center">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-sky-kos/10 flex items-center justify-center text-xs font-bold text-sky-dark flex-shrink-0">
                                {{ strtoupper(substr($payment->tenant?->full_name??'P',0,1)) }}
                            </div>
                            <span class="font-medium text-sm text-slate-800">{{ $payment->tenant?->full_name }}</span>
                        </div>
                    </td>
                    <td><a href="{{ route('invoices.show', $payment->invoice_id) }}" class="text-sky-kos text-xs hover:underline">{{ $payment->invoice?->invoice_number }}</a></td>
                    <td class="text-slate-500 text-sm">{{ $payment->room?->room_number }}</td>
                    <td class="text-slate-500 text-sm">{{ $payment->payment_date?->format('d/m/Y') }}</td>
                    <td class="capitalize text-slate-500 text-sm">{{ $payment->payment_method ?? '—' }}</td>
                    <td class="text-right font-bold text-slate-800">Rp {{ number_format($payment->amount,0,',','.') }}</td>
                    <td class="text-center">
                        @php $c=['lunas'=>'badge-green','ditolak'=>'badge-red','menunggu_konfirmasi'=>'badge-yellow','pending'=>'badge-gray']; @endphp
                        <span class="badge {{ $c[$payment->status]??'badge-gray' }}">{{ str_replace('_',' ',$payment->status) }}</span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('payments.show', $payment) }}" class="btn btn-secondary btn-sm">Detail</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-14 text-slate-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Belum ada data pembayaran
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-slate-50">{{ $payments->withQueryString()->links() }}</div>
</div>
@endsection
