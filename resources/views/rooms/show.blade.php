@extends('layouts.app')
@section('title','Detail Kamar '.$room->room_number)
@section('page-title','Detail Kamar')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('rooms.index') }}" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
    <div class="flex-1"></div>
    <a href="{{ route('rooms.edit', $room) }}" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit
    </a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
    {{-- Photos --}}
    <div class="xl:col-span-2 space-y-5">
        <div class="card overflow-hidden">
            @if($room->photos->count())
            <div class="grid grid-cols-{{ min($room->photos->count(),3) }} gap-1">
                @foreach($room->photos->take(3) as $i => $photo)
                <div class="{{ $i===0 && $room->photos->count()>=2 ? 'row-span-2' : '' }} bg-slate-100 overflow-hidden {{ $i===0 ? 'aspect-[4/3]' : 'aspect-square' }}">
                    <img src="{{ $photo->url }}" class="w-full h-full object-cover" alt="Foto kamar">
                </div>
                @endforeach
            </div>
            @else
            <div class="h-56 bg-gradient-to-br from-slate-100 to-slate-50 flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-slate-400 text-sm">Belum ada foto</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Active Rental --}}
        @if($room->activeRental)
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="font-semibold text-slate-700">Penyewa Aktif</h3>
                <span class="badge badge-green">Terisi</span>
            </div>
            <div class="card-body">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-kos/20 to-sky-dark/20 flex items-center justify-center text-sky-kos font-bold text-lg flex-shrink-0">
                        {{ strtoupper(substr($room->activeRental->tenant?->full_name ?? 'P', 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-slate-800">{{ $room->activeRental->tenant?->full_name }}</p>
                        <p class="text-sm text-slate-500">{{ $room->activeRental->tenant?->whatsapp_number }}</p>
                    </div>
                    <a href="{{ route('tenants.show', $room->activeRental->tenant_id) }}" class="btn btn-secondary btn-sm">Detail</a>
                </div>
                <div class="grid grid-cols-3 gap-4 mt-4 pt-4 border-t border-slate-50">
                    <div>
                        <p class="text-xs text-slate-400">Mulai Kontrak</p>
                        <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ $room->activeRental->start_date?->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Sewa/Bulan</p>
                        <p class="text-sm font-semibold text-slate-700 mt-0.5">Rp {{ number_format($room->activeRental->monthly_rent,0,',','.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Tgl Jatuh Tempo</p>
                        <p class="text-sm font-semibold text-slate-700 mt-0.5">Tanggal {{ $room->activeRental->due_date_day }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Info Panel --}}
    <div class="space-y-5">
        <div class="card">
            <div class="card-body">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-2xl font-extrabold text-slate-800">Kamar {{ $room->room_number }}</p>
                    </div>
                    @php $sc = ['tersedia'=>'badge-green','terisi'=>'badge-blue','maintenance'=>'badge-yellow']; @endphp
                    <span class="{{ $sc[$room->status] ?? 'badge-gray' }} badge capitalize">{{ $room->status }}</span>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2.5 border-b border-slate-50">
                        <span class="text-sm text-slate-500">Tipe</span>
                        <span class="text-sm font-semibold text-slate-700 capitalize">{{ $room->type }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2.5 border-b border-slate-50">
                        <span class="text-sm text-slate-500">Harga Sewa</span>
                        <span class="text-sm font-bold text-slate-800">Rp {{ number_format($room->price,0,',','.') }}/bln</span>
                    </div>
                    @if($room->area)
                    <div class="flex items-center justify-between py-2.5 border-b border-slate-50">
                        <span class="text-sm text-slate-500">Luas</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $room->area }} m²</span>
                    </div>
                    @endif
                    @if($room->floor)
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm text-slate-500">Lantai</span>
                        <span class="text-sm font-semibold text-slate-700">Lantai {{ $room->floor }}</span>
                    </div>
                    @endif
                </div>
                @if($room->description)
                <p class="text-sm text-slate-500 mt-4 pt-4 border-t border-slate-50 leading-relaxed">{{ $room->description }}</p>
                @endif
            </div>
        </div>

        {{-- Fasilitas --}}
        @if($room->facilities->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-slate-700">Fasilitas</h3>
            </div>
            <div class="card-body">
                <div class="flex flex-wrap gap-2">
                    @foreach($room->facilities as $f)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-600 font-medium">
                        <svg class="w-3.5 h-3.5 text-sky-kos" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        {{ $f->name }}
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Actions --}}
        <div class="card">
            <div class="card-body space-y-2">
                @if($room->status === 'tersedia')
                <a href="{{ route('rentals.create', ['room_id' => $room->id]) }}" class="btn btn-primary w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat Kontrak Baru
                </a>
                @endif
                <a href="{{ route('rooms.edit', $room) }}" class="btn btn-secondary w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Info Kamar
                </a>
                @if($room->status !== 'terisi')
                <form action="{{ route('rooms.destroy', $room) }}" method="POST"
                      onsubmit="return confirm('Hapus kamar {{ $room->room_number }}?\n\nSeluruh foto kamar akan ikut dihapus.\nTindakan ini tidak dapat dibatalkan.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn w-full text-sm bg-red-50 hover:bg-red-100 text-red-600 border border-red-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus Kamar
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
