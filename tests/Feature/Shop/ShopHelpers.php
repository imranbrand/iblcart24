<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

if (!function_exists('shop_product')) {
    function shop_product(array $o = []): Product
    {
        return Product::create(array_merge([
            'name'        => 'Test Product',
            'description' => 'A product for testing',
            'price'       => 100,
            'stock'       => 10,
        ], $o));
    }
}

if (!function_exists('shop_cart')) {
    function shop_cart(Product $p, int $qty = 2): array
    {
        return [
            $p->id => [
                'product_id' => $p->id,
                'name'       => $p->name,
                'price'      => (float) $p->price,
                'quantity'   => $qty,
                'image'      => null,
            ],
        ];
    }
}

if (!function_exists('shop_checkout_data')) {
    function shop_checkout_data(array $o = []): array
    {
        return array_merge([
            'customer_name'  => 'Test Buyer',
            'phone'          => '01700000000',
            'address'        => 'House 1, Road 2, Dhaka',
            'delivery_area'  => 'inside_dhaka',
            'payment_method' => 'Cash on Delivery',
        ], $o);
    }
}

if (!function_exists('shop_order')) {
    function shop_order(User $user, array $o = [], ?Product $product = null): Order
    {
        $order = Order::create(array_merge([
            'user_id'        => $user->id,
            'customer_name'  => 'Test Buyer',
            'phone'          => '01700000000',
            'address'        => 'Dhaka',
            'subtotal'       => 200,
            'delivery_charge' => 60,
            'total'          => 260,
            'total_amount'   => 260,
            'payment_method' => 'Cash on Delivery',
            'payment_status' => 'Pending',
            'status'         => 'pending',
        ], $o));

        if ($product) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'price'        => $product->price,
                'quantity'     => 2,
                'subtotal'     => (float) $product->price * 2,
            ]);
        }

        return $order;
    }
}
