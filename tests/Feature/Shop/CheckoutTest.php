<?php

use App\Models\Order;
use App\Models\User;

require_once __DIR__ . '/ShopHelpers.php';

beforeEach(function () {
    config([
        'shop.delivery.inside_dhaka'  => 60,
        'shop.delivery.outside_dhaka' => 120,
        'shop.delivery.free_above'    => 0,
    ]);
});

test('cash on delivery order inside dhaka', function () {
    $user = User::factory()->create();
    $product = shop_product(['price' => 100, 'stock' => 10]);

    $this->actingAs($user)
        ->withSession(['cart' => shop_cart($product, 2)])
        ->post(route('checkout.store'), shop_checkout_data())
        ->assertSessionHasNoErrors();

    $order = Order::first();

    expect($order)->not->toBeNull();
    expect((float) $order->delivery_charge)->toBe(60.0);
    expect((float) $order->total)->toBe(260.0);
    expect($order->items()->count())->toBe(1);
    expect($product->fresh()->stock)->toBe(8);
});

test('order outside dhaka uses the outside rate', function () {
    $user = User::factory()->create();
    $product = shop_product(['price' => 100, 'stock' => 10]);

    $this->actingAs($user)
        ->withSession(['cart' => shop_cart($product, 2)])
        ->post(route('checkout.store'), shop_checkout_data(['delivery_area' => 'outside_dhaka']))
        ->assertSessionHasNoErrors();

    $order = Order::first();

    expect((float) $order->delivery_charge)->toBe(120.0);
    expect((float) $order->total)->toBe(320.0);
});

test('delivery area is required', function () {
    $user = User::factory()->create();
    $product = shop_product();

    $data = shop_checkout_data();
    unset($data['delivery_area']);

    $this->actingAs($user)
        ->withSession(['cart' => shop_cart($product)])
        ->post(route('checkout.store'), $data)
        ->assertSessionHasErrors('delivery_area');

    expect(Order::count())->toBe(0);
});

test('a made-up delivery area is rejected', function () {
    $user = User::factory()->create();
    $product = shop_product();

    $this->actingAs($user)
        ->withSession(['cart' => shop_cart($product)])
        ->post(route('checkout.store'), shop_checkout_data(['delivery_area' => 'free']))
        ->assertSessionHasErrors('delivery_area');

    expect(Order::count())->toBe(0);
});

test('cannot order more than the stock', function () {
    $user = User::factory()->create();
    $product = shop_product(['stock' => 3]);

    $this->actingAs($user)
        ->withSession(['cart' => shop_cart($product, 50)])
        ->post(route('checkout.store'), shop_checkout_data());

    expect(Order::count())->toBe(0);
    expect($product->fresh()->stock)->toBe(3);
});
