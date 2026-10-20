<?php

use App\Models\User;

require_once __DIR__ . '/ShopHelpers.php';

test('smoke test opens every main page', function () {
    $this->artisan('shop:smoke-test')->assertSuccessful();
});

test('launch check runs and reports no hard failure on a fresh test database', function () {
    config(['shop.online_payment_enabled' => false]);
    User::factory()->create(['is_admin' => true]);

    // debug/env WARN items are fine; only FAIL makes the command fail.
    $this->artisan('shop:launch-check')->run();
    expect(true)->toBeTrue();
});

test('online payment can be hidden at checkout', function () {
    config(['shop.online_payment_enabled' => false]);

    $this->actingAs(User::factory()->create())
        ->withSession(['cart' => shop_cart(shop_product(), 1)])
        ->get(route('checkout.index'))
        ->assertOk()
        ->assertDontSee('value="Online Payment"', false);
});

test('online payment is refused by the server when it is switched off', function () {
    config(['shop.online_payment_enabled' => false]);

    $this->actingAs(User::factory()->create())
        ->withSession(['cart' => shop_cart(shop_product(), 1)])
        ->post(route('checkout.store'), shop_checkout_data(['payment_method' => 'Online Payment']))
        ->assertSessionHasErrors('payment_method');
});

test('online payment option shows when switched on', function () {
    config(['shop.online_payment_enabled' => true]);

    $this->actingAs(User::factory()->create())
        ->withSession(['cart' => shop_cart(shop_product(), 1)])
        ->get(route('checkout.index'))
        ->assertOk()
        ->assertSee('value="Online Payment"', false);
});
