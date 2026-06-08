@extends('layouts.app')
@section('title','Tambah Penyewa')
@section('page-title','Tambah Penyewa')

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('tenants.index') }}" class="btn btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        <h2 class="text-lg font-bold text-slate-800">Tambah Penyewa Baru</h2>
    </div>

    <form action="{{ route('tenants.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Akun Login</h3></div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" class="form-input" placeholder="penyewa@email.com" value="{{ old('email') }}" required>
                </div>
                <div>
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Min. 8 karakter">
                    <p class="form-hint">Kosongkan untuk gunakan default: Penyewa@123</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Data Pribadi</h3></div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" class="form-input" placeholder="Sesuai KTP" value="{{ old('full_name') }}" required>
                </div>
                <div>
                    <label class="form-label">NIK</label>
                    <input type="text" name="nik" class="form-input" placeholder="16 digit NIK" value="{{ old('nik') }}" maxlength="20">
                </div>
                <div>
                    <label class="form-label">No. WhatsApp <span class="text-red-500">*</span></label>
                    <input type="text" name="whatsapp_number" class="form-input" placeholder="628xxxxxxxxxx" value="{{ old('whatsapp_number') }}" required>
                    <p class="form-hint">Format: 628xxx (tanpa +)</p>
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Alamat Asal</label>
                    <textarea name="address" class="form-input" rows="2" placeholder="Alamat lengkap...">{{ old('address') }}</textarea>
                </div>
                <div>
                    <label class="form-label">Kontak Darurat</label>
                    <input type="text" name="emergency_contact" class="form-input" placeholder="Nama kontak darurat" value="{{ old('emergency_contact') }}">
                </div>
                <div>
                    <label class="form-label">No. HP Darurat</label>
                    <input type="text" name="emergency_phone" class="form-input" placeholder="08xxxxxxxxxx" value="{{ old('emergency_phone') }}">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-input" rows="2" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-slate-700">Dokumen & Foto</h3>
                <p class="text-xs text-slate-400 mt-0.5">Format JPG/PNG/WebP, maks 2 MB per file.</p>
            </div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Foto KTP --}}
                <div>
                    <label class="form-label">Foto KTP</label>
                    <label for="ktpInput"
                           id="ktpZone"
                           class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 rounded-xl p-6 text-center cursor-pointer hover:border-sky-kos hover:bg-sky-kos/5 transition-colors min-h-[120px]">
                        <svg id="ktpIcon" class="w-7 h-7 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/>
                        </svg>
                        <img id="ktpPreview" class="hidden w-full h-28 object-cover rounded-lg mb-2" alt="Preview KTP">
                        <p id="ktpLabel" class="text-sm text-slate-500">Upload foto KTP</p>
                        <p id="ktpSize" class="text-xs text-slate-400 mt-0.5 hidden"></p>
                    </label>
                    <input type="file" id="ktpInput" name="id_card_photo"
                           accept="image/jpeg,image/png,image/webp" class="sr-only">
                </div>

                {{-- Foto Profil --}}
                <div>
                    <label class="form-label">Foto Profil</label>
                    <label for="profileInput"
                           id="profileZone"
                           class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 rounded-xl p-6 text-center cursor-pointer hover:border-sky-kos hover:bg-sky-kos/5 transition-colors min-h-[120px]">
                        <svg id="profileIcon" class="w-7 h-7 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <img id="profilePreview" class="hidden w-24 h-24 object-cover rounded-full mb-2" alt="Preview Profil">
                        <p id="profileLabel" class="text-sm text-slate-500">Upload foto profil</p>
                        <p id="profileSize" class="text-xs text-slate-400 mt-0.5 hidden"></p>
                    </label>
                    <input type="file" id="profileInput" name="profile_photo"
                           accept="image/jpeg,image/png,image/webp" class="sr-only">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Preferensi Notifikasi</h3></div>
            <div class="card-body flex gap-6">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="notify_via_whatsapp" value="1" {{ old('notify_via_whatsapp', '1') ? 'checked' : '' }} class="rounded border-slate-300 text-sky-kos">
                    <span class="text-sm font-medium text-slate-700">Notifikasi via WhatsApp</span>
                </label>
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="notify_via_email" value="1" {{ old('notify_via_email') ? 'checked' : '' }} class="rounded border-slate-300 text-sky-kos">
                    <span class="text-sm font-medium text-slate-700">Notifikasi via Email</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('tenants.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Penyewa
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const MAX_SIZE = 2 * 1024 * 1024;
const ALLOWED  = ['image/jpeg', 'image/png', 'image/webp'];

function setupFileInput(inputId, previewId, labelId, sizeId, iconId) {
    const input   = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const label   = document.getElementById(labelId);
    const sizeEl  = document.getElementById(sizeId);
    const icon    = document.getElementById(iconId);

    if (!input) return;

    input.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) return;

        // Validasi tipe
        if (!ALLOWED.includes(file.type)) {
            alert('"' + file.name + '" tidak dapat diupload.\nFormat tidak didukung — gunakan JPG, PNG, atau WebP.');
            this.value = '';
            return;
        }

        // Validasi ukuran
        if (file.size > MAX_SIZE) {
            alert('"' + file.name + '" tidak dapat diupload.\nUkuran file ' + (file.size / 1024 / 1024).toFixed(1) + ' MB melebihi batas 2 MB.');
            this.value = '';
            return;
        }

        // Tampilkan preview
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            icon.classList.add('hidden');
            label.textContent = file.name.length > 24
                ? file.name.substring(0, 22) + '…'
                : file.name;
            label.classList.add('font-medium', 'text-slate-700');
            sizeEl.textContent = (file.size / 1024).toFixed(0) + ' KB';
            sizeEl.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    });
}

setupFileInput('ktpInput',     'ktpPreview',     'ktpLabel',     'ktpSize',     'ktpIcon');
setupFileInput('profileInput', 'profilePreview', 'profileLabel', 'profileSize', 'profileIcon');
</script>
@endpush
