<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CUSTOMER CHECKOUT
    |--------------------------------------------------------------------------
    */

    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $productIds = [];

        foreach ($cart as $key => $item) {
            $productId = is_array($item)
                ? ($item['product_id'] ?? $key)
                : $key;

            if ($productId) {
                $productIds[] = (int) $productId;
            }
        }

        $productIds = array_values(
            array_unique($productIds)
        );

        if (empty($productIds)) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Your cart is empty or invalid.'
                );
        }

        $products = Product::whereIn(
            'id',
            $productIds
        )
            ->get()
            ->keyBy('id');

        $cartItems = collect();

        foreach ($cart as $key => $item) {

            $productId = is_array($item)
                ? ($item['product_id'] ?? $key)
                : $key;

            if (!isset($products[$productId])) {
                continue;
            }

            $product = $products[$productId];

            $quantity = is_array($item)
                ? (int) ($item['quantity'] ?? 0)
                : (int) $item;

            if ($quantity < 1) {
                continue;
            }

            if ((int) $product->stock < $quantity) {
                return redirect()
                    ->route('cart.index')
                    ->with(
                        'error',
                        "Insufficient stock for {$product->name}. " .
                        "Available stock: {$product->stock}."
                    );
            }

            $price = (float) $product->price;

            $subtotal = $price * $quantity;

            $cartItems->push([
                'id' => $product->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'product_name' => $product->name,
                'price' => $price,
                'quantity' => $quantity,
                'image' => $product->image,
                'subtotal' => $subtotal,
                'product' => $product,
            ]);
        }

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Your cart is empty or contains unavailable products.'
                );
        }

        $subtotal = $cartItems->sum('subtotal');

        $deliveryCharge = 60;

        $total = $subtotal + $deliveryCharge;

        return view(
            'checkout.index',
            compact(
                'cartItems',
                'subtotal',
                'deliveryCharge',
                'total'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE ORDER
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => [
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
            ],

            'address' => [
                'required',
                'string',
                'max:1000',
            ],

            'payment_method' => [
                'required',
                'string',
                'in:Cash on Delivery,bKash,Nagad,Online Payment,COD,BKASH,NAGAD',
            ],

            'payment_transaction_id' => [
                'nullable',
                'string',
                'max:255',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Normalize Payment Method
        |--------------------------------------------------------------------------
        */

        $paymentMethod = match (
            strtoupper(
                trim(
                    $validated['payment_method']
                )
            )
        ) {
            'COD',
            'CASH ON DELIVERY'
                => 'Cash on Delivery',

            'BKASH'
                => 'bKash',

            'NAGAD'
                => 'Nagad',

            'ONLINE PAYMENT'
                => 'Online Payment',

            default
                => $validated['payment_method'],
        };


        /*
        |--------------------------------------------------------------------------
        | Transaction ID For bKash / Nagad
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $paymentMethod,
                ['bKash', 'Nagad'],
                true
            )
        ) {
            $request->validate([
                'payment_transaction_id' => [
                    'required',
                    'string',
                    'max:255',
                ],
            ]);

            $validated['payment_transaction_id'] =
                trim(
                    $validated['payment_transaction_id']
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Get Cart
        |--------------------------------------------------------------------------
        */

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Your cart is empty.'
                );
        }


        try {

            $order = DB::transaction(function () use (
                $cart,
                $validated,
                $paymentMethod
            ) {

                /*
                |--------------------------------------------------------------------------
                | Product IDs
                |--------------------------------------------------------------------------
                */

                $productIds = [];

                foreach ($cart as $key => $item) {

                    $productId = is_array($item)
                        ? ($item['product_id'] ?? $key)
                        : $key;

                    if ($productId) {
                        $productIds[] = (int) $productId;
                    }
                }

                $productIds = array_values(
                    array_unique($productIds)
                );

                if (empty($productIds)) {
                    throw new \Exception(
                        'Your cart contains no valid products.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Lock Products
                |--------------------------------------------------------------------------
                |
                | Prevents two simultaneous customers from purchasing
                | the same final stock.
                |
                */

                $products = Product::whereIn(
                    'id',
                    $productIds
                )
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($products->isEmpty()) {
                    throw new \Exception(
                        'No valid products found in cart.'
                    );
                }


                $subtotal = 0;

                $items = [];


                /*
                |--------------------------------------------------------------------------
                | Validate Cart Items
                |--------------------------------------------------------------------------
                */

                foreach ($cart as $key => $cartItem) {

                    $productId = is_array($cartItem)
                        ? ($cartItem['product_id'] ?? $key)
                        : $key;

                    if (!isset($products[$productId])) {
                        continue;
                    }

                    $product = $products[$productId];

                    $quantity = is_array($cartItem)
                        ? (int) ($cartItem['quantity'] ?? 0)
                        : (int) $cartItem;

                    if ($quantity < 1) {
                        continue;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Stock Validation
                    |--------------------------------------------------------------------------
                    */

                    if (
                        (int) $product->stock <
                        $quantity
                    ) {
                        throw new \Exception(
                            "Insufficient stock for {$product->name}. " .
                            "Available stock: {$product->stock}."
                        );
                    }


                    $itemSubtotal =
                        (float) $product->price *
                        $quantity;

                    $subtotal += $itemSubtotal;


                    $items[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'subtotal' => $itemSubtotal,
                    ];
                }


                if (empty($items)) {
                    throw new \Exception(
                        'Your cart contains no available products.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Delivery
                |--------------------------------------------------------------------------
                */

                $deliveryCharge = 60;

                $total =
                    $subtotal +
                    $deliveryCharge;


                /*
                |--------------------------------------------------------------------------
                | Payment
                |--------------------------------------------------------------------------
                */

                $paymentStatus = 'Pending';

                $paymentGateway = null;

                if ($paymentMethod === 'Online Payment') {
                    $paymentGateway = 'SSLCommerz';
                }


                /*
                |--------------------------------------------------------------------------
                | Create Order
                |--------------------------------------------------------------------------
                */

                $order = Order::create([

                    'user_id' =>
                        Auth::id(),

                    'customer_name' =>
                        $validated['customer_name'],

                    'phone' =>
                        $validated['phone'],

                    'address' =>
                        $validated['address'],

                    'subtotal' =>
                        $subtotal,

                    'delivery_charge' =>
                        $deliveryCharge,

                    'total_amount' =>
                        $total,

                    'total' =>
                        $total,

                    'payment_method' =>
                        $paymentMethod,

                    'payment_transaction_id' =>
                        $validated['payment_transaction_id'] ?? null,

                    'payment_gateway' =>
                        $paymentGateway,

                    'payment_status' =>
                        $paymentStatus,

                    'paid_at' =>
                        null,

                    'status' =>
                        'pending',

                    'note' =>
                        $validated['note'] ?? null,
                ]);


                /*
                |--------------------------------------------------------------------------
                | Create Order Items + Decrease Stock
                |--------------------------------------------------------------------------
                */

                foreach ($items as $item) {

                    $product =
                        $item['product'];

                    $quantity =
                        $item['quantity'];


                    OrderItem::create([

                        'order_id' =>
                            $order->id,

                        'product_id' =>
                            $product->id,

                        'product_name' =>
                            $product->name,

                        'price' =>
                            $product->price,

                        'quantity' =>
                            $quantity,

                        'subtotal' =>
                            $item['subtotal'],
                    ]);


                    /*
                    | Reserve stock
                    */

                    $product->decrement(
                        'stock',
                        $quantity
                    );
                }


                return $order;
            });


            /*
            |--------------------------------------------------------------------------
            | Clear Cart
            |--------------------------------------------------------------------------
            */

            session()->forget('cart');


            /*
            |--------------------------------------------------------------------------
            | Online Payment
            |--------------------------------------------------------------------------
            |
            | SSLCommerz integration is kept unchanged for now.
            |
            */

            if ($paymentMethod === 'Online Payment') {

                return redirect()
                    ->route(
                        'payment.sslcommerz.initiate',
                        $order
                    )
                    ->with(
                        'success',
                        'Order created successfully. Redirecting to payment...'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Normal Order
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'orders.show',
                    $order
                )
                ->with(
                    'success',
                    'Your order has been placed successfully.'
                );

        } catch (\Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER ORDERS
    |--------------------------------------------------------------------------
    */

    public function myOrders()
    {
        $userId = Auth::id();


        $orders = Order::with('items')
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);


        $totalOrders = Order::where(
            'user_id',
            $userId
        )->count();


        $pendingOrders = Order::where(
            'user_id',
            $userId
        )
            ->whereRaw(
                'LOWER(status) = ?',
                ['pending']
            )
            ->count();


        $processingOrders = Order::where(
            'user_id',
            $userId
        )
            ->whereRaw(
                'LOWER(status) = ?',
                ['processing']
            )
            ->count();


        $deliveredOrders = Order::where(
            'user_id',
            $userId
        )
            ->whereRaw(
                'LOWER(status) = ?',
                ['delivered']
            )
            ->count();


        $cancelledOrders = Order::where(
            'user_id',
            $userId
        )
            ->whereRaw(
                'LOWER(status) = ?',
                ['cancelled']
            )
            ->count();


        $paidOrders = Order::where(
            'user_id',
            $userId
        )
            ->whereRaw(
                'LOWER(payment_status) = ?',
                ['paid']
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Total Spent
        |--------------------------------------------------------------------------
        */

        $totalSpent = Order::where(
            'user_id',
            $userId
        )
            ->whereRaw(
                'LOWER(status) != ?',
                ['cancelled']
            )
            ->get()
            ->sum(function ($order) {

                return (float) (
                    $order->total_amount
                    ?? $order->total
                    ?? 0
                );
            });


        return view(
            'orders.index',
            compact(
                'orders',
                'totalOrders',
                'pendingOrders',
                'processingOrders',
                'deliveredOrders',
                'cancelledOrders',
                'paidOrders',
                'totalSpent'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER ORDER DETAILS
    |--------------------------------------------------------------------------
    */

    public function show(Order $order)
    {
        if (!Auth::check()) {
            abort(401);
        }


        /*
        |--------------------------------------------------------------------------
        | Strict Ownership Check
        |--------------------------------------------------------------------------
        */

        if (
            $order->user_id === null ||
            (int) $order->user_id !==
            (int) Auth::id()
        ) {
            abort(403);
        }


        $order->load([
            'items.product',
        ]);


        $isAdmin = false;


        return view(
            'orders.show',
            compact(
                'order',
                'isAdmin'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN ORDER DETAILS
    |--------------------------------------------------------------------------
    */

    public function adminShow(Order $order)
    {
        $order->load([
            'user',
            'items.product',
        ]);


        $isAdmin = true;


        return view(
            'orders.show',
            compact(
                'order',
                'isAdmin'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN ORDERS LIST
    |--------------------------------------------------------------------------
    |
    | Kept for backward compatibility.
    | Primary admin order list is now handled by
    | AdminOrderController.
    |
    */

    public function index(Request $request)
    {
        $query = Order::with([
            'items',
            'user',
        ])->latest();


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim(
                (string) $request->input('search')
            );


            $query->where(function ($q) use ($search) {

                $q->where(
                    'id',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'customer_name',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'phone',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'payment_method',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'status',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'payment_transaction_id',
                    'like',
                    "%{$search}%"
                );
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $status = strtolower(
                trim(
                    (string) $request->input('status')
                )
            );


            $allowedStatuses = [
                'pending',
                'confirmed',
                'processing',
                'shipped',
                'delivered',
                'cancelled',
            ];


            if (
                in_array(
                    $status,
                    $allowedStatuses,
                    true
                )
            ) {
                $query->whereRaw(
                    'LOWER(status) = ?',
                    [$status]
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Payment Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('payment_status')) {

            $paymentStatus = strtolower(
                trim(
                    (string) $request->input('payment_status')
                )
            );


            $allowedPaymentStatuses = [
                'pending',
                'paid',
                'failed',
                'refunded',
            ];


            if (
                in_array(
                    $paymentStatus,
                    $allowedPaymentStatuses,
                    true
                )
            ) {
                $query->whereRaw(
                    'LOWER(payment_status) = ?',
                    [$paymentStatus]
                );
            }
        }


        $orders = $query
            ->paginate(15)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $totalOrders =
            Order::count();


        $pendingOrders =
            Order::whereRaw(
                'LOWER(status) = ?',
                ['pending']
            )->count();


        $confirmedOrders =
            Order::whereRaw(
                'LOWER(status) = ?',
                ['confirmed']
            )->count();


        $processingOrders =
            Order::whereRaw(
                'LOWER(status) = ?',
                ['processing']
            )->count();


        $shippedOrders =
            Order::whereRaw(
                'LOWER(status) = ?',
                ['shipped']
            )->count();


        $deliveredOrders =
            Order::whereRaw(
                'LOWER(status) = ?',
                ['delivered']
            )->count();


        $cancelledOrders =
            Order::whereRaw(
                'LOWER(status) = ?',
                ['cancelled']
            )->count();


        $paidOrders =
            Order::whereRaw(
                'LOWER(payment_status) = ?',
                ['paid']
            )->count();


        /*
        |--------------------------------------------------------------------------
        | Total Sales
        |--------------------------------------------------------------------------
        */

        $totalSales = Order::whereRaw(
            'LOWER(status) != ?',
            ['cancelled']
        )
            ->get()
            ->sum(function ($order) {

                return (float) (
                    $order->total_amount
                    ?? $order->total
                    ?? 0
                );
            });


        /*
        |--------------------------------------------------------------------------
        | Compatibility Variables
        |--------------------------------------------------------------------------
        */

        $orderCount =
            $totalOrders;

        $pendingOrderCount =
            $pendingOrders;

        $processingOrderCount =
            $processingOrders;

        $deliveredOrderCount =
            $deliveredOrders;

        $cancelledOrderCount =
            $cancelledOrders;

        $paidOrderCount =
            $paidOrders;


        return view(
            'admin.orders.index',
            compact(
                'orders',
                'totalOrders',
                'pendingOrders',
                'confirmedOrders',
                'processingOrders',
                'shippedOrders',
                'deliveredOrders',
                'cancelledOrders',
                'paidOrders',
                'totalSales',
                'orderCount',
                'pendingOrderCount',
                'processingOrderCount',
                'deliveredOrderCount',
                'cancelledOrderCount',
                'paidOrderCount'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE ORDER STATUS
    |--------------------------------------------------------------------------
    */

    public function updateStatus(
        Request $request,
        Order $order
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                'in:pending,confirmed,processing,shipped,delivered,cancelled',
            ],
        ]);


        $newStatus = strtolower(
            trim(
                $validated['status']
            )
        );


        $oldStatus = strtolower(
            trim(
                $order->status ?? ''
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Nothing Changed
        |--------------------------------------------------------------------------
        */

        if ($newStatus === $oldStatus) {
            return back()
                ->with(
                    'success',
                    'Order status is already ' .
                    ucfirst($newStatus) .
                    '.'
                );
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | ACTIVE -> CANCELLED
            |--------------------------------------------------------------------------
            |
            | Order creation already deducted stock.
            | Therefore cancellation restores stock once.
            |
            */

            if (
                $newStatus === 'cancelled' &&
                $oldStatus !== 'cancelled'
            ) {

                DB::transaction(function () use ($order) {

                    $order->load('items');


                    foreach ($order->items as $item) {

                        if (!$item->product_id) {
                            continue;
                        }


                        $product = Product::where(
                            'id',
                            $item->product_id
                        )
                            ->lockForUpdate()
                            ->first();


                        if (!$product) {
                            continue;
                        }


                        $product->increment(
                            'stock',
                            (int) $item->quantity
                        );
                    }


                    $order->update([
                        'status' => 'cancelled',
                    ]);
                });


                return back()
                    ->with(
                        'success',
                        'Order cancelled and stock restored successfully.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | CANCELLED -> ACTIVE
            |--------------------------------------------------------------------------
            |
            | Stock is deducted again only after checking availability.
            |
            */

            if (
                $oldStatus === 'cancelled' &&
                $newStatus !== 'cancelled'
            ) {

                DB::transaction(function () use (
                    $order,
                    $newStatus
                ) {

                    $order->load('items');


                    foreach ($order->items as $item) {

                        if (!$item->product_id) {
                            continue;
                        }


                        $product = Product::where(
                            'id',
                            $item->product_id
                        )
                            ->lockForUpdate()
                            ->first();


                        if (!$product) {
                            throw new \Exception(
                                "Product #{$item->product_id} no longer exists."
                            );
                        }


                        $quantity =
                            (int) $item->quantity;


                        if (
                            (int) $product->stock <
                            $quantity
                        ) {
                            throw new \Exception(
                                "Insufficient stock for {$product->name}. " .
                                "Available stock: {$product->stock}."
                            );
                        }


                        $product->decrement(
                            'stock',
                            $quantity
                        );
                    }


                    $order->update([
                        'status' => $newStatus,
                    ]);
                });


                return back()
                    ->with(
                        'success',
                        'Order reactivated and stock deducted successfully.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | NORMAL STATUS UPDATE
            |--------------------------------------------------------------------------
            */

            $order->update([
                'status' => $newStatus,
            ]);


            return back()
                ->with(
                    'success',
                    'Order status updated successfully.'
                );

        } catch (\Throwable $e) {

            report($e);


            return back()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE PAYMENT STATUS
    |--------------------------------------------------------------------------
    */

    public function updatePaymentStatus(
        Request $request,
        Order $order
    ) {
        $validated = $request->validate([
            'payment_status' => [
                'required',
                'string',
                'in:Pending,Paid,Failed,Refunded',
            ],
        ]);


        $paymentStatus =
            $validated['payment_status'];


        $order->update([

            'payment_status' =>
                $paymentStatus,

            'paid_at' =>
                $paymentStatus === 'Paid'
                    ? ($order->paid_at ?? now())
                    : null,
        ]);


        return back()
            ->with(
                'success',
                'Payment status updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE ORDER
    |--------------------------------------------------------------------------
    */

    public function destroy(Order $order)
    {
        /*
        |--------------------------------------------------------------------------
        | Safety Protection
        |--------------------------------------------------------------------------
        |
        | Delivered or paid orders must not be deleted.
        |
        */

        if (
            strtolower(
                $order->status ?? ''
            ) === 'delivered'

            ||

            strtolower(
                $order->payment_status ?? ''
            ) === 'paid'
        ) {
            return back()
                ->with(
                    'error',
                    'Delivered or paid orders cannot be deleted.'
                );
        }


        try {

            DB::transaction(function () use ($order) {

                /*
                |--------------------------------------------------------------------------
                | Restore Stock
                |--------------------------------------------------------------------------
                |
                | Active orders still have reserved stock.
                |
                | Cancelled orders already restored stock when
                | they were cancelled, so do not restore again.
                |
                */

                if (
                    strtolower(
                        $order->status ?? ''
                    ) !== 'cancelled'
                ) {

                    $order->load('items');


                    foreach ($order->items as $item) {

                        if (!$item->product_id) {
                            continue;
                        }


                        $product = Product::where(
                            'id',
                            $item->product_id
                        )
                            ->lockForUpdate()
                            ->first();


                        if (!$product) {
                            continue;
                        }


                        $product->increment(
                            'stock',
                            (int) $item->quantity
                        );
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Delete Order Items
                |--------------------------------------------------------------------------
                */

                $order->items()->delete();


                /*
                |--------------------------------------------------------------------------
                | Delete Order
                |--------------------------------------------------------------------------
                */

                $order->delete();
            });


            /*
            |--------------------------------------------------------------------------
            | IMPORTANT:
            | Redirect back to ADMIN order list.
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'admin.orders.index'
                )
                ->with(
                    'success',
                    'Order deleted successfully.'
                );

        } catch (\Throwable $e) {

            report($e);


            return back()
                ->with(
                    'error',
                    'Unable to delete the order. ' .
                    $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER ORDERS ROUTE COMPATIBILITY
    |--------------------------------------------------------------------------
    */

    public function customerOrders()
    {
        return $this->myOrders();
    }
}