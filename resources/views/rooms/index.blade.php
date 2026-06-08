@extends('layouts.app')
@section('title','Manajemen Kamar')
@section('page-title','Manajemen Kamar')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Daftar Kamar</h2>
        <p class="text-sm text-slate-400 mt-0.5">Total {{ $rooms->total() }} kamar</p>
    </div>
    <a href="{{ route('rooms.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Kamar
    </a>
</div>

<div class="card mb-5">
    <form method="GET" class="p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">Semua Status</option>
                <option value="tersedia" {{ request('status')==='tersedia'?'selected':'' }}>Tersedia</option>
                <option value="terisi" {{ request('status')==='terisi'?'selected':'' }}>Terisi</option>
                <option value="maintenance" {{ request('status')==='maintenance'?'selected':'' }}>Maintenance</option>
            </select>
        </div>
        <div>
            <label class="form-label">Tipe</label>
            <select name="type" class="form-input">
                <option value="">Semua Tipe</option>
                <option value="standard" {{ request('type')==='standard'?'selected':'' }}>Standard</option>
                <option value="deluxe" {{ request('type')==='deluxe'?'selected':'' }}>Deluxe</option>
                <option value="vip" {{ request('type')==='vip'?'selected':'' }}>VIP</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request()->hasAny(['status','type']))
        <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($rooms as $room)
    <div class="card overflow-hidden group hover:shadow-card-hover transition-all duration-200">
        {{-- Photo --}}
        <div class="h-44 bg-gradient-to-br from-slate-100 to-slate-50 overflow-hidden relative">
            @if($room->primaryPhoto)
            <img src="{{ $room->primaryPhoto->url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="Kamar {{ $room->room_number }}">
            @else
            <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p class="text-xs">Belum ada foto</p>
            </div>
            @endif
            {{-- Status badge --}}
            <div class="absolute top-2.5 right-2.5">
                @php $c=['tersedia'=>'badge-green','terisi'=>'badge-blue','maintenance'=>'badge-yellow']; @endphp
                <span class="{{ $c[$room->status]??'badge-gray' }} badge shadow-sm">{{ $room->status }}</span>
            </div>
            {{-- Type badge --}}
            <div class="absolute top-2.5 left-2.5">
                <span class="badge badge-gray shadow-sm capitalize">{{ $room->type }}</span>
            </div>
        </div>

        <div class="p-4">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h3 class="font-bold text-slate-800 text-base">Kamar {{ $room->room_number }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        @if($room->floor) · Lt. {{ $room->floor }} @endif
                        @if($room->area) · {{ $room->area }}m² @endif
                    </p>
                </div>
            </div>

            <p class="text-[#0EA5E9] font-extrabold text-sm mb-3">Rp {{ number_format($room->price,0,',','.') }}<span class="text-slate-400 font-normal text-xs">/bln</span></p>

            @if($room->status === 'terisi' && $room->activeRental)
            <div class="flex items-center gap-2 mb-3 p-2.5 bg-blue-50 rounded-lg">
                <div class="w-6 h-6 rounded-md bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-600 flex-shrink-0">
                    {{ strtoupper(substr($room->activeRental->tenant?->full_name??'P',0,1)) }}
                </div>
                <p class="text-xs font-semibold text-blue-700 truncate">{{ $room->activeRental->tenant?->full_name }}</p>
            </div>
            @endif

            @if($room->facilities->isNotEmpty())
            <div class="flex flex-wrap gap-1 mb-3">
                @foreach($room->facilities->take(3) as $f)
                <span class="text-xs bg-slate-50 text-slate-500 px-2 py-0.5 rounded-md border border-slate-100">{{ $f->name }}</span>
                @endforeach
                @if($room->facilities->count() > 3)
                <span class="text-xs bg-slate-50 text-slate-400 px-2 py-0.5 rounded-md border border-slate-100">+{{ $room->facilities->count()-3 }}</span>
                @endif
            </div>
            @endif

            <div class="flex gap-2">
                <a href="{{ route('rooms.show', $room) }}" class="btn btn-secondary flex-1 text-center text-xs py-2">Detail</a>
                <a href="{{ route('rooms.edit', $room) }}" class="btn btn-primary flex-1 text-center text-xs py-2">Edit</a>
                @if($room->status !== 'terisi')
                <form action="{{ route('rooms.destroy', $room) }}" method="POST"
                      onsubmit="return confirm('Hapus kamar {{ $room->room_number }}? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn text-xs py-2 px-2.5 bg-red-50 text-red-500 hover:bg-red-100 border border-red-200 shrink-0" title="Hapus Kamar">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full card p-16 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        <p class="text-slate-400">Belum ada kamar. <a href="{{ route('rooms.create') }}" class="text-sky-kos hover:underline">Tambah kamar pertama</a></p>
    </div>
    @endforelse
</div>

<div class="mt-5">{{ $rooms->withQueryString()->links() }}</div>
@endsection
