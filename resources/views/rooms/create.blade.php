@extends('layouts.app')
@section('title','Tambah Kamar')
@section('page-title','Tambah Kamar')

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('rooms.index') }}" class="btn btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        <h2 class="text-lg font-bold text-slate-800">Tambah Kamar Baru</h2>
    </div>

    <form action="{{ route('rooms.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-slate-700">Informasi Kamar</h3>
            </div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Nomor Kamar <span class="text-red-500">*</span></label>
                    <input type="text" name="room_number" class="form-input" placeholder="cth: A-01" value="{{ old('room_number') }}" required>
                </div>
                <div>
                    <label class="form-label">Tipe Kamar <span class="text-red-500">*</span></label>
                    <select name="type" class="form-input" required>
                        <option value="standard" {{ old('type','standard')=='standard'?'selected':'' }}>Standard</option>
                        <option value="deluxe" {{ old('type')=='deluxe'?'selected':'' }}>Deluxe</option>
                        <option value="vip" {{ old('type')=='vip'?'selected':'' }}>VIP</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="form-input" required>
                        <option value="tersedia" {{ old('status','tersedia')=='tersedia'?'selected':'' }}>Tersedia</option>
                        <option value="terisi" {{ old('status')=='terisi'?'selected':'' }}>Terisi</option>
                        <option value="maintenance" {{ old('status')=='maintenance'?'selected':'' }}>Maintenance</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Harga Sewa / Bulan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" class="form-input" placeholder="800000" value="{{ old('price') }}" min="0" required>
                </div>
                <div>
                    <label class="form-label">Luas Kamar (m²)</label>
                    <input type="number" name="area" class="form-input" placeholder="12" value="{{ old('area') }}" step="0.01">
                </div>
                <div>
                    <label class="form-label">Lantai</label>
                    <input type="number" name="floor" class="form-input" placeholder="1" value="{{ old('floor') }}" min="0">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-input" rows="3" placeholder="Deskripsi kamar...">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        @if($facilities->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-slate-700">Fasilitas Kamar</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($facilities as $facility)
                    <label class="flex items-center gap-2.5 p-3 rounded-lg border border-slate-200 cursor-pointer hover:border-sky-kos hover:bg-sky-kos/5 transition-colors">
                        <input type="checkbox" name="facility_ids[]" value="{{ $facility->id }}"
                               class="rounded border-slate-300 text-sky-kos"
                               {{ in_array($facility->id, old('facility_ids', [])) ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-slate-700">{{ $facility->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-slate-700">Foto Kamar</h3>
                <p class="text-sm text-slate-400 mt-0.5">Format: JPG/PNG, maks 2MB per foto. Foto pertama dijadikan foto utama.</p>
            </div>
            <div class="card-body">
                <label class="block w-full border-2 border-dashed border-slate-200 rounded-xl p-8 text-center cursor-pointer hover:border-sky-kos hover:bg-sky-kos/5 transition-colors">
                    <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-sm text-slate-500">Klik untuk upload foto</p>
                    <p class="text-xs text-slate-400 mt-1">Bisa pilih beberapa foto sekaligus</p>
                    <input type="file" name="photos[]" multiple accept="image/*" class="hidden" id="photoInput">
                </label>
                <div id="photoPreview" class="grid grid-cols-4 gap-2 mt-3"></div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Kamar
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const MAX_SIZE    = 2 * 1024 * 1024;
const ALLOWED     = ['image/jpeg', 'image/png', 'image/webp'];

function filterAndPreview(input, previewEl) {
    const files    = Array.from(input.files);
    const rejected = [];
    const valid    = [];
    const dt       = new DataTransfer();

    files.forEach(file => {
        if (!ALLOWED.includes(file.type)) {
            rejected.push(`"${file.name}" — format tidak didukung (gunakan JPG/PNG/WebP)`);
        } else if (file.size > MAX_SIZE) {
            rejected.push(`"${file.name}" — ukuran ${(file.size/1024/1024).toFixed(1)} MB melebihi batas 2 MB`);
        } else {
            valid.push(file);
            dt.items.add(file);
        }
    });

    // Ganti FileList dengan hanya file yang valid
    input.files = dt.files;

    if (rejected.length > 0) {
        alert('File berikut tidak dapat diupload:\n\n' + rejected.join('\n'));
    }

    previewEl.innerHTML = '';
    if (valid.length === 0) return;

    valid.forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = ev => {
            const div = document.createElement('div');
            div.className = 'relative rounded-lg overflow-hidden bg-slate-100';
            div.style.aspectRatio = '1';
            div.innerHTML = `
                <img src="${ev.target.result}" class="w-full h-full object-cover">
                ${i === 0 ? '<span class="absolute top-1 left-1 text-xs bg-sky-400 text-[#0F172A] font-bold px-1.5 py-0.5 rounded shadow">Utama</span>' : ''}
                <div class="absolute bottom-1 right-1 bg-black/50 text-white text-xs px-1.5 py-0.5 rounded">${(file.size/1024).toFixed(0)} KB</div>
            `;
            previewEl.appendChild(div);
        };
        reader.readAsDataURL(file);
    });

    // Update teks label
    const labelEl = input.closest('label');
    if (labelEl) {
        const p = labelEl.querySelector('p');
        if (p) p.textContent = valid.length + ' foto dipilih';
    }
}

const photoInput = document.getElementById('photoInput');
if (photoInput) {
    photoInput.addEventListener('change', function () {
        filterAndPreview(this, document.getElementById('photoPreview'));
    });
}
</script>
@endpush
