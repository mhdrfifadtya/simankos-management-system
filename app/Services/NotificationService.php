<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\NotificationLog;
use App\Models\ReminderLog;
use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    private string $token;
    private string $url;

    public function __construct()
    {
        $this->token = config('services.fonnte.token', '');
        $this->url = config('services.fonnte.url', 'https://api.fonnte.com/send');
    }

    public function sendWhatsApp(string $target, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->url, [
                'target' => $target,
                'message' => $message,
            ]);

            $body = $response->json();

            return [
                'success' => $response->successful() && ($body['status'] ?? false),
                'response' => $body,
            ];
        } catch (\Exception $e) {
            Log::error('Fonnte WA error: ' . $e->getMessage());
            return ['success' => false, 'response' => ['error' => $e->getMessage()]];
        }
    }

    public function sendPaymentReminder(Invoice $invoice, string $type): bool
    {
        $tenant = $invoice->tenant;
        if (!$tenant || !$tenant->notify_via_whatsapp) {
            return false;
        }

        $target = $tenant->whatsapp_number;
        if (!$target) {
            return false;
        }

        $message = $this->buildReminderMessage($invoice, $type);
        $result = $this->sendWhatsApp($target, $message);

        $status = $result['success'] ? 'terkirim' : 'gagal';

        ReminderLog::create([
            'invoice_id' => $invoice->id,
            'tenant_id' => $tenant->id,
            'channel' => 'whatsapp',
            'type' => $type,
            'status' => $status,
            'message' => $message,
            'recipient' => $target,
            'response' => json_encode($result['response']),
        ]);

        NotificationLog::create([
            'tenant_id' => $tenant->id,
            'whatsapp_number' => $target,
            'type' => $type,
            'message' => $message,
            'status' => $status,
            'gateway_response' => json_encode($result['response']),
            'sent_at' => now(),
        ]);

        return $result['success'];
    }

    public function sendPaymentConfirmation(Invoice $invoice): bool
    {
        $tenant = $invoice->tenant;
        if (!$tenant || !$tenant->notify_via_whatsapp || !$tenant->whatsapp_number) {
            return false;
        }

        $message = $this->buildConfirmationMessage($invoice);
        $result = $this->sendWhatsApp($tenant->whatsapp_number, $message);

        NotificationLog::create([
            'tenant_id' => $tenant->id,
            'whatsapp_number' => $tenant->whatsapp_number,
            'type' => 'payment_confirmed',
            'message' => $message,
            'status' => $result['success'] ? 'terkirim' : 'gagal',
            'gateway_response' => json_encode($result['response']),
            'sent_at' => now(),
        ]);

        return $result['success'];
    }

    public function hasReminderSentToday(Invoice $invoice, string $type): bool
    {
        return ReminderLog::where('invoice_id', $invoice->id)
            ->where('type', $type)
            ->where('status', 'terkirim')
            ->whereDate('created_at', today())
            ->exists();
    }

    public function testConnection(): array
    {
        return $this->sendWhatsApp('628123456789', 'Test koneksi SiManKos - ' . now()->format('d/m/Y H:i'));
    }

    private function buildReminderMessage(Invoice $invoice, string $type): string
    {
        $tenant = $invoice->tenant;
        $room = $invoice->rental?->room;
        $dueDate = $invoice->due_date->format('d/m/Y');
        $amount = number_format($invoice->total, 0, ',', '.');
        $period = $invoice->period_label;

        $templates = [
            'h7'      => "Halo *[NAMA]* 👋\n\nIni pengingat bahwa tagihan sewa kamar *[KAMAR]* periode *[PERIODE]* sebesar *Rp [JUMLAH]* akan jatuh tempo pada *[TANGGAL]* (7 hari lagi).\n\nMohon siapkan pembayarannya ya. Terima kasih! 🙏",
            'h3'      => "Halo *[NAMA]* 👋\n\nMengingatkan kembali, tagihan sewa kamar *[KAMAR]* periode *[PERIODE]* sebesar *Rp [JUMLAH]* jatuh tempo pada *[TANGGAL]* (3 hari lagi).\n\nSilakan lakukan pembayaran. Terima kasih! 💸",
            'h1'      => "⚠️ *PENGINGAT PEMBAYARAN* ⚠️\n\nHalo *[NAMA]*,\nTagihan sewa kamar *[KAMAR]* periode *[PERIODE]* sebesar *Rp [JUMLAH]* jatuh tempo *BESOK* (*[TANGGAL]*).\n\nMohon segera lunasi. Terima kasih! 🙏",
            'overdue' => "🚨 *TAGIHAN TERLAMBAT* 🚨\n\nHalo *[NAMA]*,\nTagihan sewa kamar *[KAMAR]* periode *[PERIODE]* sebesar *Rp [JUMLAH]* *TELAH MELEWATI BATAS WAKTU* (*[TANGGAL]*).\n\nMohon segera lakukan pembayaran. Terima kasih.",
            'default' => "🔔 *PENGINGAT PEMBAYARAN*\n\nHalo *[NAMA]*,\nTagihan sewa kamar *[KAMAR]* periode *[PERIODE]* sebesar *Rp [JUMLAH]* jatuh tempo pada *[TANGGAL]*.\n\nTerima kasih.",
        ];

        $template = $templates[$type] ?? $templates['default'];

        return str_replace(
            ['[NAMA]', '[KAMAR]', '[PERIODE]', '[JUMLAH]', '[TANGGAL]'],
            [$tenant->full_name, $room?->room_number ?? '-', $period, $amount, $dueDate],
            $template
        );
    }

    private function buildConfirmationMessage(Invoice $invoice): string
    {
        $tenant = $invoice->tenant;
        $amount = number_format($invoice->total, 0, ',', '.');
        $period = $invoice->period_label;

        return "Halo *{$tenant->full_name}*,\n\n✅ Pembayaran sewa kos Anda untuk periode *{$period}* sebesar *Rp {$amount}* telah *TERKONFIRMASI*.\n\nTerima kasih atas pembayaran Anda.\n\n*SiManKos*";
    }
}
