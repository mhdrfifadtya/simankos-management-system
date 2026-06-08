@extends('layouts.app')
@section('title','Edit Kamar '.$room->room_number)
@section('page-title','Edit Kamar')

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('rooms.show', $room) }}" class="btn btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
        <h2 class="text-lg font-bold text-slate-800">Edit Kamar {{ $room->room_number }}</h2>
    </div>

    {{-- ═══ FORM UTAMA — HANYA berisi field edit & upload foto baru ═══ --}}
    <form action="{{ route('rooms.update', $room) }}"
          method="POST"
          enctype="multipart/form-data"
          id="editRoomForm"
          class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Informasi Kamar --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Informasi Kamar</h3></div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Nomor Kamar</label>
                    <input type="text" name="room_number" class="form-input"
                           value="{{ old('room_number', $room->room_number) }}" required>
                </div>
                <div>
                    <label class="form-label">Tipe Kamar</label>
                    <select name="type" class="form-input">
                        @foreach(['standard','deluxe','vip'] as $t)
                        <option value="{{ $t }}" {{ $room->type == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    @if($room->status === 'terisi')
                        <input type="text" class="form-input bg-slate-50 text-slate-400 cursor-not-allowed" value="Terisi (tidak bisa diubah)" disabled>
                        <input type="hidden" name="status" value="terisi">
                    @else
                        <select name="status" class="form-input">
                            <option value="tersedia" {{ $room->status === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="maintenance" {{ $room->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    @endif
                </div>
                <div>
                    <label class="form-label">Harga Sewa / Bulan (Rp)</label>
                    <input type="number" name="price" class="form-input"
                           value="{{ old('price', $room->price) }}" required min="0">
                </div>
                <div>
                    <label class="form-label">Luas (m²)</label>
                    <input type="number" name="area" class="form-input"
                           value="{{ old('area', $room->area) }}" step="0.01" min="0">
                </div>
                <div>
                    <label class="form-label">Lantai</label>
                    <input type="number" name="floor" class="form-input"
                           value="{{ old('floor', $room->floor) }}" min="0">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-input" rows="3">{{ old('description', $room->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Fasilitas --}}
        @if($facilities->isNotEmpty())
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Fasilitas Kamar</h3></div>
            <div class="card-body">
                @php $activeFacIds = $room->facilities->pluck('id')->toArray(); @endphp
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($facilities as $facility)
                    <label class="flex items-center gap-2.5 p-3 rounded-lg border cursor-pointer transition-colors
                                  {{ in_array($facility->id, $activeFacIds) ? 'border-sky-kos bg-sky-kos/5' : 'border-slate-200 hover:border-sky-kos hover:bg-sky-kos/5' }}">
                        <input type="checkbox" name="facility_ids[]" value="{{ $facility->id }}"
                               class="rounded border-slate-300 text-sky-kos"
                               {{ in_array($facility->id, old('facility_ids', $activeFacIds)) ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-slate-700">{{ $facility->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Upload Foto Baru — DI DALAM form utama --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-slate-700">Tambah Foto Baru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Format JPG/PNG, maks 2MB per foto. Foto pertama dijadikan utama jika belum ada foto.</p>
            </div>
            <div class="card-body">
                <label for="newPhotosInput"
                       class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 rounded-xl cursor-pointer hover:border-sky-kos hover:bg-sky-kos/5 transition-colors">
                    <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <p class="text-sm text-slate-500" id="uploadLabel">Klik untuk pilih foto</p>
                    <p class="text-xs text-slate-400 mt-0.5">Bisa pilih beberapa foto sekaligus</p>
                </label>
                {{-- Input file — di luar label agar tidak nested, tapi terhubung via 'for' --}}
                <input type="file" id="newPhotosInput" name="photos[]"
                       multiple accept="image/jpeg,image/png,image/webp"
                       class="sr-only">

                {{-- Preview foto yang dipilih --}}
                <div id="photoPreview" class="grid grid-cols-4 gap-2 mt-3 hidden"></div>
            </div>
        </div>

        {{-- Tombol Submit --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('rooms.show', $room) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
    {{-- ═══ END FORM UTAMA ═══ --}}


    {{-- ═══ FOTO YANG SUDAH ADA — DI LUAR form utama ═══ --}}
    {{-- Set Utama & Hapus menggunakan form terpisah (bukan nested) --}}
    {{-- ═══ HAPUS KAMAR ═══ --}}
    <div class="card mt-5 border-red-200">
        <div class="card-header" style="background:#fef2f2">
            <h3 class="font-semibold text-red-700">Zona Berbahaya</h3>
            <p class="text-xs text-red-400 mt-0.5">Tindakan berikut tidak dapat dibatalkan.</p>
        </div>
        <div class="card-body flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-slate-700">Hapus Kamar {{ $room->room_number }}</p>
                @if($room->status === 'terisi')
                    <p class="text-xs text-slate-400 mt-0.5">Tidak dapat dihapus — kamar sedang terisi oleh penyewa aktif.</p>
                @else
                    <p class="text-xs text-slate-400 mt-0.5">Menghapus kamar beserta seluruh foto dan data terkait secara permanen.</p>
                @endif
            </div>
            @if($room->status === 'terisi')
                <button disabled
                        class="btn text-sm border border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed whitespace-nowrap shrink-0">
                    Kamar Terisi
                </button>
            @else
                <form action="{{ route('rooms.destroy', $room) }}" method="POST" id="deleteRoomForm">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDeleteRoom()"
                            class="btn text-sm bg-red-500 hover:bg-red-600 text-white border-0 whitespace-nowrap shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Kamar
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if($room->photos->count())
    <div class="card mt-5">
        <div class="card-header flex items-center justify-between">
            <h3 class="font-semibold text-slate-700">Foto Kamar Saat Ini</h3>
            <span class="text-xs text-slate-400">{{ $room->photos->count() }} foto</span>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                @foreach($room->photos as $photo)
                <div class="relative group rounded-xl overflow-hidden bg-slate-100" style="aspect-ratio:1">
                    <img src="{{ $photo->url }}" class="w-full h-full object-cover" alt="Foto kamar"
                         onerror="this.closest('div').classList.add('bg-slate-200'); this.style.display='none';">

                    {{-- Badge Utama --}}
                    @if($photo->is_primary)
                    <div class="absolute top-1.5 left-1.5">
                        <span class="text-xs bg-sky-kos text-[#0F172A] font-bold px-2 py-0.5 rounded-full shadow">
                            Utama
                        </span>
                    </div>
                    @endif

                    {{-- Overlay aksi — HOVER --}}
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                        @if(!$photo->is_primary)
                        {{-- Form Set Utama — STANDALONE, di luar form edit --}}
                        <form action="{{ route('rooms.photos.primary', $photo) }}" method="POST"
                              style="display:inline">
                            @csrf
                            <button type="submit"
                                    class="text-xs bg-white text-slate-700 font-semibold px-2.5 py-1.5 rounded-lg hover:bg-slate-100 transition">
                                Set Utama
                            </button>
                        </form>
                        @endif

                        {{-- Form Hapus — STANDALONE, di luar form edit --}}
                        <form action="{{ route('rooms.photos.delete', $photo) }}" method="POST"
                              style="display:inline"
                              onsubmit="return confirm('Hapus foto ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-xs bg-red-500 text-white font-semibold px-2.5 py-1.5 rounded-lg hover:bg-red-600 transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            <p class="text-xs text-slate-400 mt-3">Arahkan kursor ke foto untuk mengatur atau menghapus.</p>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
const MAX_SIZE = 2 * 1024 * 1024;
const ALLOWED  = ['image/jpeg', 'image/png', 'image/webp'];

const input   = document.getElementById('newPhotosInput');
const label   = document.getElementById('uploadLabel');
const preview = document.getElementById('photoPreview');

input.addEventListener('change', function () {
    const files    = Array.from(this.files);
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

    // Ganti FileList: hanya file valid yang dikirim ke server
    this.files = dt.files;

    if (rejected.length > 0) {
        alert('File berikut tidak dapat diupload:\n\n' + rejected.join('\n'));
    }

    if (valid.length === 0) {
        label.textContent = 'Klik untuk pilih foto';
        preview.innerHTML = '';
        preview.classList.add('hidden');
        return;
    }

    label.textContent = valid.length + ' foto dipilih';
    preview.innerHTML = '';
    preview.classList.remove('hidden');

    valid.forEach(file => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const wrap = document.createElement('div');
            wrap.className = 'relative rounded-lg overflow-hidden bg-slate-100';
            wrap.style.aspectRatio = '1';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-full h-full object-cover';

            const badge = document.createElement('div');
            badge.className = 'absolute bottom-0 left-0 right-0 bg-black/50 text-white text-center py-1';
            badge.style.fontSize = '10px';
            badge.textContent = (file.size / 1024).toFixed(0) + ' KB';

            wrap.appendChild(img);
            wrap.appendChild(badge);
            preview.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
});

function confirmDeleteRoom() {
    if (confirm('Hapus kamar {{ $room->room_number }}?\n\nSeluruh foto kamar akan ikut dihapus.\nTindakan ini tidak dapat dibatalkan.')) {
        document.getElementById('deleteRoomForm').submit();
    }
}
</script>
@endpush
