<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    /**
     * Admin Order List
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product'])
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | Order Status Filter
        |--------------------------------------------------------------------------
        */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /*
        |--------------------------------------------------------------------------
        | Payment Status Filter
        |--------------------------------------------------------------------------
        */
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                    ->orWhere('customer_name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('payment_transaction_id', 'like', '%' . $search . '%');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */
        $orders = $query->paginate(15)->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */
        $totalOrders = Order::count();

        $pendingOrders = Order::where('status', 'pending')->count();

        $processingOrders = Order::where('status', 'processing')->count();

        $shippedOrders = Order::where('status', 'shipped')->count();

        $deliveredOrders = Order::where('status', 'delivered')->count();

        $cancelledOrders = Order::where('status', 'cancelled')->count();

        $paidOrders = Order::where('payment_status', 'Paid')->count();

        $pendingPayments = Order::where('payment_status', 'Pending')->count();

        $totalSales = Order::where('payment_status', 'Paid')
            ->where('status', '!=', 'cancelled')
            ->sum(DB::raw('COALESCE(total_amount, total, 0)'));

        return view('admin.orders.index', compact(
            'orders',
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'shippedOrders',
            'deliveredOrders',
            'cancelledOrders',
            'paidOrders',
            'pendingPayments',
            'totalSales'
        ));
    }

    /**
     * Show Single Order
     */
    public function show(Order $order)
    {
        $order->load([
            'user',
            'items.product',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update Order Status
     *
     * Handles stock restoration when an order is cancelled
     * and stock re-deduction when a cancelled order becomes active again.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending,confirmed,processing,shipped,delivered,cancelled'
            ],
        ]);

        $newStatus = strtolower(trim($validated['status']));
        $oldStatus = strtolower(trim((string) $order->status));

        /*
        |--------------------------------------------------------------------------
        | Delivered orders are final in this admin workflow.
        |--------------------------------------------------------------------------
        |
        | A delivered order must not be moved back to another status because
        | doing so could incorrectly restore/deduct stock a second time.
        |--------------------------------------------------------------------------
        */
        if ($oldStatus === 'delivered' && $newStatus !== 'delivered') {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'Delivered orders cannot be moved back to another status.');
        }

        /*
        |--------------------------------------------------------------------------
        | No Change
        |--------------------------------------------------------------------------
        */
        if ($oldStatus === $newStatus) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Order status is already ' . ucfirst($newStatus) . '.');
        }

        DB::transaction(function () use ($order, $oldStatus, $newStatus) {

            /*
            |--------------------------------------------------------------------------
            | ACTIVE -> CANCELLED
            |
            | Restore stock because cancelled orders should not hold stock.
            |--------------------------------------------------------------------------
            */
            if ($oldStatus !== 'cancelled' && $newStatus === 'cancelled') {

                foreach ($order->items as $item) {

                    $product = Product::lockForUpdate()
                        ->find($item->product_id);

                    if ($product) {
                        $product->stock += (int) $item->quantity;
                        $product->save();
                    }
                }

                // Give the coupon redemption back so limited coupons are not wasted.
                if ($order->coupon_code) {
                    \App\Models\Coupon::where('code', $order->coupon_code)
                        ->where('used_count', '>', 0)
                        ->decrement('used_count');
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CANCELLED -> ACTIVE
            |
            | Deduct stock again.
            |--------------------------------------------------------------------------
            */
            if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {

                foreach ($order->items as $item) {

                    $product = Product::lockForUpdate()
                        ->find($item->product_id);

                    if (!$product) {
                        throw new \Exception(
                            "Product not found for order item #{$item->id}."
                        );
                    }

                    if ($product->stock < $item->quantity) {
                        throw new \Exception(
                            "Not enough stock for product: {$product->name}"
                        );
                    }

                    $product->stock -= (int) $item->quantity;
                    $product->save();
                }

                if ($order->coupon_code) {
                    \App\Models\Coupon::where('code', $order->coupon_code)
                        ->increment('used_count');
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Save New Status
            |--------------------------------------------------------------------------
            */
            $order->status = $newStatus;
            $order->save();
        });

        // Tell the customer about the new status (best-effort).
        try {
            app(\App\Services\SmsService::class)->notifyStatusChange($order);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with(
                'success',
                'Order status updated successfully to ' . ucfirst($newStatus) . '.'
            );
    }

    /**
     * Update Payment Status
     *
     * This method fixes:
     * Route [admin.orders.payment.status.update] not defined
     *
     * Supported payment statuses:
     * Pending, Paid, Failed, Refunded
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => [
                'required',
                'in:Pending,Paid,Failed,Refunded'
            ],
        ]);

        $paymentStatus = $validated['payment_status'];

        /*
        |--------------------------------------------------------------------------
        | Update Payment Status
        |--------------------------------------------------------------------------
        */
        $order->payment_status = $paymentStatus;

        /*
        |--------------------------------------------------------------------------
        | Paid Date
        |--------------------------------------------------------------------------
        |
        | If payment becomes Paid, save the current time.
        |
        | If payment is changed back to Pending/Failed/Refunded,
        | clear paid_at.
        |
        */
        if ($paymentStatus === 'Paid') {

            if (!$order->paid_at) {
                $order->paid_at = now();
            }

        } else {

            $order->paid_at = null;
        }

        $order->save();

        return redirect()
            ->route('admin.orders.show', $order)
            ->with(
                'success',
                'Payment status updated successfully to ' . $paymentStatus . '.'
            );
    }

    /**
     * Delete Order
     */
    public function destroy(Order $order)
    {
        /*
        |--------------------------------------------------------------------------
        | Protect financial/order history
        |--------------------------------------------------------------------------
        |
        | Paid or delivered orders must remain in the database for audit/history.
        | Use status changes/refunds instead of deleting them.
        |--------------------------------------------------------------------------
        */

        if (
            $order->isDelivered()
            || $order->isPaid()
        ) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with(
                    'error',
                    'Delivered or paid orders cannot be deleted. Keep them for order and payment history.'
                );
        }

        try {
            DB::transaction(function () use ($order) {

                /*
                |--------------------------------------------------------------------------
                | Restore stock only for active orders.
                |--------------------------------------------------------------------------
                */
                if (!$order->isCancelled()) {

                    $order->load('items');

                    foreach ($order->items as $item) {

                        if (!$item->product_id) {
                            continue;
                        }

                        $product = Product::lockForUpdate()
                            ->find($item->product_id);

                        if ($product) {
                            $product->increment(
                                'stock',
                                (int) $item->quantity
                            );
                        }
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Delete child items first.
                |--------------------------------------------------------------------------
                */
                OrderItem::where('order_id', $order->id)->delete();

                /*
                |--------------------------------------------------------------------------
                | Delete order.
                |--------------------------------------------------------------------------
                */
                $order->delete();
            });

            return redirect()
                ->route('admin.orders.index')
                ->with(
                    'success',
                    'Order deleted successfully and reserved stock was restored.'
                );

        } catch (\Throwable $e) {

            report($e);

            return redirect()
                ->route('admin.orders.show', $order)
                ->with(
                    'error',
                    'The order could not be deleted. Please try again.'
                );
        }
    }
}
