<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->user()->tenant;
        if (!$tenant) {
            return response()->json(['message' => 'Data penyewa tidak ditemukan.'], 404);
        }

        $invoices = Invoice::query()->where('tenant_id', $tenant->id)
            ->where('status', '!=', 'draft')
            ->with(['rental.room', 'payments'])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(20);

        return InvoiceResource::collection($invoices);
    }

    public function show(Request $request, Invoice $invoice)
    {
        $tenant = $request->user()->tenant;
        if (!$tenant || $invoice->tenant_id !== $tenant->id || $invoice->status === 'draft') {
            return response()->json(['message' => 'Tagihan tidak ditemukan.'], 404);
        }

        $invoice->load(['rental.room', 'payments']);
        return new InvoiceResource($invoice);
    }

    public function summary(Request $request)
    {
        $tenant = $request->user()->tenant;
        if (!$tenant) {
            return response()->json(['message' => 'Data penyewa tidak ditemukan.'], 404);
        }

        $summary = [
            'total_tagihan' => Invoice::query()->where('tenant_id', $tenant->id)->count(),
            'belum_bayar' => Invoice::query()->where('tenant_id', $tenant->id)
                ->whereNotIn('status', ['paid', 'draft'])->count(),
            'terlambat' => Invoice::query()->where('tenant_id', $tenant->id)
                ->where('status', 'overdue')->count(),
            'lunas' => Invoice::query()->where('tenant_id', $tenant->id)
                ->where('status', 'paid')->count(),
            'tagihan_aktif' => optional(Invoice::query()->where('tenant_id', $tenant->id)
                ->whereNotIn('status', ['paid', 'overdue', 'draft'])
                ->orderBy('due_date')
                ->first(['id', 'invoice_number', 'total', 'due_date', 'status']))->toArray(),
        ];

        return response()->json($summary);
    }
}
