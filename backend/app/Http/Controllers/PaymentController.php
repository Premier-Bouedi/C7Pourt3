<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    /**
     * Display all Cash On Delivery (COD) payments
     */
    public function index(): Response
    {
        // Fetch all COD payments with order details
        $payments = Payment::with('order')
            ->where('status', 'pending')
            ->orWhere('status', 'completed')
            ->orderByDesc('created_at')
            ->paginate(50);

        // Summary statistics
        $pendingAmount = Payment::where('status', 'pending')->sum('amount');
        $completedAmount = Payment::where('status', 'completed')->sum('amount');
        $failedAmount = Payment::where('status', 'failed')->sum('amount');

        return Inertia::render('PaymentsCOD', [
            'payments' => $payments,
            'summary' => [
                'pending_amount' => $pendingAmount,
                'completed_amount' => $completedAmount,
                'failed_amount' => $failedAmount,
                'currency' => 'MAD',
            ],
        ]);
    }

    /**
     * Validate a COD payment
     */
    public function validate(Request $request, Payment $payment): Response
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $payment->update([
            'status' => 'completed',
            'validated_at' => now(),
            'validated_by' => auth()->id(),
        ]);

        // Update order status if all payments complete
        if ($payment->order->payments->every(fn ($p) => $p->status === 'completed')) {
            $payment->order->update(['status' => 'processing']);
        }

        return back()->with('success', 'Payment validated successfully');
    }
}
