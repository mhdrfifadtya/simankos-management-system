<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function index(Request $request)
    {
        $tenant = $request->user()->tenant;
        if (!$tenant) {
            return response()->json(['message' => 'Data penyewa tidak ditemukan.'], 404);
        }

        $payments = Payment::where('tenant_id', '=', $tenant->id, 'and')
            ->with(['invoice', 'room'])
            ->latest()
            ->paginate(20);

        return PaymentResource::collection($payments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:1000'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'string'],
            'proof_of_payment' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'notes' => ['nullable', 'string'],
        ]);

        $tenant = $request->user()->tenant;
        $invoice = Invoice::with('rental')->findOrFail($request->input('invoice_id'));

        if ($invoice->tenant_id !== $tenant->id) {
            return response()->json(['message' => 'Tagihan tidak ditemukan.'], 404);
        }

        if ($invoice->status === 'draft') {
            return response()->json(['message' => 'Tagihan belum aktif.'], 422);
        }

        if ($invoice->status === 'paid') {
            return response()->json(['message' => 'Tagihan sudah lunas.'], 422);
        }

        $data = $request->only(['invoice_id', 'amount', 'payment_date', 'payment_method', 'notes']);
        $data['tenant_id'] = $tenant->id;
        $data['room_id'] = $invoice->rental->room_id;

        if ($request->hasFile('proof_of_payment')) {
            $file = $request->file('proof_of_payment');
            $name = time() . '_' . uniqid() . '.' . strtolower($file->getClientOriginalExtension());
            $file->move(public_path('uploads/payments/proof'), $name);
            $data['proof_of_payment'] = "payments/proof/{$name}";
        }

        $payment = $this->paymentService->createPayment($data);

        return response()->json([
            'message' => 'Pembayaran berhasil dikirim. Menunggu konfirmasi admin.',
            'payment' => PaymentResource::make($payment),
        ], 201);
    }

    public function show(Request $request, Payment $payment)
    {
        $tenant = $request->user()->tenant;
        if (!$tenant || $payment->tenant_id !== $tenant->id) {
            return response()->json(['message' => 'Pembayaran tidak ditemukan.'], 404);
        }

        return PaymentResource::make($payment->load(['invoice', 'room']));
    }
}
