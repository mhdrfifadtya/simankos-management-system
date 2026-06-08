<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;

class PaymentService
{
    public function __construct(private NotificationService $notificationService) {}

    public function createPayment(array $data): Payment
    {
        $payment = Payment::create([
            'invoice_id' => $data['invoice_id'],
            'tenant_id' => $data['tenant_id'],
            'room_id' => $data['room_id'],
            'amount' => $data['amount'],
            'payment_date' => $data['payment_date'],
            'payment_method' => $data['payment_method'] ?? null,
            'proof_of_payment' => $data['proof_of_payment'] ?? null,
            'status' => 'menunggu_konfirmasi',
            'notes' => $data['notes'] ?? null,
        ]);

        $invoice = $payment->invoice;
        if ($invoice->status !== 'paid') {
            $invoice->update(['status' => 'pending']);
        }

        return $payment;
    }

    public function verifyPayment(Payment $payment, User $admin): Payment
    {
        $payment->update([
            'status' => 'lunas',
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        $invoice = $payment->invoice;
        $totalPaid = $invoice->payments()
            ->where('status', 'lunas')
            ->sum('amount');

        if ($totalPaid >= $invoice->total) {
            $invoice->update(['status' => 'paid', 'paid_date' => today()]);
            $this->notificationService->sendPaymentConfirmation($invoice);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'partial']);
        }

        return $payment;
    }

    public function rejectPayment(Payment $payment, User $admin, string $reason = ''): Payment
    {
        $payment->update([
            'status' => 'ditolak',
            'notes' => $reason,
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        $invoice = $payment->invoice;
        if ($invoice->status === 'pending') {
            $invoice->update(['status' => 'sent']);
        }

        return $payment;
    }

    public function getPendingPayments(): \Illuminate\Database\Eloquent\Collection
    {
        return Payment::where('status', 'menunggu_konfirmasi')
            ->with(['invoice', 'tenant', 'room'])
            ->latest()
            ->get();
    }
}
