<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Rental;
use Carbon\Carbon;

class InvoiceService
{
    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $last = Invoice::whereDate('created_at', today())->count() + 1;
        return "{$prefix}-{$date}-" . str_pad($last, 4, '0', STR_PAD_LEFT);
    }

    public function generateMonthlyInvoice(Rental $rental, int $year, int $month): Invoice
    {
        $existing = Invoice::where('rental_id', $rental->id)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if ($existing) {
            return $existing;
        }

        $dueDate = Carbon::create($year, $month, $rental->due_date_day);

        return Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'rental_id' => $rental->id,
            'tenant_id' => $rental->tenant_id,
            'year' => $year,
            'month' => $month,
            'rent_amount' => $rental->monthly_rent,
            'extra_charges' => 0,
            'total' => $rental->monthly_rent,
            'due_date' => $dueDate,
            'status' => 'draft',
        ]);
    }

    public function generateFirstInvoice(Rental $rental): Invoice
    {
        return $this->generateMonthlyInvoice(
            $rental,
            $rental->start_date->year,
            $rental->start_date->month
        );
    }

    /**
     * Generate all monthly invoices from rental start_date to end_date.
     * Falls back to first-invoice-only when end_date is null.
     *
     * @return Invoice[]
     */
    public function generateInvoicesForPeriod(Rental $rental): array
    {
        if (!$rental->end_date) {
            return [$this->generateFirstInvoice($rental)];
        }

        $invoices = [];
        $current  = $rental->start_date->copy()->startOfMonth();
        $last     = $rental->end_date->copy()->startOfMonth();

        // Exclude end month when end_date day < start_date day
        // e.g. start=5 Jun, end=4 Jul → last stays on Jun (1 invoice, not 2)
        if ($rental->end_date->day < $rental->start_date->day) {
            $last->subMonth();
        }

        while ($current->lte($last)) {
            $invoices[] = $this->generateMonthlyInvoice($rental, $current->year, $current->month);
            $current->addMonth();
        }

        return $invoices;
    }

    public function generateBulkMonthlyInvoices(): array
    {
        $generated = [];
        $now = now();
        $year = $now->year;
        $month = $now->month;

        $rentals = Rental::where('status', 'aktif')
            ->with(['tenant', 'room'])
            ->get();

        foreach ($rentals as $rental) {
            $invoice = $this->generateMonthlyInvoice($rental, $year, $month);
            $generated[] = $invoice;
        }

        return $generated;
    }

    public function markAsPaid(Invoice $invoice): Invoice
    {
        $invoice->update([
            'status' => 'paid',
            'paid_date' => today(),
        ]);
        return $invoice;
    }

    public function updateOverdueStatus(): int
    {
        return Invoice::whereNotIn('status', ['paid', 'overdue', 'draft'])
            ->whereDate('due_date', '<', today())
            ->update(['status' => 'overdue']);
    }

    public function getDueSoonInvoices(int $days = 7): \Illuminate\Database\Eloquent\Collection
    {
        return Invoice::whereNotIn('status', ['paid', 'overdue', 'draft'])
            ->whereBetween('due_date', [today(), today()->addDays($days)])
            ->with(['tenant', 'rental.room'])
            ->orderBy('due_date')
            ->get();
    }

    public function getMonthlyRevenue(int $year, int $month): float
    {
        return (float) Invoice::where('status', 'paid')
            ->where('year', $year)
            ->where('month', $month)
            ->sum('total');
    }
}
