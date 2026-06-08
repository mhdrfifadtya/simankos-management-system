@extends('layouts.app')
@section('title','Pengaturan Sistem')
@section('page-title','Pengaturan Sistem')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    <div class="xl:col-span-2 space-y-5">
        {{-- General Settings --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Informasi Kos</h3></div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf @method('POST')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="form-label">Nama Aplikasi</label>
                            <input type="text" name="app_name" class="form-input" value="{{ $settings['app_name']?->value ?? 'SiManKos' }}">
                        </div>
                        <div>
                            <label class="form-label">Nama Kos</label>
                            <input type="text" name="kos_name" class="form-input" value="{{ $settings['kos_name']?->value ?? '' }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="form-label">Alamat Kos</label>
                            <textarea name="kos_address" class="form-input" rows="2">{{ $settings['kos_address']?->value ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="form-label">No. Telepon Kos</label>
                            <input type="text" name="kos_phone" class="form-input" value="{{ $settings['kos_phone']?->value ?? '' }}">
                        </div>
                    </div>
                    <div class="mt-5 pt-5 border-t border-slate-50 flex justify-end">
                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Fonnte API --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-slate-700">Konfigurasi Fonnte WhatsApp</h3>
                <p class="text-xs text-slate-400 mt-0.5">Dapatkan token di <span class="text-sky-kos">fonnte.com</span></p>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">API Token Fonnte</label>
                            <input type="text" name="fonnte_token" class="form-input font-mono" value="{{ $settings['fonnte_token']?->value ?? '' }}" placeholder="Masukkan token Fonnte...">
                            <p class="form-hint">Token diperoleh dari dashboard Fonnte setelah menghubungkan perangkat WA</p>
                        </div>
                    </div>
                    <div class="mt-5 pt-5 border-t border-slate-50 flex justify-end gap-2">
                        <a href="{{ route('notifications.index') }}" class="btn btn-secondary">Test Koneksi</a>
                        <button type="submit" class="btn btn-primary">Simpan Token</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="space-y-5">
        {{-- Profile Quick Link --}}
        <div class="card">
            <div class="card-body">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-kos to-sky-dark flex items-center justify-center text-white font-extrabold text-lg">
                        {{ strtoupper(substr(auth()->user()->name??'A',0,1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                        <p class="text-sm text-slate-400">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <a href="{{ route('settings.profile') }}" class="btn btn-secondary w-full">Edit Profil Admin</a>
            </div>
        </div>

        {{-- App Info --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Info Aplikasi</h3></div>
            <div class="card-body space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">Versi</span>
                    <span class="text-sm font-semibold">1.0.0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">Laravel</span>
                    <span class="text-sm font-semibold">12.x</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">PHP</span>
                    <span class="text-sm font-semibold">{{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">Tema</span>
                    <span class="text-sm font-semibold text-sky-kos">SKYLINE</span>
                </div>
            </div>
        </div>

        {{-- Quick Scheduler --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Scheduler Reminder</h3></div>
            <div class="card-body">
                <p class="text-sm text-slate-500 mb-3">Notifikasi otomatis berjalan setiap hari pukul <strong>08:00 WIB</strong>.</p>
                <div class="bg-slate-800 rounded-lg px-3 py-2 font-mono text-xs text-green-400 overflow-x-auto">
                    * * * * * php artisan schedule:run
                </div>
                <p class="form-hint mt-2">Tambahkan ke crontab server untuk mengaktifkan scheduler</p>
            </div>
        </div>
    </div>
</div>
@endsection
