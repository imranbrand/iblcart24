<?php

/*
|--------------------------------------------------------------------------
| Shop settings (IBL CART 24)
|--------------------------------------------------------------------------
| Defaults come from .env. Values saved in Admin > Settings override these.
| IMPORTANT: this file name must be all lowercase (shop.php).
*/

return [

    'name'     => env('SHOP_NAME', 'IBL CART 24'),
    'email'    => env('SHOP_EMAIL', ''),
    'phone'    => env('SHOP_PHONE', ''),
    'whatsapp' => env('SHOP_WHATSAPP', ''),
    'address'  => env('SHOP_ADDRESS', ''),
    'hours'    => env('SHOP_HOURS', ''),

    'return_days' => (int) env('SHOP_RETURN_DAYS', 7),

    'reviews_require_approval' => (bool) env('SHOP_REVIEWS_REQUIRE_APPROVAL', false),

    'announcement_enabled' => (bool) env('SHOP_ANNOUNCEMENT_ENABLED', false),
    'announcement_text'    => env('SHOP_ANNOUNCEMENT_TEXT', ''),

    'delivery' => [
        'inside_dhaka'  => (float) env('SHOP_DELIVERY_INSIDE_DHAKA', 80),
        'outside_dhaka' => (float) env('SHOP_DELIVERY_OUTSIDE_DHAKA', 150),

        // Free delivery when the subtotal reaches this amount. 0 = disabled.
        'free_above'    => (float) env('SHOP_FREE_DELIVERY_ABOVE', 0),

        'inside_days'   => env('SHOP_DELIVERY_DAYS_INSIDE', '1-2'),
        'outside_days'  => env('SHOP_DELIVERY_DAYS_OUTSIDE', '3-5'),
    ],

    // Marketing / SEO (leave empty to switch off)
    'fb_pixel_id' => env('FB_PIXEL_ID', ''),
    'ga_id'       => env('GA_MEASUREMENT_ID', ''),
    'og_image'    => env('SHOP_OG_IMAGE', ''),

    // Fake-order protection (blocked phones are managed in admin)
    'fraud' => [
        'enabled'            => (bool) env('FRAUD_GUARD_ENABLED', true),
        'max_orders_per_day' => (int) env('FRAUD_MAX_ORDERS_PER_DAY', 5),
        'max_cancelled'      => (int) env('FRAUD_MAX_CANCELLED', 3),
    ],

    // One reminder SMS for carts left at checkout
    'abandoned_cart' => [
        'enabled'       => (bool) env('ABANDONED_CART_ENABLED', true),
        'delay_minutes' => (int) env('ABANDONED_CART_DELAY_MINUTES', 60),
        'max_age_hours' => (int) env('ABANDONED_CART_MAX_AGE_HOURS', 72),
    ],

    // Show the SSLCommerz "Online Payment" option at checkout. Set false until the gateway is live.
    'online_payment_enabled' => (bool) env('ONLINE_PAYMENT_ENABLED', true),

];
