<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\RentalRequest;
use App\Models\Rental;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\RentalService;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function __construct(private RentalService $rentalService) {}

    public function index(Request $request)
    {
        $query = Rental::with(['tenant', 'room', 'invoices']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenant', fn($q) => $q->where('full_name', 'like', "%{$search}%"));
        }

        $rentals = $query->latest()->paginate(15);
        return view('rentals.index', compact('rentals'));
    }

    public function create()
    {
        $tenants = Tenant::whereStatus('aktif')
            ->whereDoesntHave('rentals', fn($q) => $q->where('status', 'aktif'))
            ->get();
        $rooms = Room::whereStatus('tersedia')->get();
        return view('rentals.create', compact('tenants', 'rooms'));
    }

    public function store(RentalRequest $request)
    {
        $rental = $this->rentalService->createRental($request->validated());
        return redirect()->route('rentals.show', $rental)
            ->with('success', 'Kontrak berhasil dibuat dan invoice pertama telah di-generate.');
    }

    public function show(Rental $rental)
    {
        $rental->load(['tenant', 'room', 'invoices.payments']);
        return view('rentals.show', compact('rental'));
    }

    public function edit(Rental $rental)
    {
        return view('rentals.edit', compact('rental'));
    }

    public function update(Request $request, Rental $rental)
    {
        $request->validate([
            'end_date'     => ['nullable', 'date', 'after:start_date'],
            'monthly_rent' => ['required', 'numeric', 'min:0'],
            'notes'        => ['nullable', 'string'],
        ]);

        $this->rentalService->extendRental($rental, $request->only(['end_date', 'monthly_rent', 'notes']));
        return redirect()->route('rentals.show', $rental)
            ->with('success', 'Kontrak berhasil diperbarui.');
    }

    public function terminate(Request $request, Rental $rental)
    {
        if ($rental->status !== 'aktif') {
            return back()->with('error', 'Kontrak sudah tidak aktif.');
        }
        $this->rentalService->terminateRental($rental, $request->reason ?? '');
        return redirect()->route('rentals.index')
            ->with('success', 'Kontrak berhasil diakhiri.');
    }

    public function destroy(Rental $rental)
    {
        if ($rental->status === 'aktif') {
            return back()->with('error', 'Kontrak aktif tidak dapat dihapus. Akhiri kontrak terlebih dahulu.');
        }

        $rental->delete();

        return redirect()->route('rentals.index')
            ->with('success', 'Kontrak berhasil dihapus.');
    }
}
