@extends('layouts.app')
@section('title','Notifikasi & WhatsApp')
@section('page-title','Notifikasi & WhatsApp')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- Config & Test --}}
    <div class="space-y-5">
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="font-semibold text-slate-700">Pengaturan Reminder</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('notifications.settings') }}" method="POST">
                    @csrf
                    <div class="space-y-3 mb-5">
                        @foreach([
                            ['key'=>'reminder_h7','label'=>'H-7 (7 hari sebelum JT)','desc'=>'Kirim 7 hari sebelum jatuh tempo'],
                            ['key'=>'reminder_h3','label'=>'H-3 (3 hari sebelum JT)','desc'=>'Kirim 3 hari sebelum jatuh tempo'],
                            ['key'=>'reminder_h1','label'=>'H-1 (1 hari sebelum JT)','desc'=>'Kirim 1 hari sebelum jatuh tempo'],
                            ['key'=>'reminder_overdue','label'=>'Overdue (Terlambat)','desc'=>'Kirim saat sudah melewati jatuh tempo'],
                        ] as $item)
                        <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-colors {{ $settings[$item['key']] ? 'border-sky-kos/30 bg-sky-kos/5' : 'border-slate-100 hover:border-sky-kos/20' }}">
                            <input type="checkbox" name="{{ $item['key'] }}" value="1" {{ $settings[$item['key']]?'checked':'' }} class="mt-0.5 rounded border-slate-300 text-sky-kos">
                            <div>
                                <p class="text-sm font-semibold text-slate-700">{{ $item['label'] }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $item['desc'] }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-primary w-full">Simpan Pengaturan</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Tes Koneksi Fonnte</h3></div>
            <div class="card-body">
                <p class="text-sm text-slate-500 mb-4">Kirim pesan tes untuk memverifikasi koneksi ke WhatsApp Gateway.</p>
                <button onclick="testFonnte(this)" class="btn btn-secondary w-full" id="testBtn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Test Koneksi WA
                </button>
                <div id="testResult" class="mt-3 hidden"></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-slate-700">Kirim Pesan Manual</h3></div>
            <div class="card-body">
                <form action="{{ route('notifications.send-manual') }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="form-label">Penyewa</label>
                            <select name="tenant_id" id="tenantSelect" class="form-input" required onchange="onTenantChange(this.value)">
                                <option value="">— Pilih Penyewa —</option>
                                @foreach(\App\Models\Tenant::where('status','aktif')->get() as $t)
                                <option value="{{ $t->id }}">{{ $t->full_name }} ({{ $t->whatsapp_number }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Gunakan Template</label>
                            <select id="templateSelect" class="form-input" onchange="applyTemplate(this.value)">
                                <option value="">— Tulis sendiri —</option>
                                <option value="default">Default / Umum</option>
                                <option value="h7">H-7 (7 hari sebelum JT)</option>
                                <option value="h3">H-3 (3 hari sebelum JT)</option>
                                <option value="h1">H-1 (besok JT)</option>
                                <option value="overdue">Overdue (terlambat)</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Pesan</label>
                            <textarea id="manualMessage" name="message" class="form-input font-mono text-xs" rows="6" placeholder="Tulis pesan WhatsApp atau pilih template di atas..." required></textarea>
                            <p class="text-xs text-slate-400 mt-1">Pilih penyewa terlebih dahulu agar variabel template terisi otomatis.</p>
                        </div>
                        <button type="submit" class="btn btn-primary w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            Kirim Pesan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Template Reference --}}
        <div class="card">
            <div class="card-header">
                <button type="button" onclick="toggleTemplates()" class="w-full flex items-center justify-between text-left">
                    <h3 class="font-semibold text-slate-700">Referensi Template Pesan</h3>
                    <svg id="templateChevron" class="w-4 h-4 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>
            <div id="templateList" class="hidden">
                <div class="card-body space-y-3 pt-0">
                    @foreach([
                        ['key'=>'default','label'=>'Default / Umum','color'=>'badge-gray'],
                        ['key'=>'h7','label'=>'H-7','color'=>'badge-navy'],
                        ['key'=>'h3','label'=>'H-3','color'=>'badge-blue'],
                        ['key'=>'h1','label'=>'H-1','color'=>'badge-sky'],
                        ['key'=>'overdue','label'=>'Overdue','color'=>'badge-red'],
                    ] as $tpl)
                    <div class="rounded-xl border border-slate-100 p-3">
                        <span class="badge {{ $tpl['color'] }} mb-2">{{ $tpl['label'] }}</span>
                        <pre id="tpl_{{ $tpl['key'] }}" class="text-xs text-slate-600 whitespace-pre-wrap font-mono leading-relaxed"></pre>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Log Table --}}
    <div class="xl:col-span-2">
        <div class="card overflow-hidden">
            <div class="card-header flex items-center justify-between">
                <h3 class="font-semibold text-slate-700">Log Notifikasi Terkirim</h3>
                <span class="text-xs text-slate-400">{{ $logs->total() }} total</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Penyewa</th><th>Tipe</th><th>Waktu Kirim</th><th class="text-center">Status</th></tr></thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>
                                <p class="font-medium text-slate-700 text-sm">{{ $log->tenant?->full_name ?? '—' }}</p>
                                <p class="text-xs text-slate-400">{{ $log->whatsapp_number }}</p>
                            </td>
                            <td>
                                @php $tc=['h7'=>'badge-navy','h3'=>'badge-blue','h1'=>'badge-sky','overdue'=>'badge-red','manual'=>'badge-gray','payment_confirmed'=>'badge-green']; @endphp
                                <span class="badge {{ $tc[$log->type]??'badge-gray' }}">{{ $log->type }}</span>
                            </td>
                            <td class="text-slate-500 text-sm">
                                {{ $log->sent_at ? $log->sent_at->format('d/m/Y H:i') : ($log->created_at?->format('d/m/Y H:i') ?? '—') }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $log->status==='terkirim'?'badge-green':($log->status==='gagal'?'badge-red':'badge-yellow') }}">{{ $log->status }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-12 text-slate-400">Belum ada log notifikasi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-50">{{ $logs->links() }}</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const WA_TEMPLATES = {
    default: "🔔 *PENGINGAT PEMBAYARAN*\n\nHalo *[NAMA]*,\nTagihan sewa kamar *[KAMAR]* periode *[PERIODE]* sebesar *Rp [JUMLAH]* jatuh tempo pada *[TANGGAL]*.\n\nTerima kasih.",
    h7:      "Halo *[NAMA]* 👋\n\nIni pengingat bahwa tagihan sewa kamar *[KAMAR]* periode *[PERIODE]* sebesar *Rp [JUMLAH]* akan jatuh tempo pada *[TANGGAL]* (7 hari lagi).\n\nMohon siapkan pembayarannya ya. Terima kasih! 🙏",
    h3:      "Halo *[NAMA]* 👋\n\nMengingatkan kembali, tagihan sewa kamar *[KAMAR]* periode *[PERIODE]* sebesar *Rp [JUMLAH]* jatuh tempo pada *[TANGGAL]* (3 hari lagi).\n\nSilakan lakukan pembayaran. Terima kasih! 💸",
    h1:      "⚠️ *PENGINGAT PEMBAYARAN* ⚠️\n\nHalo *[NAMA]*,\nTagihan sewa kamar *[KAMAR]* periode *[PERIODE]* sebesar *Rp [JUMLAH]* jatuh tempo *BESOK* (*[TANGGAL]*).\n\nMohon segera lunasi. Terima kasih! 🙏",
    overdue: "🚨 *TAGIHAN TERLAMBAT* 🚨\n\nHalo *[NAMA]*,\nTagihan sewa kamar *[KAMAR]* periode *[PERIODE]* sebesar *Rp [JUMLAH]* *TELAH MELEWATI BATAS WAKTU* (*[TANGGAL]*).\n\nMohon segera lakukan pembayaran. Terima kasih.",
};

let tenantData = null;

// Isi konten referensi template
document.addEventListener('DOMContentLoaded', function () {
    Object.keys(WA_TEMPLATES).forEach(function (key) {
        const el = document.getElementById('tpl_' + key);
        if (el) el.textContent = WA_TEMPLATES[key];
    });
});

function onTenantChange(tenantId) {
    tenantData = null;
    const templateKey = document.getElementById('templateSelect').value;
    if (!tenantId) {
        if (templateKey) applyTemplate(templateKey);
        return;
    }
    const url = '{{ route("notifications.tenant-data", ["tenant" => "__ID__"]) }}'.replace('__ID__', tenantId);
    fetch(url, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
    })
    .then(r => r.json())
    .then(data => {
        tenantData = data;
        if (templateKey) applyTemplate(templateKey);
    });
}

function fillPlaceholders(text) {
    if (!tenantData) return text;
    return text
        .replace(/\[NAMA\]/g,    tenantData.nama)
        .replace(/\[KAMAR\]/g,   tenantData.kamar)
        .replace(/\[PERIODE\]/g, tenantData.periode)
        .replace(/\[JUMLAH\]/g,  tenantData.jumlah)
        .replace(/\[TANGGAL\]/g, tenantData.tanggal);
}

function applyTemplate(key) {
    const textarea = document.getElementById('manualMessage');
    if (!textarea) return;
    const raw = key ? WA_TEMPLATES[key] ?? '' : '';
    textarea.value = fillPlaceholders(raw);
}

function toggleTemplates() {
    const list = document.getElementById('templateList');
    const chevron = document.getElementById('templateChevron');
    list.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180');
}

function testFonnte(btn) {
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Testing...';
    fetch('{{ route("notifications.test") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const el = document.getElementById('testResult');
        el.className = 'mt-3 p-3 rounded-lg text-sm font-medium ' + (data.success ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200');
        el.textContent = data.success ? '✓ Koneksi berhasil! Pesan tes terkirim.' : '✗ Gagal: ' + (data.response?.reason ?? 'Token tidak valid');
        el.classList.remove('hidden');
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Test Koneksi WA';
    })
    .catch(() => { btn.disabled = false; });
}
</script>
@endpush
