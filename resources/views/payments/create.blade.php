@extends('layouts.app')
@section('title','Input Pembayaran')
@section('page-title','Input Pembayaran')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('payments.index') }}" class="btn btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        <h2 class="text-lg font-bold text-slate-800">Input Pembayaran</h2>
    </div>

    <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5" x-data="{ selectedInv: '{{ $invoice?->id ?? '' }}', invoices: @json($invoices) }">
        @csrf
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Pilih Invoice</h3></div>
            <div class="card-body">
                <label class="form-label">Invoice yang Dibayar <span class="text-red-500">*</span></label>
                <select name="invoice_id" class="form-input" x-model="selectedInv" required>
                    <option value="">— Pilih Invoice —</option>
                    @foreach($invoices as $inv)
                    <option value="{{ $inv->id }}" {{ old('invoice_id', $invoice?->id)==$inv->id?'selected':'' }}>
                        {{ $inv->invoice_number }} — {{ $inv->tenant?->full_name }} ({{ $inv->period_label }}) — Rp {{ number_format($inv->total,0,',','.') }}
                    </option>
                    @endforeach
                </select>

                {{-- Invoice Preview --}}
                <template x-for="inv in invoices.filter(i => i.id == selectedInv)" :key="inv.id">
                    <div class="mt-3 p-4 bg-sky-kos/5 border border-sky-kos/20 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-slate-700" x-text="inv.tenant?.full_name"></p>
                                <p class="text-sm text-slate-500" x-text="inv.period_label + ' — Jatuh tempo: ' + inv.due_date"></p>
                            </div>
                            <p class="font-bold text-sky-dark" x-text="'Rp ' + Number(inv.total).toLocaleString('id-ID')"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Detail Pembayaran</h3></div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Jumlah Dibayar (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" class="form-input" placeholder="800000" value="{{ old('amount') }}" required min="1">
                </div>
                <div>
                    <label class="form-label">Tanggal Pembayaran <span class="text-red-500">*</span></label>
                    <input type="date" name="payment_date" class="form-input" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                </div>
                <div>
                    <label class="form-label">Metode Pembayaran <span class="text-red-500">*</span></label>
                    <select name="payment_method" class="form-input" required>
                        <option value="">— Pilih —</option>
                        @foreach(['Transfer Bank','BCA','BRI','BNI','Mandiri','GoPay','OVO','QRIS','Cash'] as $m)
                        <option value="{{ $m }}" {{ old('payment_method')===$m?'selected':'' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Bukti Pembayaran</label>
                    <label for="proofInput"
                           id="proofZone"
                           class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 rounded-xl p-5 text-center cursor-pointer hover:border-sky-kos hover:bg-sky-kos/5 transition-colors min-h-[100px]">
                        {{-- State default --}}
                        <div id="proofDefault">
                            <svg class="w-7 h-7 text-slate-300 mx-auto mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm text-slate-500">Klik untuk upload bukti</p>
                            <p class="text-xs text-slate-400 mt-0.5">JPG / PNG / PDF, maks 2 MB</p>
                        </div>
                        {{-- Preview gambar --}}
                        <img id="proofImgPreview" src="" alt="Preview"
                             class="hidden max-h-28 rounded-lg object-contain mb-1.5">
                        {{-- Preview PDF --}}
                        <div id="proofPdfPreview" class="hidden items-center gap-2">
                            <svg class="w-8 h-8 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span id="proofPdfName" class="text-sm font-medium text-slate-700 truncate max-w-[160px]"></span>
                        </div>
                        {{-- Info ukuran file --}}
                        <p id="proofFileInfo" class="hidden text-xs text-green-600 font-medium mt-1"></p>
                    </label>
                    <input type="file" id="proofInput" name="proof_of_payment"
                           accept="image/jpeg,image/png,image/webp,application/pdf" class="sr-only">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-input" rows="2" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('payments.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Pembayaran
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const MAX_SIZE      = 2 * 1024 * 1024;
const ALLOWED_IMG   = ['image/jpeg', 'image/png', 'image/webp'];
const ALLOWED_ALL   = [...ALLOWED_IMG, 'application/pdf'];

const proofInput      = document.getElementById('proofInput');
const proofDefault    = document.getElementById('proofDefault');
const proofImgPreview = document.getElementById('proofImgPreview');
const proofPdfPreview = document.getElementById('proofPdfPreview');
const proofPdfName    = document.getElementById('proofPdfName');
const proofFileInfo   = document.getElementById('proofFileInfo');

function resetProof() {
    proofDefault.classList.remove('hidden');
    proofImgPreview.classList.add('hidden');
    proofPdfPreview.classList.add('hidden');
    proofFileInfo.classList.add('hidden');
    proofImgPreview.src = '';
}

proofInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) { resetProof(); return; }

    if (!ALLOWED_ALL.includes(file.type)) {
        alert('"' + file.name + '" tidak dapat diupload.\nFormat tidak didukung — gunakan JPG, PNG, WebP, atau PDF.');
        this.value = '';
        resetProof();
        return;
    }

    if (file.size > MAX_SIZE) {
        alert('"' + file.name + '" tidak dapat diupload.\nUkuran file ' + (file.size / 1024 / 1024).toFixed(1) + ' MB melebihi batas 2 MB.');
        this.value = '';
        resetProof();
        return;
    }

    proofDefault.classList.add('hidden');
    proofFileInfo.textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
    proofFileInfo.classList.remove('hidden');

    if (file.type === 'application/pdf') {
        proofPdfName.textContent = file.name;
        proofPdfPreview.classList.remove('hidden');
        proofPdfPreview.classList.add('flex');
    } else {
        const reader = new FileReader();
        reader.onload = function (e) {
            proofImgPreview.src = e.target.result;
            proofImgPreview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
