<?php

use App\Http\Controllers\CheckoutController;

require_once __DIR__ . '/ShopHelpers.php';

test('static pages load', function (string $route) {
    $this->get(route($route))->assertOk();
})->with([
    'pages.about',
    'pages.contact',
    'pages.privacy',
    'pages.terms',
    'pages.returns',
    'pages.shipping',
]);

test('delivery charge depends on the area', function () {
    config([
        'shop.delivery.inside_dhaka'  => 60,
        'shop.delivery.outside_dhaka' => 120,
        'shop.delivery.free_above'    => 0,
    ]);

    expect(CheckoutController::deliveryFor('inside_dhaka', 500))->toBe(60.0);
    expect(CheckoutController::deliveryFor('outside_dhaka', 500))->toBe(120.0);
    expect(CheckoutController::deliveryFor('something-else', 500))->toBe(60.0);
});

test('delivery is free above the threshold', function () {
    config([
        'shop.delivery.inside_dhaka'  => 60,
        'shop.delivery.outside_dhaka' => 120,
        'shop.delivery.free_above'    => 1000,
    ]);

    expect(CheckoutController::deliveryFor('outside_dhaka', 999))->toBe(120.0);
    expect(CheckoutController::deliveryFor('outside_dhaka', 1000))->toBe(0.0);
});
