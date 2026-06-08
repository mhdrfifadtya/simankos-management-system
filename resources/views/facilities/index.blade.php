@extends('layouts.app')
@section('title','Manajemen Fasilitas')
@section('page-title','Fasilitas')

@section('content')
<div x-data="facilityManager()">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Fasilitas</h2>
            <p class="text-sm text-slate-400 mt-0.5">Total {{ $facilities->count() }} fasilitas tersedia</p>
        </div>
        <button @click="openAdd()" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Fasilitas
        </button>
    </div>

    {{-- Tabel --}}
    <div class="card overflow-hidden">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama Fasilitas</th>
                        <th>Digunakan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($facilities as $facility)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-sky-kos/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-sky-kos" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <p class="font-semibold text-slate-800 text-sm">{{ $facility->name }}</p>
                            </div>
                        </td>
                        <td>
                            @if($facility->rooms_count > 0)
                                <span class="badge badge-sky">{{ $facility->rooms_count }} kamar</span>
                            @else
                                <span class="text-slate-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button @click="openEdit({{ $facility->id }}, '{{ addslashes($facility->name) }}')"
                                        class="btn btn-secondary btn-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </button>
                                <form action="{{ route('facilities.destroy', $facility) }}" method="POST"
                                      onsubmit="return confirm('Hapus fasilitas \'{{ addslashes($facility->name) }}\'?\nFasilitas ini akan dilepas dari {{ $facility->rooms_count }} kamar.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm bg-red-50 text-red-500 hover:bg-red-100 border border-red-200" title="Hapus">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-14 text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            Belum ada fasilitas. Klik <strong>Tambah Fasilitas</strong> untuk mulai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Tambah / Edit --}}
    <div x-show="modal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             @click.stop>

            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-slate-800 text-base" x-text="editId ? 'Edit Fasilitas' : 'Tambah Fasilitas'"></h3>
                <button @click="closeModal()" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Form Tambah --}}
            <form x-show="!editId" action="{{ route('facilities.store') }}" method="POST">
                @csrf
                <label class="form-label">Nama Fasilitas <span class="text-red-500">*</span></label>
                <input type="text" name="name" class="form-input" placeholder="cth: AC, WiFi, Kasur..."
                       x-model="newName" required>
                <div class="flex gap-3 mt-5">
                    <button type="button" @click="closeModal()" class="btn btn-secondary flex-1">Batal</button>
                    <button type="submit" class="btn btn-primary flex-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan
                    </button>
                </div>
            </form>

            {{-- Form Edit --}}
            <div x-show="editId">
                <label class="form-label">Nama Fasilitas <span class="text-red-500">*</span></label>
                <input type="text" class="form-input" x-model="editName" placeholder="Nama fasilitas" required>
                <div class="flex gap-3 mt-5">
                    <button type="button" @click="closeModal()" class="btn btn-secondary flex-1">Batal</button>
                    <button type="button" @click="submitEdit()" class="btn btn-primary flex-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden edit forms (di luar modal agar tidak nested) --}}
    @foreach($facilities as $facility)
    <form id="editForm{{ $facility->id }}"
          action="{{ route('facilities.update', $facility) }}"
          method="POST" class="hidden">
        @csrf @method('PUT')
        <input type="text" name="name">
    </form>
    @endforeach

</div>
@endsection

@push('scripts')
<script>
function facilityManager() {
    return {
        modal: false,
        editId: null,
        editName: '',
        newName: '',

        openAdd() {
            this.editId = null;
            this.newName = '';
            this.modal = true;
        },

        openEdit(id, name) {
            this.editId = id;
            this.editName = name;
            this.modal = true;
        },

        closeModal() {
            this.modal = false;
            this.editId = null;
            this.editName = '';
            this.newName = '';
        },

        submitEdit() {
            if (!this.editName.trim()) return;
            const form = document.getElementById('editForm' + this.editId);
            if (!form) return;
            form.querySelector('input[name="name"]').value = this.editName;
            form.submit();
        },
    };
}
</script>
@endpush
