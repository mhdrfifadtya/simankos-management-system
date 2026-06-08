@extends('layouts.app')
@section('title','Detail Penyewa')
@section('page-title','Detail Penyewa')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('tenants.index') }}" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
    <div class="flex-1"></div>
    <a href="{{ route('tenants.edit', $tenant) }}" class="btn btn-secondary btn-sm">Edit</a>
    @if($tenant->status === 'aktif')
    <form action="{{ route('tenants.deactivate', $tenant) }}" method="POST" onsubmit="return confirm('Nonaktifkan penyewa ini?')">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-danger btn-sm">Nonaktifkan</button>
    </form>
    @endif
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
    {{-- Profile Card --}}
    <div class="space-y-5">
        <div class="card">
            <div class="card-body text-center">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-sky-kos to-sky-dark flex items-center justify-center text-white font-extrabold text-3xl mx-auto mb-4">
                    {{ strtoupper(substr($tenant->full_name, 0, 1)) }}
                </div>
                <h2 class="text-xl font-bold text-slate-800">{{ $tenant->full_name }}</h2>
                <p class="text-slate-400 text-sm mt-1">{{ $tenant->user?->email }}</p>
                <div class="mt-3">
                    <span class="badge {{ $tenant->status==='aktif'?'badge-green':'badge-gray' }} text-sm">{{ $tenant->status }}</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Kontak</h3></div>
            <div class="card-body space-y-3">
                <div>
                    <p class="text-xs text-slate-400">WhatsApp</p>
                    <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ $tenant->whatsapp_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">NIK</p>
                    <p class="text-sm font-semibold text-slate-700 mt-0.5 font-mono">{{ $tenant->nik ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Alamat</p>
                    <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ $tenant->address ?? '—' }}</p>
                </div>
                @if($tenant->emergency_contact)
                <div class="pt-3 border-t border-slate-50">
                    <p class="text-xs text-slate-400">Kontak Darurat</p>
                    <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ $tenant->emergency_contact }}</p>
                    <p class="text-xs text-slate-500">{{ $tenant->emergency_phone }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Notifikasi</h3></div>
            <div class="card-body space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600">WhatsApp</span>
                    <span class="badge {{ $tenant->notify_via_whatsapp ? 'badge-green' : 'badge-gray' }}">{{ $tenant->notify_via_whatsapp ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600">Email</span>
                    <span class="badge {{ $tenant->notify_via_email ? 'badge-green' : 'badge-gray' }}">{{ $tenant->notify_via_email ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="xl:col-span-2 space-y-5">
        {{-- Active Rental --}}
        @if($tenant->activeRental)
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="font-semibold text-slate-700">Kontrak Aktif</h3>
                <span class="badge badge-green">Aktif</span>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400">Kamar</p>
                        <p class="font-bold text-slate-800 mt-1">{{ $tenant->activeRental->room?->room_number }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400">Sewa/Bulan</p>
                        <p class="font-bold text-slate-800 mt-1">Rp {{ number_format($tenant->activeRental->monthly_rent,0,',','.') }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400">Mulai</p>
                        <p class="font-bold text-slate-800 mt-1">{{ $tenant->activeRental->start_date?->format('d M Y') }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400">Jatuh Tempo</p>
                        <p class="font-bold text-slate-800 mt-1">Tgl {{ $tenant->activeRental->due_date_day }}</p>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('rentals.show', $tenant->activeRental) }}" class="btn btn-secondary btn-sm">Lihat Kontrak</a>
                    <a href="{{ route('invoices.index', ['tenant_id' => $tenant->id]) }}" class="btn btn-secondary btn-sm">Lihat Invoice</a>
                </div>
            </div>
        </div>
        @endif

        {{-- Invoice History --}}
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="font-semibold text-slate-700">Riwayat Invoice</h3>
                <span class="text-xs text-slate-400">{{ $tenant->invoices->count() }} invoice</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr>
                        <th>No. Invoice</th><th>Periode</th><th>Jatuh Tempo</th><th class="text-right">Total</th><th class="text-center">Status</th>
                    </tr></thead>
                    <tbody>
                        @forelse($tenant->invoices->take(10) as $inv)
                        <tr>
                            <td><a href="{{ route('invoices.show', $inv) }}" class="text-sky-kos hover:underline font-medium">{{ $inv->invoice_number }}</a></td>
                            <td>{{ $inv->period_label }}</td>
                            <td class="text-slate-500">{{ $inv->due_date?->format('d/m/Y') }}</td>
                            <td class="text-right font-semibold">Rp {{ number_format($inv->total,0,',','.') }}</td>
                            <td class="text-center">
                                @php $c=['paid'=>'badge-green','overdue'=>'badge-red','pending'=>'badge-yellow','sent'=>'badge-sky','draft'=>'badge-gray']; @endphp
                                <span class="badge {{ $c[$inv->status]??'badge-gray' }}">{{ $inv->status }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-8 text-slate-400">Belum ada invoice</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Payment History --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Riwayat Pembayaran</h3></div>
            <div class="table-wrap">
                <table>
                    <thead><tr>
                        <th>Tanggal</th><th>Invoice</th><th>Metode</th><th class="text-right">Jumlah</th><th class="text-center">Status</th>
                    </tr></thead>
                    <tbody>
                        @forelse($tenant->payments->take(8) as $pay)
                        <tr>
                            <td>{{ $pay->payment_date?->format('d/m/Y') }}</td>
                            <td class="text-slate-500 text-xs">{{ $pay->invoice?->invoice_number }}</td>
                            <td class="capitalize">{{ $pay->payment_method ?? '—' }}</td>
                            <td class="text-right font-semibold">Rp {{ number_format($pay->amount,0,',','.') }}</td>
                            <td class="text-center">
                                @php $c=['lunas'=>'badge-green','ditolak'=>'badge-red','menunggu_konfirmasi'=>'badge-yellow','pending'=>'badge-gray']; @endphp
                                <span class="badge {{ $c[$pay->status]??'badge-gray' }} text-xs">{{ str_replace('_',' ',$pay->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-8 text-slate-400">Belum ada pembayaran</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Zona Berbahaya --}}
<div class="card mt-5 border-red-200">
    <div class="card-header" style="background:#fef2f2">
        <h3 class="font-semibold text-red-700">Zona Berbahaya</h3>
        <p class="text-xs text-red-400 mt-0.5">Tindakan berikut tidak dapat dibatalkan.</p>
    </div>
    <div class="card-body flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-slate-700">Hapus Penyewa</p>
            @if($tenant->activeRental)
                <p class="text-xs text-slate-400 mt-0.5">Tidak dapat dihapus — penyewa memiliki kontrak aktif. Akhiri kontrak terlebih dahulu.</p>
            @else
                <p class="text-xs text-slate-400 mt-0.5">Menghapus penyewa beserta akun login dan seluruh data terkait secara permanen.</p>
            @endif
        </div>
        @if($tenant->activeRental)
            <button disabled class="btn text-sm border border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed whitespace-nowrap shrink-0">
                Kontrak Aktif
            </button>
        @else
            <form action="{{ route('tenants.destroy', $tenant) }}" method="POST" id="deleteTenantForm">
                @csrf @method('DELETE')
                <button type="button" onclick="confirmDeleteTenant()"
                        class="btn text-sm bg-red-500 hover:bg-red-600 text-white border-0 whitespace-nowrap shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus Penyewa
                </button>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDeleteTenant() {
    if (confirm('Hapus penyewa {{ addslashes($tenant->full_name) }}?\n\nAkun login juga akan dihapus.\nTindakan ini tidak dapat dibatalkan.')) {
        document.getElementById('deleteTenantForm').submit();
    }
}
</script>
@endpush
