@extends('layouts.app')
@section('title','Profil Admin')
@section('page-title','Profil Admin')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('settings.index') }}" class="btn btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        <h2 class="text-lg font-bold text-slate-800">Edit Profil</h2>
    </div>

    <form action="{{ route('settings.profile.update') }}" method="POST" class="space-y-5">
        @csrf
        <div class="card">
            <div class="card-body flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-sky-kos to-sky-dark flex items-center justify-center text-white font-extrabold text-2xl flex-shrink-0">
                    {{ strtoupper(substr($user->name??'A',0,1)) }}
                </div>
                <div>
                    <p class="font-bold text-slate-800 text-lg">{{ $user->name }}</p>
                    <p class="text-slate-400 text-sm">{{ $user->email }}</p>
                    <span class="badge badge-navy mt-1">Administrator</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Informasi Akun</h3></div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                </div>
                <div>
                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                </div>
                <div>
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-slate-700">Ubah Password</h3>
                <p class="text-xs text-slate-400 mt-0.5">Kosongkan jika tidak ingin mengubah password</p>
            </div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="form-label">Password Saat Ini</label>
                    <input type="password" name="current_password" class="form-input" placeholder="Masukkan password lama">
                </div>
                <div>
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="new_password" class="form-input" placeholder="Min. 8 karakter">
                </div>
                <div>
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" class="form-input" placeholder="Ulangi password baru">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('settings.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
