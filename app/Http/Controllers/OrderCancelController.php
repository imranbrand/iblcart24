<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderCancelController extends Controller
{
    /**
     * A customer may cancel their OWN order while it is still pending
     * and has not been paid. Stock and coupon use are given back.
     */
    public function __invoke(Request $request, Order $order)
    {
        if ((int) $order->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        $status = strtolower((string) $order->status);

        if ($status !== 'pending') {
            return back()->with('error', 'This order can no longer be cancelled online. Please contact us.');
        }

        if (strtolower((string) $order->payment_status) === 'paid') {
            return back()->with('error', 'A paid order cannot be cancelled online. Please contact us for a refund.');
        }

        DB::transaction(function () use ($order) {
            $locked = Order::lockForUpdate()->with('items')->find($order->id);

            if (! $locked || strtolower((string) $locked->status) !== 'pending') {
                return;
            }

            foreach ($locked->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)->increment('stock', (int) $item->quantity);
                }
            }

            if ($locked->coupon_code) {
                Coupon::where('code', $locked->coupon_code)
                    ->where('used_count', '>', 0)
                    ->decrement('used_count');
            }

            $locked->status = 'cancelled';
            $locked->save();
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Your order has been cancelled.');
    }
}
