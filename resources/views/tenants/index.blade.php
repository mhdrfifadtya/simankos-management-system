@extends('layouts.app')
@section('title','Manajemen Penyewa')
@section('page-title','Manajemen Penyewa')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Daftar Penyewa</h2>
        <p class="text-sm text-slate-400 mt-0.5">Total {{ $tenants->total() }} penyewa terdaftar</p>
    </div>
    <a href="{{ route('tenants.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Penyewa
    </a>
</div>

{{-- Filters --}}
<div class="card mb-5">
    <form method="GET" class="p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="form-label">Cari</label>
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" class="form-input pl-9" placeholder="Nama, NIK, atau WhatsApp..." value="{{ request('search') }}">
            </div>
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status')==='aktif'?'selected':'' }}>Aktif</option>
                <option value="nonaktif" {{ request('status')==='nonaktif'?'selected':'' }}>Non-aktif</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Cari</button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ route('tenants.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>
</div>

<div class="card overflow-hidden">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Penyewa</th>
                    <th>WhatsApp</th>
                    <th>Kamar Aktif</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-sky-kos/20 to-sky-dark/20 flex items-center justify-center text-sky-dark font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($tenant->full_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800 text-sm">{{ $tenant->full_name }}</p>
                                <p class="text-xs text-slate-400">{{ $tenant->user?->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="text-slate-600">{{ $tenant->whatsapp_number ?? '—' }}</td>
                    <td>
                        @if($tenant->activeRental)
                        <div>
                            <p class="font-medium text-sm text-slate-700">Kamar {{ $tenant->activeRental->room?->room_number }}</p>
                        </div>
                        @else
                        <span class="text-slate-400 text-sm">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $tenant->status==='aktif' ? 'badge-green' : 'badge-gray' }}">{{ $tenant->status }}</span>
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('tenants.show', $tenant) }}" class="btn btn-secondary btn-sm">Detail</a>
                            <a href="{{ route('tenants.edit', $tenant) }}" class="btn btn-secondary btn-sm">Edit</a>
                            @if(!$tenant->activeRental)
                            <form action="{{ route('tenants.destroy', $tenant) }}" method="POST"
                                  onsubmit="return confirm('Hapus penyewa {{ addslashes($tenant->full_name) }}?\n\nAkun login juga akan dihapus.\nTindakan ini tidak dapat dibatalkan.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm bg-red-50 text-red-500 hover:bg-red-100 border border-red-200" title="Hapus Penyewa">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-14 text-slate-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Belum ada penyewa terdaftar
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-slate-50">{{ $tenants->withQueryString()->links() }}</div>
</div>
@endsection
