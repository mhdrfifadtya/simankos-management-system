<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Room;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function payments(Request $request)
    {
        $query = Invoice::query()->where('status', 'paid')
            ->with(['tenant', 'rental.room']);

        if ($request->filled('start_date')) {
            $query->whereDate('paid_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('paid_date', '<=', $request->end_date);
        }

        $invoices = $query->latest('paid_date')->get();
        $total = $invoices->sum('total');

        return view('reports.payments', compact('invoices', 'total'));
    }

    public function exportPaymentsPdf(Request $request)
    {
        $query = Invoice::query()->where('status', 'paid')->with(['tenant', 'rental.room']);

        if ($request->filled('start_date')) {
            $query->whereDate('paid_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('paid_date', '<=', $request->end_date);
        }

        $invoices = $query->latest('paid_date')->get();
        $total = $invoices->sum('total');
        $period = ($request->start_date ?? 'Awal') . ' s/d ' . ($request->end_date ?? 'Akhir');

        $pdf = Pdf::loadView('pdf.payment-report', compact('invoices', 'total', 'period'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pembayaran.pdf');
    }

    public function rooms()
    {
        $rooms = Room::withCount(['rentals as active_rentals_count' => fn($q) => $q->where('status', 'aktif')])->get();

        $totalRooms = Room::query()->count('*');
        $tersedia = Room::query()->where('status', '=', 'tersedia')->count('*');
        $terisi = Room::query()->where('status', '=', 'terisi')->count('*');
        $maintenance = Room::query()->where('status', '=', 'maintenance')->count('*');

        $stats = [
            'total' => $totalRooms,
            'tersedia' => $tersedia,
            'terisi' => $terisi,
            'maintenance' => $maintenance,
            'occupancy_rate' => $totalRooms > 0
                ? round(($terisi / $totalRooms) * 100, 1)
                : 0,
        ];

        return view('reports.rooms', compact('rooms', 'stats'));
    }
}
