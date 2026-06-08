@extends('layouts.app')
@section('title','Edit Penyewa')
@section('page-title','Edit Penyewa')

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('tenants.show', $tenant) }}" class="btn btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        <h2 class="text-lg font-bold text-slate-800">Edit: {{ $tenant->full_name }}</h2>
    </div>

    <form action="{{ route('tenants.update', $tenant) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Data Pribadi</h3></div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" class="form-input" value="{{ old('full_name', $tenant->full_name) }}" required>
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $tenant->user?->email) }}" required>
                </div>
                <div>
                    <label class="form-label">No. WhatsApp</label>
                    <input type="text" name="whatsapp_number" class="form-input" value="{{ old('whatsapp_number', $tenant->whatsapp_number) }}" required>
                </div>
                <div>
                    <label class="form-label">NIK</label>
                    <input type="text" name="nik" class="form-input" value="{{ old('nik', $tenant->nik) }}" maxlength="20">
                </div>
                <div>
                    <label class="form-label">Kontak Darurat</label>
                    <input type="text" name="emergency_contact" class="form-input" value="{{ old('emergency_contact', $tenant->emergency_contact) }}">
                </div>
                <div>
                    <label class="form-label">No. HP Darurat</label>
                    <input type="text" name="emergency_phone" class="form-input" value="{{ old('emergency_phone', $tenant->emergency_phone) }}">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-input" rows="2">{{ old('address', $tenant->address) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-input" rows="2">{{ old('notes', $tenant->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-slate-700">Dokumen</h3>
                <p class="text-xs text-slate-400 mt-0.5">Format JPG/PNG/WebP, maks 2 MB per file.</p>
            </div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Foto KTP Baru</label>
                    @if($tenant->id_card_photo)
                    <p class="text-xs text-slate-400 mb-1">Foto saat ini:</p>
                    <div class="mb-2 rounded-lg overflow-hidden h-24 bg-slate-100">
                        <img src="{{ $tenant->id_card_photo_url ?? asset('storage/'.$tenant->id_card_photo) }}" class="h-full object-contain mx-auto">
                    </div>
                    @endif
                    <input type="file" id="editKtpInput" name="id_card_photo"
                           accept="image/jpeg,image/png,image/webp" class="form-input py-2">
                    <div id="editKtpPreviewWrap" class="hidden mt-2">
                        <p class="text-xs text-slate-400 mb-1">Preview baru:</p>
                        <div class="rounded-lg overflow-hidden h-24 bg-slate-100">
                            <img id="editKtpPreview" src="" class="h-full object-contain mx-auto" alt="Preview KTP">
                        </div>
                        <p id="editKtpMsg" class="text-xs mt-1 text-green-600 font-medium"></p>
                    </div>
                </div>
                <div>
                    <label class="form-label">Foto Profil Baru</label>
                    @if($tenant->profile_photo)
                    <p class="text-xs text-slate-400 mb-1">Foto saat ini:</p>
                    <div class="mb-2 rounded-lg overflow-hidden h-24 bg-slate-100">
                        <img src="{{ $tenant->profile_photo_url ?? asset('storage/'.$tenant->profile_photo) }}" class="h-full object-contain mx-auto">
                    </div>
                    @endif
                    <input type="file" id="editProfileInput" name="profile_photo"
                           accept="image/jpeg,image/png,image/webp" class="form-input py-2">
                    <div id="editProfilePreviewWrap" class="hidden mt-2">
                        <p class="text-xs text-slate-400 mb-1">Preview baru:</p>
                        <div class="rounded-lg overflow-hidden h-24 bg-slate-100 flex items-center justify-center">
                            <img id="editProfilePreview" src="" class="h-full w-24 object-cover rounded-full" alt="Preview Profil">
                        </div>
                        <p id="editProfileMsg" class="text-xs mt-1 text-green-600 font-medium"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Preferensi Notifikasi</h3></div>
            <div class="card-body flex gap-6">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="notify_via_whatsapp" value="1" {{ old('notify_via_whatsapp', $tenant->notify_via_whatsapp) ? 'checked' : '' }} class="rounded border-slate-300 text-sky-kos">
                    <span class="text-sm font-medium text-slate-700">WhatsApp</span>
                </label>
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="notify_via_email" value="1" {{ old('notify_via_email', $tenant->notify_via_email) ? 'checked' : '' }} class="rounded border-slate-300 text-sky-kos">
                    <span class="text-sm font-medium text-slate-700">Email</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('tenants.show', $tenant) }}" class="btn btn-secondary">Batal</a>
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
                <p class="text-sm font-semibold text-slate-700">Hapus Penyewa {{ $tenant->full_name }}</p>
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
</div>
@endsection

@push('scripts')
<script>
const MAX_SIZE = 2 * 1024 * 1024;
const ALLOWED  = ['image/jpeg', 'image/png', 'image/webp'];

function setupEditFileInput(inputId, previewId, wrapId, msgId) {
    const input   = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const wrap    = document.getElementById(wrapId);
    const msgEl   = document.getElementById(msgId);
    if (!input) return;

    input.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) {
            wrap.classList.add('hidden');
            return;
        }

        if (!ALLOWED.includes(file.type)) {
            alert('"' + file.name + '" tidak dapat diupload.\nFormat tidak didukung — gunakan JPG, PNG, atau WebP.');
            this.value = '';
            wrap.classList.add('hidden');
            return;
        }

        if (file.size > MAX_SIZE) {
            alert('"' + file.name + '" tidak dapat diupload.\nUkuran file ' + (file.size / 1024 / 1024).toFixed(1) + ' MB melebihi batas 2 MB.');
            this.value = '';
            wrap.classList.add('hidden');
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            wrap.classList.remove('hidden');
            msgEl.textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
        };
        reader.readAsDataURL(file);
    });
}

setupEditFileInput('editKtpInput',     'editKtpPreview',     'editKtpPreviewWrap',     'editKtpMsg');
setupEditFileInput('editProfileInput', 'editProfilePreview', 'editProfilePreviewWrap', 'editProfileMsg');

function confirmDeleteTenant() {
    if (confirm('Hapus penyewa {{ addslashes($tenant->full_name) }}?\n\nAkun login juga akan dihapus.\nTindakan ini tidak dapat dibatalkan.')) {
        document.getElementById('deleteTenantForm').submit();
    }
}
</script>
@endpush
