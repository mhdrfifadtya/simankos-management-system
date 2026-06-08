<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1E293B; }
        .header { background: #0F172A; color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 20px; font-weight: bold; }
        .logo span { color: #38BDF8; }
        .tagline { font-size: 10px; color: #94A3B8; margin-top: 2px; }
        .invoice-meta { text-align: right; }
        .invoice-number { font-size: 14px; font-weight: bold; color: #38BDF8; }
        .content { padding: 20px; }
        .section { margin-bottom: 16px; }
        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #64748B; letter-spacing: 0.05em; border-bottom: 1px solid #E2E8F0; padding-bottom: 4px; margin-bottom: 8px; }
        .grid-2 { display: table; width: 100%; }
        .col { display: table-cell; width: 50%; vertical-align: top; padding-right: 16px; }
        .label { color: #64748B; font-size: 10px; }
        .value { font-weight: 600; font-size: 11px; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #F1F5F9; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; color: #64748B; }
        td { padding: 8px 10px; border-bottom: 1px solid #F1F5F9; font-size: 11px; }
        .amount { text-align: right; }
        .total-row td { font-weight: bold; background: #F8FAFC; border-top: 2px solid #E2E8F0; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: bold; }
        .status-paid { background: #DCFCE7; color: #15803D; }
        .status-overdue { background: #FEE2E2; color: #DC2626; }
        .status-pending { background: #FEF3C7; color: #D97706; }
        .status-draft { background: #F1F5F9; color: #64748B; }
        .footer { background: #F8FAFC; border-top: 2px solid #E2E8F0; padding: 12px 20px; text-align: center; font-size: 10px; color: #94A3B8; margin-top: 20px; }
    </style>
</head>
<body>
<div class="header">
    <div>
        <div class="logo">Si<span>ManKos</span></div>
        <div class="tagline">Kelola Kos, Lebih Mudah.</div>
    </div>
    <div class="invoice-meta">
        <div class="invoice-number">{{ $invoice->invoice_number }}</div>
        <div style="color: #94A3B8; font-size: 10px; margin-top: 4px;">TAGIHAN SEWA KOS</div>
        @php
            $statusClass = match($invoice->status) { 'paid' => 'status-paid', 'overdue' => 'status-overdue', 'pending' => 'status-pending', default => 'status-draft' };
        @endphp
        <span class="status-badge {{ $statusClass }}">{{ strtoupper($invoice->status) }}</span>
    </div>
</div>

<div class="content">
    <div class="section">
        <div class="grid-2">
            <div class="col">
                <div class="section-title">Data Penyewa</div>
                <div class="label">Nama Lengkap</div>
                <div class="value">{{ $invoice->tenant?->full_name }}</div>
                <div class="label" style="margin-top: 8px;">No. WhatsApp</div>
                <div class="value">{{ $invoice->tenant?->whatsapp_number }}</div>
            </div>
            <div class="col">
                <div class="section-title">Detail Kamar</div>
                <div class="label">Kamar</div>
                <div class="value">{{ $invoice->rental?->room?->room_number }} ({{ $invoice->rental?->room?->type }})</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Detail Tagihan</div>
        <table>
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th>Periode</th>
                    <th>Jatuh Tempo</th>
                    <th class="amount">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sewa Kamar</td>
                    <td>{{ $invoice->period_label }}</td>
                    <td>{{ $invoice->due_date?->format('d/m/Y') }}</td>
                    <td class="amount">Rp {{ number_format($invoice->rent_amount, 0, ',', '.') }}</td>
                </tr>
                @if($invoice->extra_charges > 0)
                <tr>
                    <td>Biaya Tambahan</td>
                    <td colspan="2"></td>
                    <td class="amount">Rp {{ number_format($invoice->extra_charges, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="3">TOTAL</td>
                    <td class="amount">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($invoice->payments->isNotEmpty())
    <div class="section">
        <div class="section-title">Riwayat Pembayaran</div>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th class="amount">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $payment)
                <tr>
                    <td>{{ $payment->payment_date?->format('d/m/Y') }}</td>
                    <td>{{ $payment->payment_method }}</td>
                    <td>{{ str_replace('_', ' ', $payment->status) }}</td>
                    <td class="amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<div class="footer">
    Dicetak pada {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }} &mdash;
    SiManKos &copy; {{ date('Y') }}
</div>
</body>
</html>
