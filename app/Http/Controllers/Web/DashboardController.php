<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\InvoiceService;

class DashboardController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function index()
    {
        $stats = [
            'total_rooms' => Room::count('*'),
            'occupied_rooms' => Room::where('status', '=', 'terisi', 'and')->count(),
            'available_rooms' => Room::where('status', '=', 'tersedia', 'and')->count(),
            'maintenance_rooms' => Room::where('status', '=', 'maintenance', 'and')->count(),
            'active_tenants' => Tenant::where('status', '=', 'aktif', 'and')->count(),
            'monthly_revenue' => $this->invoiceService->getMonthlyRevenue(now()->year, now()->month),
            'pending_invoices' => Invoice::whereNotIn('status', ['paid', 'draft'], 'and')->count(),
        ];

        $dueSoonInvoices = $this->invoiceService->getDueSoonInvoices(7);

        $pendingPayments = Payment::where('status', '=', 'menunggu_konfirmasi', 'and')
            ->with(['tenant', 'invoice', 'room'])
            ->latest()
            ->take(10)
            ->get();

        $chartData = $this->getChartData();

        return view('dashboard.index', compact('stats', 'dueSoonInvoices', 'pendingPayments', 'chartData'));
    }

    private function getChartData(): array
    {
        $months = [];
        $revenues = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->locale('id')->monthName . ' ' . $date->year;
            $revenues[] = $this->invoiceService->getMonthlyRevenue($date->year, $date->month);
        }

        return ['labels' => $months, 'data' => $revenues];
    }
}
