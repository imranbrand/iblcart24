<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Download the invoice PDF for an order.
     * Allowed: the customer who owns the order, or an admin.
     */
    public function download(Order $order)
    {
        $user = Auth::user();

        abort_unless($user, 401);

        $isOwner = $order->user_id !== null
            && (int) $order->user_id === (int) $user->id;

        abort_unless($isOwner || $user->isAdmin(), 403);

        $order->load(['items', 'user']);

        $number = str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);

        return Pdf::loadView('invoices.order', [
                'order'  => $order,
                'number' => $number,
            ])
            ->setPaper('a4')
            ->download('invoice-' . $number . '.pdf');
    }
}
