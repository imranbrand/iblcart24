<?php

namespace App\Http\Controllers;

use App\Models\Order;

class AdminVerifyPaymentController extends Controller
{
    /**
     * Mark a bKash / Nagad order as paid after the admin has checked the
     * Transaction ID in the bKash / Nagad merchant app.
     */
    public function __invoke(Order $order)
    {
        $back = redirect()->route('admin.orders.show', $order);

        if (!in_array($order->payment_method, ['bKash', 'Nagad'], true) || blank($order->payment_transaction_id)) {
            return $back->with('error', 'Only bKash / Nagad orders with a Transaction ID can be verified here.');
        }

        if ($order->isCancelled()) {
            return $back->with('error', 'This order is cancelled.');
        }

        if (strtolower((string) $order->payment_status) === 'paid') {
            return $back->with('success', 'Payment was already verified.');
        }

        $order->payment_status = 'Paid';
        $order->paid_at = $order->paid_at ?: now();
        $order->save();

        return $back->with('success', 'Payment verified. Order marked as Paid.');
    }
}
