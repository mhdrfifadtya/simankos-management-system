<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Rental;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function index(Request $request)
    {
        $query = Invoice::with(['tenant', 'rental.room']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        $invoices = $query->latest()->paginate(15);
        return view('invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['tenant', 'rental.room', 'payments.verifiedBy']);
        return view('invoices.show', compact('invoice'));
    }

    public function generateMonthly(Request $request)
    {
        $request->validate([
            'rental_id' => ['required', 'exists:rentals,id'],
            'year' => ['required', 'integer', 'min:2020'],
            'month' => ['required', 'integer', 'between:1,12'],
        ]);

        $rental = Rental::findOrFail($request->rental_id);
        $invoice = $this->invoiceService->generateMonthlyInvoice($rental, $request->year, $request->month);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice berhasil di-generate.');
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => ['required', 'in:draft,sent,pending,partial,paid,overdue'],
        ]);

        $invoice->update(['status' => $request->status]);
        return back()->with('success', 'Status invoice berhasil diperbarui.');
    }

    public function exportPdf(Invoice $invoice)
    {
        $invoice->load(['tenant', 'rental.room', 'payments']);
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }
}
