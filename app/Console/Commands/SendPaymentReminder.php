<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Setting;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendPaymentReminder extends Command
{
    protected $signature = 'notify:payment-reminder {--dry-run : Preview without sending}';
    protected $description = 'Kirim notifikasi WhatsApp reminder pembayaran otomatis';

    public function __construct(private NotificationService $notificationService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $startTime = microtime(true);
        $isDryRun = $this->option('dry-run');

        $this->info('SiManKos — Payment Reminder Scheduler');
        $this->info('Tanggal: ' . now()->format('d/m/Y H:i:s'));

        try {
            $sentCount = 0;
            $skippedCount = 0;
            $failedCount = 0;

            $invoices = Invoice::query()
                ->whereNotIn('status', ['paid', 'draft'], 'and')
                ->with(['tenant', 'rental.room'])
                ->get();

            $this->info("Total invoice aktif: {$invoices->count()}");

            $enabledTypes = [
                'h7'      => Setting::get('reminder_h7', true),
                'h3'      => Setting::get('reminder_h3', true),
                'h1'      => Setting::get('reminder_h1', true),
                'overdue' => Setting::get('reminder_overdue', true),
            ];

            foreach ($invoices as $invoice) {
                $diff = Carbon::today()->diffInDays($invoice->due_date, false);
                $type = $this->getReminderType($diff);

                if (!$type) {
                    continue;
                }

                if (!($enabledTypes[$type] ?? true)) {
                    $this->line("  SKIP [{$type}] Invoice #{$invoice->invoice_number} — tipe reminder dinonaktifkan");
                    $skippedCount++;
                    continue;
                }

                if (!$isDryRun && $this->notificationService->hasReminderSentToday($invoice, $type)) {
                    $this->line("  SKIP [{$type}] Invoice #{$invoice->invoice_number} — sudah terkirim hari ini");
                    $skippedCount++;
                    continue;
                }

                $tenant = $invoice->tenant;
                $this->line("  → [{$type}] {$tenant->full_name} — Invoice #{$invoice->invoice_number} (jatuh tempo: {$invoice->due_date->format('d/m/Y')})");

                if ($isDryRun) {
                    $this->comment('    [DRY RUN] Tidak ada notifikasi yang dikirim');
                    continue;
                }

                $sent = $this->notificationService->sendPaymentReminder($invoice, $type);

                if ($sent) {
                    $sentCount++;
                    $this->info("    ✓ Terkirim ke {$tenant->whatsapp_number}");

                    if ($type === 'overdue') {
                        $invoice->update(['status' => 'overdue']);
                    } elseif ($invoice->status === 'draft') {
                        $invoice->update(['status' => 'sent']);
                    }
                } else {
                    $failedCount++;
                    $this->error("    ✗ Gagal kirim ke {$tenant->whatsapp_number}");
                }
            }

            $runtime = round(microtime(true) - $startTime, 3);
            $this->info("Selesai. Terkirim: {$sentCount}, Skip: {$skippedCount}, Gagal: {$failedCount}, Waktu: {$runtime}s");

            return self::SUCCESS;
        } catch (\Exception $e) {
            Log::error('SendPaymentReminder error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->error('Error: ' . $e->getMessage());

            return self::FAILURE;
        }
    }

    private function getReminderType(int $diff): ?string
    {
        return match (true) {
            $diff === 7 => 'h7',
            $diff === 3 => 'h3',
            $diff === 1 => 'h1',
            $diff < 0 => 'overdue',
            default => null,
        };
    }
}
