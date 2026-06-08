<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1E293B; }
        .header { background: #0F172A; color: white; padding: 16px 20px; }
        .title { font-size: 16px; font-weight: bold; }
        .title span { color: #38BDF8; }
        .subtitle { color: #94A3B8; font-size: 10px; margin-top: 4px; }
        .content { padding: 16px; }
        .meta { margin-bottom: 14px; background: #F8FAFC; padding: 10px; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #0F172A; color: #F1F5F9; text-align: left; padding: 8px; font-size: 9px; text-transform: uppercase; }
        td { padding: 7px 8px; border-bottom: 1px solid #F1F5F9; font-size: 10px; }
        tr:nth-child(even) td { background: #F8FAFC; }
        .total-row { background: #E0F2FE !important; }
        .total-row td { font-weight: bold; border-top: 2px solid #38BDF8; }
        .amount { text-align: right; }
        .footer { margin-top: 16px; text-align: center; font-size: 9px; color: #94A3B8; }
    </style>
</head>
<body>
<div class="header">
    <div class="title">Si<span>ManKos</span> — Laporan Pembayaran</div>
    <div class="subtitle">Periode: {{ $period }}</div>
</div>

<div class="content">
    <div class="meta">
        <strong>Total Transaksi:</strong> {{ $invoices->count() }} &nbsp;&nbsp;
        <strong>Total Pendapatan:</strong> Rp {{ number_format($total, 0, ',', '.') }} &nbsp;&nbsp;
        <strong>Cetak:</strong> {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Invoice</th>
                <th>Penyewa</th>
                <th>Kamar</th>
                <th>Periode</th>
                <th>Tgl Bayar</th>
                <th class="amount">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $i => $invoice)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->tenant?->full_name }}</td>
                <td>{{ $invoice->rental?->room?->room_number }}</td>
                <td>{{ $invoice->period_label }}</td>
                <td>{{ $invoice->paid_date?->format('d/m/Y') }}</td>
                <td class="amount">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6">TOTAL KESELURUHAN</td>
                <td class="amount">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="footer">SiManKos &copy; {{ date('Y') }} — Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
