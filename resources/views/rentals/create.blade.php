@extends('layouts.app')
@section('title','Buat Kontrak Baru')
@section('page-title','Buat Kontrak Baru')

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('rentals.index') }}" class="btn btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    <div class="alert alert-info mb-5">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Setelah kontrak dibuat, status kamar otomatis berubah menjadi <strong>Terisi</strong> dan <strong>semua invoice</strong> untuk periode kontrak akan otomatis di-generate.</span>
    </div>

    <form action="{{ route('rentals.store') }}" method="POST" id="rentalForm" class="space-y-5">
        @csrf

        {{-- ─── Penyewa & Kamar ─── --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Pilih Penyewa & Kamar</h3></div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Penyewa <span class="text-red-500">*</span></label>
                    <select name="tenant_id" id="tenantSelect" class="form-input" required>
                        <option value="">— Pilih Penyewa —</option>
                        @foreach($tenants as $t)
                        <option value="{{ $t->id }}" {{ old('tenant_id')==$t->id?'selected':'' }}>{{ $t->full_name }} ({{ $t->whatsapp_number }})</option>
                        @endforeach
                    </select>
                    <p class="form-hint">Hanya penyewa tanpa kontrak aktif yang ditampilkan</p>
                </div>
                <div>
                    <label class="form-label">Kamar <span class="text-red-500">*</span></label>
                    <select name="room_id" id="roomSelect" class="form-input" required>
                        <option value="">— Pilih Kamar —</option>
                        @foreach($rooms as $r)
                        <option value="{{ $r->id }}" data-price="{{ $r->price }}"
                                {{ old('room_id')==$r->id?'selected':'' }}>
                            {{ $r->room_number }} — {{ ucfirst($r->type) }} (Rp {{ number_format($r->price,0,',','.') }}/bln)
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Room preview --}}
            <div id="roomPreview" class="px-5 pb-5 hidden">
                <div class="bg-sky-kos/5 border border-sky-kos/20 rounded-xl p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-sky-kos/20 flex items-center justify-center text-sky-dark font-extrabold text-lg flex-shrink-0">K</div>
                    <div>
                        <p id="roomPreviewName" class="font-semibold text-slate-700"></p>
                        <p id="roomPreviewPrice" class="text-sm text-sky-dark font-bold"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── Detail Kontrak ─── --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Detail Kontrak</h3></div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="startDate" class="form-input"
                           value="{{ old('start_date', date('Y-m-d')) }}" required>
                </div>
                <div>
                    <label class="form-label">Tanggal Berakhir</label>
                    <input type="date" name="end_date" id="endDate" class="form-input"
                           value="{{ old('end_date') }}">
                    <p class="form-hint" id="endDateHint">Kosongkan untuk kontrak tidak tentu</p>
                </div>

                <div>
                    <label class="form-label">Harga Sewa / Bulan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="monthly_rent" id="monthlyRent" class="form-input"
                           value="{{ old('monthly_rent') }}" placeholder="Akan terisi otomatis" required min="0">
                    <p class="form-hint" id="yearlyHint" style="display:none">
                        Total tahunan: <strong id="yearlyTotal" class="text-slate-700"></strong>
                    </p>
                </div>
                <div>
                    <label class="form-label">Deposit (Rp)</label>
                    <input type="number" name="deposit" class="form-input"
                           value="{{ old('deposit', 0) }}" min="0">
                </div>

                <div>
                    <label class="form-label">Metode Pembayaran <span class="text-red-500">*</span></label>
                    <select name="method" id="methodSelect" class="form-input">
                        <option value="monthly" {{ old('method','monthly')==='monthly'?'selected':'' }}>Bulanan</option>
                        <option value="yearly"  {{ old('method')==='yearly'?'selected':'' }}>Tahunan</option>
                        <option value="custom"  {{ old('method')==='custom'?'selected':'' }}>Custom</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Tanggal Jatuh Tempo <span class="text-red-500">*</span></label>
                    <input type="number" name="due_date_day" id="dueDateDay" class="form-input"
                           value="{{ old('due_date_day', 5) }}" min="1" max="28" required>
                    <p class="form-hint">Tanggal berapa setiap bulan (1–28)</p>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-input" rows="2"
                              placeholder="Catatan kontrak...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ─── Invoice Preview ─── --}}
        <div id="invoicePreviewCard" class="card hidden">
            <div class="card-header flex items-center justify-between">
                <h3 class="font-semibold text-slate-700">Jadwal Invoice</h3>
                <span id="invoiceCount" class="badge badge-sky text-xs"></span>
            </div>
            <div class="card-body">
                <p class="text-xs text-slate-400 mb-3">Invoice berikut akan otomatis di-generate saat kontrak dibuat.</p>
                <div id="invoiceList" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2"></div>
                <div id="invoiceMore" class="hidden mt-2 text-xs text-slate-400 text-center"></div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('rentals.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" id="submitBtn" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Buat Kontrak
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const rooms = @json($rooms->keyBy('id'));

const roomSelect    = document.getElementById('roomSelect');
const methodSelect  = document.getElementById('methodSelect');
const startDateEl   = document.getElementById('startDate');
const endDateEl     = document.getElementById('endDate');
const monthlyRentEl = document.getElementById('monthlyRent');
const dueDateDayEl  = document.getElementById('dueDateDay');

// ── Auto-fill monthly rent when room is selected ──────────────────────────
roomSelect.addEventListener('change', function () {
    const room = rooms[this.value];
    if (!room) {
        document.getElementById('roomPreview').classList.add('hidden');
        return;
    }

    // Fill price
    monthlyRentEl.value = room.price;

    // Show preview
    document.getElementById('roomPreviewName').textContent  = 'Kamar ' + room.room_number + ' — ' + cap(room.type);
    document.getElementById('roomPreviewPrice').textContent = 'Rp ' + fmt(room.price) + '/bulan';
    document.getElementById('roomPreview').classList.remove('hidden');

    syncYearly();
    buildInvoicePreview();
});

// ── Sync end_date & hints based on payment method ────────────────────────
methodSelect.addEventListener('change', syncMethod);
monthlyRentEl.addEventListener('input', syncMethod);
startDateEl.addEventListener('change', function () { syncMethod(); buildInvoicePreview(); });
endDateEl.addEventListener('change', buildInvoicePreview);
dueDateDayEl.addEventListener('change', buildInvoicePreview);

function setEndDateLock(locked) {
    endDateEl.readOnly = locked;
    endDateEl.classList.toggle('bg-slate-50',        locked);
    endDateEl.classList.toggle('text-slate-400',     locked);
    endDateEl.classList.toggle('cursor-not-allowed', locked);
}

function syncMethod() {
    const method    = methodSelect.value;
    const price     = parseFloat(monthlyRentEl.value) || 0;
    const yearlyEl  = document.getElementById('yearlyHint');
    const yearlyTxt = document.getElementById('yearlyTotal');
    const hintEl    = document.getElementById('endDateHint');

    if (method === 'monthly') {
        // Bulanan = start + 1 bulan − 1 hari, field tetap bisa diedit
        if (startDateEl.value) {
            const d = new Date(startDateEl.value);
            d.setMonth(d.getMonth() + 1);
            d.setDate(d.getDate() - 1);
            endDateEl.value = d.toISOString().split('T')[0];
        }
        setEndDateLock(false);
        yearlyEl.style.display = 'none';
        hintEl.textContent = 'Otomatis 1 bulan — bebas diubah';
        buildInvoicePreview();

    } else if (method === 'yearly') {
        // Tahunan = start + 12 bulan − 1 hari, field dikunci
        if (startDateEl.value) {
            const d = new Date(startDateEl.value);
            d.setMonth(d.getMonth() + 12);
            d.setDate(d.getDate() - 1);
            endDateEl.value = d.toISOString().split('T')[0];
            buildInvoicePreview();
        }
        setEndDateLock(true);
        yearlyTxt.textContent = 'Rp ' + fmt(price * 12);
        yearlyEl.style.display = 'block';
        hintEl.textContent = 'Otomatis: tanggal mulai + 12 bulan − 1 hari';

    } else {
        // Custom = field bebas, tidak dikosongkan
        setEndDateLock(false);
        yearlyEl.style.display = 'none';
        hintEl.textContent = 'Isi sesuai perjanjian kontrak';
    }
}

// ── Invoice schedule preview ──────────────────────────────────────────────
function buildInvoicePreview() {
    const startVal = startDateEl.value;
    const endVal   = endDateEl.value;
    const card     = document.getElementById('invoicePreviewCard');
    const list     = document.getElementById('invoiceList');
    const countEl  = document.getElementById('invoiceCount');
    const moreEl   = document.getElementById('invoiceMore');
    const dayVal   = parseInt(dueDateDayEl.value) || 5;
    const rentVal  = parseFloat(monthlyRentEl.value) || 0;

    if (!startVal || !endVal) {
        card.classList.add('hidden');
        return;
    }

    const start = new Date(startVal);
    const end   = new Date(endVal);

    if (end <= start) {
        card.classList.add('hidden');
        return;
    }

    const months = [];
    const cur = new Date(start.getFullYear(), start.getMonth(), 1);
    // Exclude end month when end_date.day < start_date.day
    // e.g. start=5/6, end=4/7 → fin stays on June (1 invoice, not 2)
    const fin = new Date(end.getFullYear(), end.getMonth(), 1);
    if (end.getDate() < start.getDate()) fin.setMonth(fin.getMonth() - 1);

    while (cur <= fin) {
        months.push(new Date(cur));
        cur.setMonth(cur.getMonth() + 1);
    }

    const SHOW_MAX = 12;
    countEl.textContent = months.length + ' invoice';
    list.innerHTML = '';

    const locale = 'id-ID';
    months.slice(0, SHOW_MAX).forEach((m, i) => {
        const label = m.toLocaleDateString(locale, { month: 'short', year: 'numeric' });
        const due   = new Date(m.getFullYear(), m.getMonth(), Math.min(dayVal, 28));
        const dueStr = due.toLocaleDateString(locale, { day: 'numeric', month: 'short' });

        const div = document.createElement('div');
        div.className = 'flex flex-col gap-0.5 bg-slate-50 border border-slate-100 rounded-lg px-3 py-2';
        div.innerHTML = `
            <span class="text-xs font-semibold text-slate-700">${label}</span>
            <span class="text-xs text-slate-400">Jatuh tempo: ${dueStr}</span>
            ${rentVal ? `<span class="text-xs font-bold text-sky-dark">Rp ${fmt(rentVal)}</span>` : ''}
        `;
        list.appendChild(div);
    });

    if (months.length > SHOW_MAX) {
        moreEl.textContent = '+ ' + (months.length - SHOW_MAX) + ' invoice berikutnya...';
        moreEl.classList.remove('hidden');
    } else {
        moreEl.classList.add('hidden');
    }

    card.classList.remove('hidden');
}

// ── Submit guard ──────────────────────────────────────────────────────────
document.getElementById('rentalForm').addEventListener('submit', function (e) {
    const room   = roomSelect.value;
    const tenant = document.getElementById('tenantSelect').value;
    const start  = startDateEl.value;
    const rent   = monthlyRentEl.value;

    if (!room || !tenant || !start || !rent) {
        e.preventDefault();
        siToast('Pastikan Penyewa, Kamar, Tanggal Mulai, dan Harga Sewa sudah diisi.', 'warning', 'Form Belum Lengkap');
        return;
    }

    const endVal = endDateEl.value;
    if (endVal && new Date(endVal) <= new Date(start)) {
        e.preventDefault();
        siToast('Tanggal berakhir harus setelah tanggal mulai.', 'error', 'Tanggal Tidak Valid');
        return;
    }

    // Show loading state
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Memproses...`;
});

// ── Helpers ───────────────────────────────────────────────────────────────
function fmt(n) { return Number(n).toLocaleString('id-ID'); }
function cap(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

// Init on page load (in case old() repopulates values)
(function init() {
    if (roomSelect.value) roomSelect.dispatchEvent(new Event('change'));
    syncMethod();
    if (startDateEl.value && endDateEl.value) buildInvoicePreview();
})();
</script>
@endpush
