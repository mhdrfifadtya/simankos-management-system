@extends('layouts.app')
@section('title','Laporan Hunian Kamar')
@section('page-title','Laporan Hunian Kamar')

@section('content')
<div class="flex gap-2 mb-5">
    <a href="{{ route('reports.payments') }}" class="btn btn-secondary btn-sm">Pembayaran</a>
    <a href="{{ route('reports.rooms') }}" class="btn btn-primary btn-sm">Hunian Kamar</a>
</div>

<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="stat-card border-l-4 border-slate-400">
        <p class="text-xs text-slate-400 uppercase font-semibold mb-2">Total Kamar</p>
        <p class="text-3xl font-extrabold text-slate-800">{{ $stats['total'] }}</p>
    </div>
    <div class="stat-card border-l-4 border-blue-400">
        <p class="text-xs text-slate-400 uppercase font-semibold mb-2">Terisi</p>
        <p class="text-3xl font-extrabold text-blue-600">{{ $stats['terisi'] }}</p>
    </div>
    <div class="stat-card border-l-4 border-green-400">
        <p class="text-xs text-slate-400 uppercase font-semibold mb-2">Tersedia</p>
        <p class="text-3xl font-extrabold text-green-600">{{ $stats['tersedia'] }}</p>
    </div>
    <div class="stat-card border-l-4 border-sky-kos">
        <p class="text-xs text-slate-400 uppercase font-semibold mb-2">Tingkat Hunian</p>
        <p class="text-3xl font-extrabold text-sky-dark">{{ $stats['occupancy_rate'] }}%</p>
        <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-sky-kos rounded-full" style="width: {{ $stats['occupancy_rate'] }}%"></div>
        </div>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="font-semibold text-slate-700">Daftar Kamar</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Kamar</th><th>Tipe</th><th>Lantai</th><th>Harga/Bulan</th><th class="text-center">Status</th></tr></thead>
            <tbody>
                @forelse($rooms as $room)
                <tr>
                    <td><a href="{{ route('rooms.show', $room) }}" class="text-sky-kos hover:underline font-medium">{{ $room->room_number }}</a></td>
                    <td><span class="badge badge-gray capitalize">{{ $room->type }}</span></td>
                    <td class="text-slate-500">{{ $room->floor ? 'Lt. '.$room->floor : '—' }}</td>
                    <td class="font-semibold">Rp {{ number_format($room->price,0,',','.') }}</td>
                    <td class="text-center">
                        @php $c=['tersedia'=>'badge-green','terisi'=>'badge-blue','maintenance'=>'badge-yellow']; @endphp
                        <span class="badge {{ $c[$room->status]??'badge-gray' }}">{{ $room->status }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-12 text-slate-400">Belum ada kamar</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
