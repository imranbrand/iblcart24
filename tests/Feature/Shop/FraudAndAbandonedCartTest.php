<?php

use App\Models\AbandonedCart;
use App\Models\BlockedPhone;
use App\Models\Order;
use App\Models\User;
use App\Services\FakeOrderGuard;

require_once __DIR__ . '/ShopHelpers.php';

beforeEach(function () {
    config([
        'shop.delivery.inside_dhaka'  => 60,
        'shop.delivery.outside_dhaka' => 120,
        'shop.delivery.free_above'    => 0,
    ]);
});

function saved_cart(array $o = []): AbandonedCart
{
    return AbandonedCart::create(array_merge([
        'phone'        => '8801712345678',
        'items'        => [['product_id' => 1, 'name' => 'Test Product', 'price' => 100, 'quantity' => 2]],
        'total'        => 200,
        'token'        => 'tok_' . uniqid(),
        'last_seen_at' => now()->subMinutes(120),
    ], $o));
}

test('blocked phone cannot place an order', function () {
    BlockedPhone::create(['phone' => '8801712345678']);

    $this->actingAs(User::factory()->create())
        ->withSession(['cart' => shop_cart(shop_product(), 1)])
        ->post(route('checkout.store'), shop_checkout_data(['phone' => '01712-345678']))
        ->assertSessionHasErrors('phone');

    expect(Order::count())->toBe(0);
});

test('guard allows a normal phone', function () {
    expect(app(FakeOrderGuard::class)->check('01911111111', 'Cash on Delivery'))->toBeNull();
});

test('repeat cancellers must pay first, not cash on delivery', function () {
    $user = User::factory()->create();

    foreach (range(1, 3) as $i) {
        shop_order($user, ['phone' => '01812345678', 'status' => 'cancelled']);
    }

    $guard = app(FakeOrderGuard::class);

    expect($guard->check('01812345678', 'Cash on Delivery'))->not->toBeNull();
    expect($guard->check('01812345678', 'Online Payment'))->toBeNull();
});

test('too many orders from one phone in a day are refused', function () {
    config(['shop.fraud.max_orders_per_day' => 2]);
    $user = User::factory()->create();

    shop_order($user, ['phone' => '01612345678']);
    shop_order($user, ['phone' => '01612345678']);

    expect(app(FakeOrderGuard::class)->check('01612345678', 'Online Payment'))->not->toBeNull();
});

test('checkout page saves the cart with the phone', function () {
    $product = shop_product();

    $this->withSession(['cart' => shop_cart($product, 2)])
        ->postJson(route('checkout.capture'), ['phone' => '01712345678', 'customer_name' => 'Rahim'])
        ->assertOk()
        ->assertJson(['ok' => true]);

    $row = AbandonedCart::first();

    expect($row->phone)->toBe('8801712345678');
    expect((float) $row->total)->toBe(200.0);
});

test('nothing is saved for an empty cart or a bad phone', function () {
    $this->postJson(route('checkout.capture'), ['phone' => '01712345678'])->assertJson(['ok' => false]);
    $this->withSession(['cart' => shop_cart(shop_product(), 1)])
        ->postJson(route('checkout.capture'), ['phone' => '123'])->assertJson(['ok' => false]);

    expect(AbandonedCart::count())->toBe(0);
});

test('reminder is sent once after the delay', function () {
    $cart = saved_cart();

    $this->artisan('shop:abandoned-reminders')->assertSuccessful();
    expect($cart->fresh()->reminded_at)->not->toBeNull();

    $first = $cart->fresh()->reminded_at;
    $this->artisan('shop:abandoned-reminders')->assertSuccessful();
    expect($cart->fresh()->reminded_at->equalTo($first))->toBeTrue();
});

test('fresh carts are not reminded yet', function () {
    $cart = saved_cart(['last_seen_at' => now()->subMinutes(5)]);

    $this->artisan('shop:abandoned-reminders')->assertSuccessful();
    expect($cart->fresh()->reminded_at)->toBeNull();
});

test('reminder link puts the items back and opens checkout', function () {
    $product = shop_product(['stock' => 10]);
    $cart = saved_cart(['items' => [['product_id' => $product->id, 'name' => 'x', 'price' => 1, 'quantity' => 3]]]);

    $this->get(route('cart.recover', $cart->token))
        ->assertRedirect(route('checkout.index'))
        ->assertSessionHas('cart');

    expect(session('cart')[$product->id]['quantity'])->toBe(3);
});

test('placing an order stops the reminder', function () {
    $cart = saved_cart(['phone' => '8801700000000']);

    $this->actingAs(User::factory()->create())
        ->withSession(['cart' => shop_cart(shop_product(), 1)])
        ->post(route('checkout.store'), shop_checkout_data())
        ->assertSessionHasNoErrors();

    expect($cart->fresh()->recovered_at)->not->toBeNull();
});
