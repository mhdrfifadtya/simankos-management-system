<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\ReminderLog;
use App\Models\Tenant;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $notificationService) {}

    public function index()
    {
        $logs = NotificationLog::with('tenant')
            ->latest()
            ->paginate(20);

        $settings = [
            'reminder_h7' => \App\Models\Setting::get('reminder_h7', true),
            'reminder_h3' => \App\Models\Setting::get('reminder_h3', true),
            'reminder_h1' => \App\Models\Setting::get('reminder_h1', true),
            'reminder_overdue' => \App\Models\Setting::get('reminder_overdue', true),
        ];

        return view('notifications.index', compact('logs', 'settings'));
    }

    public function updateSettings(Request $request)
    {
        foreach (['reminder_h7', 'reminder_h3', 'reminder_h1', 'reminder_overdue'] as $key) {
            \App\Models\Setting::set($key, $request->boolean($key), 'boolean');
        }
        return back()->with('success', 'Pengaturan notifikasi berhasil disimpan.');
    }

    public function sendManual(Request $request)
    {
        $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
            'message' => ['required', 'string', 'min:10'],
        ]);

        $tenant = Tenant::findOrFail($request->tenant_id);
        $result = $this->notificationService->sendWhatsApp($tenant->whatsapp_number, $request->message);

        NotificationLog::create([
            'tenant_id' => $tenant->id,
            'whatsapp_number' => $tenant->whatsapp_number,
            'type' => 'manual',
            'message' => $request->message,
            'status' => $result['success'] ? 'terkirim' : 'gagal',
            'gateway_response' => json_encode($result['response']),
            'sent_at' => now(),
        ]);

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['success'] ? 'Pesan berhasil dikirim.' : 'Gagal mengirim pesan.'
        );
    }

    public function getTenantData(Tenant $tenant)
    {
        $rental = $tenant->rentals()->where('status', 'aktif')->with('room')->latest()->first();
        $invoice = $tenant->invoices()->whereIn('status', ['unpaid', 'overdue'])->latest()->first();

        return response()->json([
            'nama'    => $tenant->full_name,
            'kamar'   => $rental?->room?->room_number ?? '-',
            'periode' => $invoice ? $invoice->period_label : now()->locale('id')->translatedFormat('F Y'),
            'jumlah'  => $invoice ? number_format($invoice->total, 0, ',', '.') : '-',
            'tanggal' => $invoice ? $invoice->due_date->format('d/m/Y') : '-',
        ]);
    }

    public function testConnection()
    {
        $result = $this->notificationService->testConnection();
        return response()->json($result);
    }
}
