@extends('layouts.app')
@section('title','Detail Kontrak')
@section('page-title','Detail Kontrak')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('rentals.index') }}" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
    <div class="flex-1"></div>
    @if($rental->status === 'aktif')
    <a href="{{ route('rentals.edit', $rental) }}" class="btn btn-secondary btn-sm">Edit</a>
    <form action="{{ route('rentals.terminate', $rental) }}" method="POST" onsubmit="return confirm('Yakin mengakhiri kontrak ini? Kamar akan dikembalikan ke status tersedia.')">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-danger btn-sm">Akhiri Kontrak</button>
    </form>
    @endif
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
    <div class="space-y-5">
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-slate-400 text-sm font-medium">Status Kontrak</span>
                    @php $c=['aktif'=>'badge-green','berakhir'=>'badge-gray','dibatalkan'=>'badge-red']; @endphp
                    <span class="badge {{ $c[$rental->status]??'badge-gray' }} text-sm">{{ $rental->status }}</span>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-500">Penyewa</span>
                        <a href="{{ route('tenants.show', $rental->tenant_id) }}" class="text-sm font-semibold text-sky-kos hover:underline">{{ $rental->tenant?->full_name }}</a>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-500">Kamar</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $rental->room?->room_number }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-500">Sewa/Bulan</span>
                        <span class="text-sm font-bold text-slate-800">Rp {{ number_format($rental->monthly_rent,0,',','.') }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-500">Deposit</span>
                        <span class="text-sm font-semibold text-slate-700">Rp {{ number_format($rental->deposit,0,',','.') }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-500">Mulai</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $rental->start_date?->format('d M Y') }}</span>
                    </div>
                    @if($rental->end_date)
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-500">Berakhir</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $rental->end_date?->format('d M Y') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between py-2">
                        <span class="text-sm text-slate-500">Jatuh Tempo</span>
                        <span class="text-sm font-semibold text-slate-700">Tanggal {{ $rental->due_date_day }}</span>
                    </div>
                </div>
                @if($rental->notes)
                <div class="mt-4 pt-4 border-t border-slate-50">
                    <p class="text-xs text-slate-400 mb-1">Catatan</p>
                    <p class="text-sm text-slate-600">{{ $rental->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($rental->status === 'aktif')
        <div class="card">
            <div class="card-body space-y-2">
                <a href="{{ route('invoices.generate') }}?rental_id={{ $rental->id }}&year={{ now()->year }}&month={{ now()->month }}" class="btn btn-secondary w-full btn-sm" onclick="return confirm('Generate invoice bulan ini?')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Generate Invoice Bulan Ini
                </a>
                <a href="{{ route('payments.create', ['invoice_id' => $rental->invoices->where('status','!=','paid')->first()?->id]) }}" class="btn btn-primary w-full btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Input Pembayaran
                </a>
            </div>
        </div>
        @endif
    </div>

    <div class="xl:col-span-2 space-y-5">
        {{-- Invoices --}}
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="font-semibold text-slate-700">Riwayat Invoice</h3>
                <span class="text-xs text-slate-400">{{ $rental->invoices->count() }} invoice</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>No. Invoice</th><th>Periode</th><th>Jatuh Tempo</th><th class="text-right">Total</th><th class="text-center">Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse($rental->invoices->sortByDesc('created_at') as $inv)
                        <tr>
                            <td><a href="{{ route('invoices.show', $inv) }}" class="text-sky-kos hover:underline font-medium">{{ $inv->invoice_number }}</a></td>
                            <td>{{ $inv->period_label }}</td>
                            <td class="text-slate-500 text-sm">{{ $inv->due_date?->format('d/m/Y') }}</td>
                            <td class="text-right font-semibold">Rp {{ number_format($inv->total,0,',','.') }}</td>
                            <td class="text-center">
                                @php $c=['paid'=>'badge-green','overdue'=>'badge-red','pending'=>'badge-yellow','sent'=>'badge-sky','draft'=>'badge-gray']; @endphp
                                <span class="badge {{ $c[$inv->status]??'badge-gray' }}">{{ $inv->status }}</span>
                            </td>
                            <td><a href="{{ route('invoices.pdf', $inv) }}" class="text-slate-400 hover:text-red-500" title="Download PDF"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-8 text-slate-400">Belum ada invoice</td></tr>
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
            <p class="text-sm font-semibold text-slate-700">Hapus Kontrak</p>
            @if($rental->status === 'aktif')
                <p class="text-xs text-slate-400 mt-0.5">Tidak dapat dihapus — kontrak masih aktif. Akhiri kontrak terlebih dahulu.</p>
            @else
                <p class="text-xs text-slate-400 mt-0.5">Menghapus kontrak beserta seluruh riwayat invoice terkait secara permanen.</p>
            @endif
        </div>
        @if($rental->status === 'aktif')
            <button disabled class="btn text-sm border border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed whitespace-nowrap shrink-0">
                Kontrak Aktif
            </button>
        @else
            <form action="{{ route('rentals.destroy', $rental) }}" method="POST" id="deleteRentalForm">
                @csrf @method('DELETE')
                <button type="button" onclick="confirmDeleteRental()"
                        class="btn text-sm bg-red-500 hover:bg-red-600 text-white border-0 whitespace-nowrap shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus Kontrak
                </button>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDeleteRental() {
    if (confirm('Hapus kontrak {{ $rental->tenant?->full_name }} — Kamar {{ $rental->room?->room_number }}?\n\nTindakan ini tidak dapat dibatalkan.')) {
        document.getElementById('deleteRentalForm').submit();
    }
}
</script>
@endpush
