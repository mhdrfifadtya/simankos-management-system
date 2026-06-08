@extends('layouts.app')
@section('title','Edit Kontrak')
@section('page-title','Edit Kontrak')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('rentals.show', $rental) }}" class="btn btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        <h2 class="text-lg font-bold text-slate-800">Perpanjang / Edit Kontrak</h2>
    </div>

    <div class="card mb-5 bg-slate-50 border-0">
        <div class="card-body flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-sky-kos/20 flex items-center justify-center text-sky-dark font-bold flex-shrink-0">
                {{ strtoupper(substr($rental->tenant?->full_name??'P',0,1)) }}
            </div>
            <div>
                <p class="font-semibold text-slate-800">{{ $rental->tenant?->full_name }}</p>
                <p class="text-sm text-slate-500">Kamar {{ $rental->room?->room_number }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('rentals.update', $rental) }}" method="POST" class="space-y-5">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Detail Kontrak</h3></div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-input bg-slate-50" value="{{ $rental->start_date?->format('Y-m-d') }}" disabled>
                    <p class="form-hint">Tidak bisa diubah</p>
                </div>
                <div>
                    <label class="form-label">Perpanjang Sampai</label>
                    <input type="date" name="end_date" class="form-input" value="{{ old('end_date', $rental->end_date?->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="form-label">Harga Sewa / Bulan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="monthly_rent" class="form-input" value="{{ old('monthly_rent', $rental->monthly_rent) }}" required>
                </div>
                <div>
                    <label class="form-label">Tanggal Jatuh Tempo</label>
                    <input type="number" class="form-input bg-slate-50" value="{{ $rental->due_date_day }}" disabled>
                    <input type="hidden" name="due_date_day" value="{{ $rental->due_date_day }}">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-input">{{ old('notes', $rental->notes) }}</textarea>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('rentals.show', $rental) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>

    {{-- Zona Berbahaya --}}
    <div class="card mt-5 border-red-200">
        <div class="card-header" style="background:#fef2f2">
            <h3 class="font-semibold text-red-700">Zona Berbahaya</h3>
            <p class="text-xs text-red-400 mt-0.5">Tindakan berikut tidak dapat dibatalkan.</p>
        </div>
        <div class="card-body flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-slate-700">Hapus Kontrak</p>
                <p class="text-xs text-slate-400 mt-0.5">Tidak dapat dihapus — kontrak masih aktif. Akhiri kontrak terlebih dahulu.</p>
            </div>
            <button disabled class="btn text-sm border border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed whitespace-nowrap shrink-0">
                Kontrak Aktif
            </button>
        </div>
    </div>
</div>
@endsection
