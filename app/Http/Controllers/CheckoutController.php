<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Delivery rates per area (configured in config/shop.php / .env).
     */
    public static function deliveryRates(): array
    {
        return [
            'inside_dhaka'  => (float) config('shop.delivery.inside_dhaka', 60),
            'outside_dhaka' => (float) config('shop.delivery.outside_dhaka', 120),
        ];
    }

    /**
     * Delivery charge for an area. Always calculated on the server.
     */
    public static function deliveryFor(?string $area, float $subtotal = 0): float
    {
        $rates = self::deliveryRates();
        $freeAbove = (float) config('shop.delivery.free_above', 0);

        if ($freeAbove > 0 && $subtotal >= $freeAbove) {
            return 0.0;
        }

        return $rates[$area] ?? $rates['inside_dhaka'];
    }


    /**
     * Show checkout page.
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $key => $item) {

            $productId = $item['product_id'] ?? $key;

            $product = Product::find($productId);

            /*
            |--------------------------------------------------------------------------
            | Remove deleted products
            |--------------------------------------------------------------------------
            */

            if (!$product) {
                unset($cart[$key]);
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Quantity
            |--------------------------------------------------------------------------
            */

            $quantity = max(
                1,
                (int) ($item['quantity'] ?? 1)
            );

            /*
            |--------------------------------------------------------------------------
            | Stock validation
            |--------------------------------------------------------------------------
            */

            if ($product->stock <= 0) {
                unset($cart[$key]);
                continue;
            }

            if ($quantity > $product->stock) {
                $quantity = $product->stock;
            }

            /*
            |--------------------------------------------------------------------------
            | Current database price
            |--------------------------------------------------------------------------
            */

            $price = (float) $product->price;

            $itemSubtotal = $price * $quantity;

            $subtotal += $itemSubtotal;

            /*
            |--------------------------------------------------------------------------
            | Checkout item
            |--------------------------------------------------------------------------
            */

            $cartItems[] = [
                'product' => $product,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $itemSubtotal,
            ];

            /*
            |--------------------------------------------------------------------------
            | Synchronize session cart
            |--------------------------------------------------------------------------
            */

            $cart[$key] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $price,
                'quantity' => $quantity,
                'image' => $product->image ?? null,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Save cleaned cart
        |--------------------------------------------------------------------------
        */

        session()->put('cart', $cart);

        /*
        |--------------------------------------------------------------------------
        | No valid products
        |--------------------------------------------------------------------------
        */

        if (empty($cartItems)) {

            session()->forget('cart');

            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'All products in your cart are unavailable.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Delivery
        |--------------------------------------------------------------------------
        */

        $deliveryRates = self::deliveryRates();
        $deliveryArea = 'inside_dhaka';
        $deliveryCharge = self::deliveryFor($deliveryArea, (float) $subtotal);

        /*
        |--------------------------------------------------------------------------
        | Coupon (optional)
        |--------------------------------------------------------------------------
        */

        $couponCode = session('coupon_code');
        $discount = 0;
        $coupon = null;

        if ($couponCode) {
            $coupon = \App\Models\Coupon::findValidByCode($couponCode);

            if ($coupon) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                // Coupon became invalid (expired/used up) since it was applied.
                session()->forget('coupon_code');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Total
        |--------------------------------------------------------------------------
        */

        $total = $subtotal + $deliveryCharge - $discount;

        return view(
            'checkout.index',
            compact(
                'cartItems',
                'subtotal',
                'deliveryCharge',
                'deliveryRates',
                'deliveryArea',
                'discount',
                'coupon',
                'total'
            )
        );
    }


    /**
     * Place order.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validate basic checkout information
        |--------------------------------------------------------------------------
        */

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

            'delivery_area' => [
                'required',
                'in:inside_dhaka,outside_dhaka',
            ],

            'payment_method' => [
                'required',
                'in:Cash on Delivery,bKash,Nagad' . (config('shop.online_payment_enabled', true) ? ',Online Payment' : ''),
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
        | Fake-order protection (blocked phone / too many orders / repeat cancels)
        |--------------------------------------------------------------------------
        */

        $guardMessage = app(\App\Services\FakeOrderGuard::class)
            ->check($validated['phone'], $validated['payment_method']);

        if ($guardMessage !== null) {
            return back()
                ->withInput()
                ->withErrors(['phone' => $guardMessage]);
        }

        /*
        |--------------------------------------------------------------------------
        | Additional payment validation
        |--------------------------------------------------------------------------
        */

        $paymentMethod =
            $validated['payment_method'];

        $transactionId =
            trim(
                (string) (
                    $validated['payment_transaction_id']
                    ?? ''
                )
            );

        /*
        |--------------------------------------------------------------------------
        | bKash / Nagad require transaction ID
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $paymentMethod,
                ['bKash', 'Nagad'],
                true
            )
            &&
            $transactionId === ''
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'payment_transaction_id' =>
                        "Transaction ID is required for {$paymentMethod}.",
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Cash on Delivery must not use transaction ID
        |--------------------------------------------------------------------------
        */

        if ($paymentMethod === 'Cash on Delivery') {
            $transactionId = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Online Payment transaction ID is generated by SSLCommerz.
        |--------------------------------------------------------------------------
        */

        if ($paymentMethod === 'Online Payment') {
            $transactionId = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Get cart
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

        /*
        |--------------------------------------------------------------------------
        | Coupon (optional) - captured before the transaction
        |--------------------------------------------------------------------------
        */

        $appliedCouponCode = session('coupon_code');

        /*
        |--------------------------------------------------------------------------
        | Create order safely inside database transaction
        |--------------------------------------------------------------------------
        */

        try {
            $order = DB::transaction(function () use (
                $cart,
                $validated,
                $paymentMethod,
                $transactionId,
                $appliedCouponCode
            ) {
    
                $subtotal = 0;
    
                $items = [];
    
                /*
                |--------------------------------------------------------------------------
                | Lock products and validate stock
                |--------------------------------------------------------------------------
                */
    
                foreach ($cart as $key => $item) {
    
                    $productId =
                        $item['product_id']
                        ?? $key;
    
                    $product = Product::query()
                        ->lockForUpdate()
                        ->find($productId);
    
                    /*
                    |--------------------------------------------------------------------------
                    | Product no longer exists
                    |--------------------------------------------------------------------------
                    */
    
                    if (!$product) {
                        continue;
                    }
    
                    /*
                    |--------------------------------------------------------------------------
                    | Quantity
                    |--------------------------------------------------------------------------
                    */
    
                    $quantity = max(
                        1,
                        (int) ($item['quantity'] ?? 1)
                    );
    
                    /*
                    |--------------------------------------------------------------------------
                    | Stock validation
                    |--------------------------------------------------------------------------
                    */
    
                    if ($product->stock <= 0) {
    
                        throw new \DomainException("{$product->name} is currently out of stock.");
                    }
    
                    if ($quantity > $product->stock) {
    
                        throw new \DomainException("Insufficient stock for {$product->name}. Only {$product->stock} item(s) available.");
                    }
    
                    /*
                    |--------------------------------------------------------------------------
                    | Always use current database price
                    |--------------------------------------------------------------------------
                    */
    
                    $price =
                        (float) $product->price;
    
                    $itemSubtotal =
                        $price * $quantity;
    
                    $subtotal += $itemSubtotal;
    
                    /*
                    |--------------------------------------------------------------------------
                    | Save temporary item data
                    |--------------------------------------------------------------------------
                    */
    
                    $items[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $itemSubtotal,
                    ];
                }
    
                /*
                |--------------------------------------------------------------------------
                | Validate items
                |--------------------------------------------------------------------------
                */
    
                if (empty($items)) {
    
                    throw new \DomainException('Your cart contains no valid products.');
                }
    
                /*
                |--------------------------------------------------------------------------
                | Delivery charge
                |--------------------------------------------------------------------------
                */
    
                $deliveryCharge = self::deliveryFor(
                    $validated['delivery_area'],
                    (float) $subtotal
                );
    
                /*
                |--------------------------------------------------------------------------
                | Coupon (re-validated here so it can't be tampered with client-side)
                |--------------------------------------------------------------------------
                */
    
                $discount = 0;
                $appliedCoupon = null;
    
                if ($appliedCouponCode) {
                    $candidate = \App\Models\Coupon::findValidByCode($appliedCouponCode);
    
                    if ($candidate) {
                        // Lock the row so two simultaneous orders cannot both
                        // use the last remaining redemption of a coupon.
                        $appliedCoupon = \App\Models\Coupon::query()
                            ->lockForUpdate()
                            ->find($candidate->id);
    
                        if ($appliedCoupon && $appliedCoupon->isCurrentlyValid()) {
                            $discount = $appliedCoupon->calculateDiscount($subtotal);
                        } else {
                            $appliedCoupon = null;
                        }
    
                        // Coupon gives no discount (e.g. below minimum order): don't record it.
                        if ($appliedCoupon && $discount <= 0) {
                            $appliedCoupon = null;
                        }
                    }
                }
    
                /*
                |--------------------------------------------------------------------------
                | Total
                |--------------------------------------------------------------------------
                */
    
                $total =
                    $subtotal +
                    $deliveryCharge -
                    $discount;
    
                /*
                |--------------------------------------------------------------------------
                | Payment status
                |--------------------------------------------------------------------------
                */
    
                $paymentStatus =
                    'Pending';
    
                /*
                |--------------------------------------------------------------------------
                | Payment gateway
                |--------------------------------------------------------------------------
                */
    
                $paymentGateway = null;
    
                if ($paymentMethod === 'bKash') {
                    $paymentGateway = 'bKash';
                }
    
                if ($paymentMethod === 'Nagad') {
                    $paymentGateway = 'Nagad';
                }
    
                if ($paymentMethod === 'Online Payment') {
                    $paymentGateway = 'SSLCommerz';
                }
    
                /*
                |--------------------------------------------------------------------------
                | Create order
                |--------------------------------------------------------------------------
                */
    
                $order = Order::create([
    
                    'user_id' =>
                        auth()->id(),
    
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
                        $transactionId,
    
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
    
                    'coupon_code' =>
                        $appliedCoupon?->code,
    
                    'discount_amount' =>
                        $discount,
                ]);
    
                /*
                |--------------------------------------------------------------------------
                | Record coupon usage
                |--------------------------------------------------------------------------
                */
    
                if ($appliedCoupon) {
                    $appliedCoupon->increment('used_count');
                }
    
                /*
                |--------------------------------------------------------------------------
                | Create order items and decrease stock
                |--------------------------------------------------------------------------
                */
    
                foreach ($items as $item) {
    
                    $product =
                        $item['product'];
    
                    OrderItem::create([
    
                        'order_id' =>
                            $order->id,
    
                        'product_id' =>
                            $product->id,
    
                        'product_name' =>
                            $product->name,
    
                        'quantity' =>
                            $item['quantity'],
    
                        'price' =>
                            $item['price'],
    
                        'subtotal' =>
                            $item['subtotal'],
                    ]);
    
                    /*
                    |--------------------------------------------------------------------------
                    | Decrease stock
                    |--------------------------------------------------------------------------
                    */
    
                    $product->decrement(
                        'stock',
                        $item['quantity']
                    );
                }
    
                return $order;
            });
        } catch (\DomainException $e) {
            return redirect()
                ->route('cart.index')
                ->with('error', $e->getMessage());
        }

        /*
        |--------------------------------------------------------------------------
        | Clear cart and coupon after successful order creation
        |--------------------------------------------------------------------------
        */

        session()->forget('cart');
        session()->forget('coupon_code');

        /*
        |--------------------------------------------------------------------------
        | Send order confirmation email (best-effort; never block checkout)
        |--------------------------------------------------------------------------
        */

        try {
            $recipientEmail = $order->user?->email;

            if ($recipientEmail) {
                \Illuminate\Support\Facades\Mail::to($recipientEmail)
                    ->send(new \App\Mail\OrderConfirmationMail($order));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        /*
        |--------------------------------------------------------------------------
        | Stop abandoned-cart reminders for this customer
        |--------------------------------------------------------------------------
        */

        try {
            \App\Models\AbandonedCart::markRecovered($order->phone);
        } catch (\Throwable $e) {
            report($e);
        }

        /*
        |--------------------------------------------------------------------------
        | Order received SMS (best-effort; never block checkout)
        |--------------------------------------------------------------------------
        */

        try {
            app(\App\Services\SmsService::class)->notifyOrderPlaced($order);
        } catch (\Throwable $e) {
            report($e);
        }

        /*
        |--------------------------------------------------------------------------
        | SSLCommerz
        |--------------------------------------------------------------------------
        |
        | Online Payment orders must go to SSLCommerz.
        |
        */

        if ($paymentMethod === 'Online Payment') {

            return redirect()
                ->route(
                    'payment.sslcommerz.initiate',
                    $order
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Cash / bKash / Nagad
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'orders.show',
                $order
            )
            ->with(
                'success',
                'Your order has been placed successfully!'
            )
            ->with('track_purchase', true);
    }
}


