<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\Room;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RentalInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();
        $rooms = Room::where('status', 'tersedia')->take(6)->get();

        $now = Carbon::now();

        foreach ($tenants->take(6) as $index => $tenant) {
            $room = $rooms[$index] ?? null;
            if (!$room) continue;

            $room->update(['status' => 'terisi']);

            $rental = Rental::create([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'start_date' => $now->copy()->subMonths(2)->startOfMonth(),
                'monthly_rent' => $room->price,
                'deposit' => $room->price,
                'method' => 'monthly',
                'status' => 'aktif',
                'due_date_day' => 5,
                'notes' => 'Kontrak awal',
            ]);

            // Generate invoices with mixed statuses
            $invoiceStatuses = ['paid', 'paid', 'pending', 'pending', 'overdue', 'draft'];
            $status = $invoiceStatuses[$index] ?? 'pending';

            for ($m = 0; $m < 3; $m++) {
                $targetDate = $now->copy()->subMonths(2 - $m);
                $year = $targetDate->year;
                $month = $targetDate->month;
                $dueDate = Carbon::create($year, $month, 5);

                $invoiceStatus = $m < 2 ? 'paid' : $status;
                $paidDate = $invoiceStatus === 'paid' ? $dueDate->copy()->subDays(2) : null;

                $invoice = Invoice::create([
                    'invoice_number' => 'INV-' . date('Ymd') . '-' . str_pad(($index * 3 + $m + 1), 4, '0', STR_PAD_LEFT),
                    'rental_id' => $rental->id,
                    'tenant_id' => $tenant->id,
                    'year' => $year,
                    'month' => $month,
                    'rent_amount' => $room->price,
                    'extra_charges' => 0,
                    'total' => $room->price,
                    'due_date' => $dueDate,
                    'paid_date' => $paidDate,
                    'status' => $invoiceStatus,
                ]);

                if ($invoiceStatus === 'paid') {
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'tenant_id' => $tenant->id,
                        'room_id' => $room->id,
                        'amount' => $room->price,
                        'payment_date' => $paidDate,
                        'payment_method' => 'transfer',
                        'status' => 'lunas',
                        'verified_at' => $paidDate,
                    ]);
                } elseif ($invoiceStatus === 'pending') {
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'tenant_id' => $tenant->id,
                        'room_id' => $room->id,
                        'amount' => $room->price,
                        'payment_date' => today(),
                        'payment_method' => 'transfer',
                        'status' => 'menunggu_konfirmasi',
                    ]);
                }
            }
        }
    }
}
