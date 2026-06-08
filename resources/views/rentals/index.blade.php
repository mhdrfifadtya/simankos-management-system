@extends('layouts.app')
@section('title','Kontrak Sewa')
@section('page-title','Kontrak Sewa')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Manajemen Kontrak</h2>
        <p class="text-sm text-slate-400 mt-0.5">Total {{ $rentals->total() }} kontrak</p>
    </div>
    <a href="{{ route('rentals.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Kontrak Baru
    </a>
</div>

<div class="card mb-5">
    <form method="GET" class="p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="form-label">Cari Penyewa</label>
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" class="form-input pl-9" placeholder="Nama penyewa..." value="{{ request('search') }}">
            </div>
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">Semua</option>
                <option value="aktif" {{ request('status')==='aktif'?'selected':'' }}>Aktif</option>
                <option value="berakhir" {{ request('status')==='berakhir'?'selected':'' }}>Berakhir</option>
                <option value="dibatalkan" {{ request('status')==='dibatalkan'?'selected':'' }}>Dibatalkan</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ route('rentals.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>
</div>

<div class="card overflow-hidden">
    <div class="table-wrap">
        <table>
            <thead><tr>
                <th>Penyewa</th><th>Kamar</th><th>Mulai Kontrak</th><th>Sewa/Bulan</th><th>Status</th><th class="text-right">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($rentals as $rental)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-sky-kos/10 flex items-center justify-center text-sky-dark font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($rental->tenant?->full_name??'P',0,1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-slate-800">{{ $rental->tenant?->full_name }}</p>
                                <p class="text-xs text-slate-400">{{ $rental->tenant?->whatsapp_number }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="font-medium text-slate-700">{{ $rental->room?->room_number }}</p>
                    </td>
                    <td>
                        <p class="text-sm text-slate-700">{{ $rental->start_date?->format('d M Y') }}</p>
                        @if($rental->end_date)
                        <p class="text-xs text-slate-400">s/d {{ $rental->end_date?->format('d M Y') }}</p>
                        @endif
                    </td>
                    <td class="font-semibold text-slate-700">Rp {{ number_format($rental->monthly_rent,0,',','.') }}</td>
                    <td>
                        @php $c=['aktif'=>'badge-green','berakhir'=>'badge-gray','dibatalkan'=>'badge-red']; @endphp
                        <span class="badge {{ $c[$rental->status]??'badge-gray' }}">{{ $rental->status }}</span>
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('rentals.show', $rental) }}" class="btn btn-secondary btn-sm">Detail</a>
                            @if($rental->status === 'aktif')
                            <form action="{{ route('rentals.terminate', $rental) }}" method="POST" onsubmit="return confirm('Akhiri kontrak ini?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-danger btn-sm">Akhiri</button>
                            </form>
                            @else
                            <form action="{{ route('rentals.destroy', $rental) }}" method="POST"
                                  onsubmit="return confirm('Hapus kontrak ini? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm bg-red-50 text-red-500 hover:bg-red-100 border border-red-200" title="Hapus Kontrak">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-14 text-slate-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Belum ada kontrak
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-slate-50">{{ $rentals->withQueryString()->links() }}</div>
</div>
@endsection
