<?php

namespace App\Services;

use App\Models\Rental;
use App\Models\Room;

class RentalService
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function createRental(array $data): Rental
    {
        $rental = Rental::create([
            'tenant_id' => $data['tenant_id'],
            'room_id' => $data['room_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'monthly_rent' => $data['monthly_rent'],
            'deposit' => $data['deposit'] ?? 0,
            'method' => $data['method'] ?? 'monthly',
            'status' => 'aktif',
            'due_date_day' => $data['due_date_day'] ?? 1,
            'notes' => $data['notes'] ?? null,
        ]);

        Room::where('id', $data['room_id'])->update(['status' => 'terisi']);

        $this->invoiceService->generateInvoicesForPeriod($rental);

        return $rental;
    }

    public function terminateRental(Rental $rental, string $reason = ''): Rental
    {
        $rental->update([
            'status' => 'berakhir',
            'end_date' => today(),
            'notes' => $reason ?: $rental->notes,
        ]);

        Room::where('id', $rental->room_id)->update(['status' => 'tersedia']);

        return $rental;
    }

    public function extendRental(Rental $rental, array $data): Rental
    {
        $rental->update([
            'end_date' => $data['end_date'],
            'monthly_rent' => $data['monthly_rent'] ?? $rental->monthly_rent,
            'notes' => $data['notes'] ?? $rental->notes,
        ]);

        return $rental;
    }

    public function getActiveRentals(): \Illuminate\Database\Eloquent\Collection
    {
        return Rental::where('status', 'aktif')
            ->with(['tenant', 'room'])
            ->get();
    }

    public function getRentalStats(): array
    {
        return [
            'total' => Rental::count(),
            'aktif' => Rental::where('status', 'aktif')->count(),
            'berakhir' => Rental::where('status', 'berakhir')->count(),
            'dibatalkan' => Rental::where('status', 'dibatalkan')->count(),
        ];
    }
}
