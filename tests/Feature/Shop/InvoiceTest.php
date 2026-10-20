<?php

use App\Models\User;

require_once __DIR__ . '/ShopHelpers.php';

test('the owner can download the invoice', function () {
    $user = User::factory()->create();
    $order = shop_order($user, [], shop_product());

    $response = $this->actingAs($user)->get(route('orders.invoice', $order));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('pdf');
});

test('another customer cannot download the invoice', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $order = shop_order($owner);

    $this->actingAs($other)->get(route('orders.invoice', $order))->assertForbidden();
});

test('an admin can download any invoice', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);
    $order = shop_order($owner, [], shop_product());

    $this->actingAs($admin)->get(route('orders.invoice', $order))->assertOk();
});

test('a guest is sent to login', function () {
    $owner = User::factory()->create();
    $order = shop_order($owner);

    $this->get(route('orders.invoice', $order))->assertRedirect(route('login'));
});
