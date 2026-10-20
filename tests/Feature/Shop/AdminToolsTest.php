<?php

use App\Models\Review;
use App\Models\Setting;
use App\Models\User;

require_once __DIR__ . '/ShopHelpers.php';

test('admin can verify a bKash payment', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $customer = User::factory()->create();
    $order = shop_order($customer, [
        'payment_method'         => 'bKash',
        'payment_transaction_id' => 'ABC123XYZ',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.orders.payment.verify', $order))
        ->assertRedirect(route('admin.orders.show', $order));

    $order->refresh();

    expect(strtolower($order->payment_status))->toBe('paid');
    expect($order->paid_at)->not->toBeNull();
});

test('cash on delivery orders cannot be verified as paid', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $customer = User::factory()->create();
    $order = shop_order($customer);

    $this->actingAs($admin)->patch(route('admin.orders.payment.verify', $order));

    expect(strtolower($order->fresh()->payment_status))->toBe('pending');
});

test('a normal customer cannot verify payments', function () {
    $customer = User::factory()->create();
    $order = shop_order($customer, [
        'payment_method'         => 'Nagad',
        'payment_transaction_id' => 'NGD999',
    ]);

    $this->actingAs($customer)
        ->patch(route('admin.orders.payment.verify', $order))
        ->assertForbidden();

    expect(strtolower($order->fresh()->payment_status))->toBe('pending');
});

test('admin can hide and show a review', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $customer = User::factory()->create();
    $product = shop_product();

    $review = Review::create([
        'product_id'  => $product->id,
        'user_id'     => $customer->id,
        'rating'      => 4,
        'comment'     => 'Nice',
        'is_approved' => true,
    ]);

    $this->actingAs($admin)->patch(route('admin.reviews.toggle', $review));
    expect($review->fresh()->is_approved)->toBeFalse();

    $this->actingAs($admin)->patch(route('admin.reviews.toggle', $review));
    expect($review->fresh()->is_approved)->toBeTrue();
});

test('new reviews wait for approval when the setting is on', function () {
    config(['shop.reviews_require_approval' => true]);

    $customer = User::factory()->create();
    $product = shop_product();
    shop_order($customer, [], $product);

    $this->actingAs($customer)
        ->post(route('reviews.store', $product), ['rating' => 5, 'comment' => 'Great'])
        ->assertSessionHasNoErrors();

    expect(Review::first()->is_approved)->toBeFalse();
});

test('new reviews show at once when approval is off', function () {
    config(['shop.reviews_require_approval' => false]);

    $customer = User::factory()->create();
    $product = shop_product();
    shop_order($customer, [], $product);

    $this->actingAs($customer)
        ->post(route('reviews.store', $product), ['rating' => 5, 'comment' => 'Great']);

    expect(Review::first()->is_approved)->toBeTrue();
});

test('admin can save shop settings', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->put(route('admin.settings.update'), [
            'name'                   => 'My Shop',
            'return_days'            => 10,
            'delivery_inside_dhaka'  => 70,
            'delivery_outside_dhaka' => 150,
            'free_delivery_above'    => 2500,
            'phone'                  => '01711111111',
        ])
        ->assertSessionHasNoErrors();

    expect((float) config('shop.delivery.outside_dhaka'))->toBe(150.0);
    expect((float) config('shop.delivery.free_above'))->toBe(2500.0);
    expect(config('shop.name'))->toBe('My Shop');
    expect(Setting::where('key', 'delivery_outside_dhaka')->exists())->toBeTrue();
});

test('a customer cannot open the settings page', function () {
    $customer = User::factory()->create();

    $this->actingAs($customer)->get(route('admin.settings.edit'))->assertForbidden();
});
