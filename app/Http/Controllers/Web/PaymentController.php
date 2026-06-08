<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\PaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function index(Request $request)
    {
        $query = Payment::with(['invoice', 'tenant', 'room', 'verifiedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenant', fn($q) => $q->where('full_name', 'like', "%{$search}%"));
        }

        $payments = $query->latest()->paginate(15);
        return view('payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $invoice = null;
        if ($request->filled('invoice_id')) {
            $invoice = Invoice::with(['tenant', 'rental.room'])->find($request->invoice_id);
        }
        $invoices = Invoice::whereNotIn('status', ['paid', 'draft'], 'and')->with(['tenant', 'rental.room'])->get();
        return view('payments.create', compact('invoices', 'invoice'));
    }

    public function store(PaymentRequest $request)
    {
        // Ambil hanya field yang valid untuk Payment — jangan pakai validated() langsung
        // karena proof_of_payment adalah UploadedFile, bukan string
        $data = [
            'invoice_id'      => $request->invoice_id,
            'amount'          => $request->amount,
            'payment_date'    => $request->payment_date,
            'payment_method'  => $request->payment_method,
            'notes'           => $request->notes,
        ];

        // Simpan bukti pembayaran ke public/uploads
        if ($request->hasFile('proof_of_payment')) {
            $data['proof_of_payment'] = $this->saveProof($request->file('proof_of_payment'));
        }

        $invoice = Invoice::with('rental')->findOrFail($data['invoice_id']);
        $data['tenant_id'] = $invoice->tenant_id;
        $data['room_id']   = $invoice->rental->room_id;

        $payment = $this->paymentService->createPayment($data);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Pembayaran berhasil dicatat. Menunggu verifikasi admin.');
    }

    private function saveProof(UploadedFile $file): string
    {
        $name = time() . '_' . uniqid() . '.' . strtolower($file->getClientOriginalExtension());
        $file->move(public_path('uploads/payments/proof'), $name);
        return "payments/proof/{$name}";
    }

    public function show(Payment $payment)
    {
        $payment->load(['invoice.rental.room', 'tenant', 'verifiedBy']);
        return view('payments.show', compact('payment'));
    }

    public function verify(Payment $payment)
    {
        if ($payment->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'Pembayaran tidak dalam status menunggu konfirmasi.');
        }

        $this->paymentService->verifyPayment($payment, Auth::user());
        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $request->validate(['reason' => ['required', 'string', 'min:5']]);

        if ($payment->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'Pembayaran tidak dalam status menunggu konfirmasi.');
        }

        $this->paymentService->rejectPayment($payment, Auth::user(), $request->reason);
        return back()->with('success', 'Pembayaran berhasil ditolak.');
    }
}
