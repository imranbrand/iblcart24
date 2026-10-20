<?php

use App\Models\Product;
use App\Models\User;

require_once __DIR__ . '/ShopHelpers.php';

test('sale price and discount percent are calculated', function () {
    $p = shop_product(['price' => 800, 'compare_price' => 1000]);

    expect($p->is_on_sale)->toBeTrue();
    expect($p->discount_percent)->toBe(20);
});

test('a product without a regular price is not on sale', function () {
    $p = shop_product(['price' => 800]);

    expect($p->is_on_sale)->toBeFalse();
    expect($p->discount_percent)->toBe(0);
});

test('a regular price below the price is not a sale', function () {
    $p = shop_product(['price' => 800, 'compare_price' => 500]);

    expect($p->is_on_sale)->toBeFalse();
});

test('order again puts the items back in the cart', function () {
    $user = User::factory()->create();
    $product = shop_product(['price' => 100, 'stock' => 10]);
    $order = shop_order($user, [], $product);

    $this->actingAs($user)
        ->post(route('orders.reorder', $order))
        ->assertRedirect(route('cart.index'))
        ->assertSessionHas('cart.' . $product->id . '.quantity', 2)
        ->assertSessionHas('cart.' . $product->id . '.price', 100.0);
});

test('order again is not allowed for someone elses order', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $order = shop_order($owner, [], shop_product());

    $this->actingAs($other)->post(route('orders.reorder', $order))->assertForbidden();
});

test('the faq page loads', function () {
    $this->get(route('pages.faq'))->assertOk();
});

test('the product page loads with related and recently viewed sections', function () {
    $a = shop_product(['name' => 'Alpha']);
    $b = shop_product(['name' => 'Beta']);

    $this->get(route('products.show', $a))->assertOk();

    $this->withSession(['recently_viewed' => [$a->id]])
        ->get(route('products.show', $b))
        ->assertOk()
        ->assertSee('Recently viewed');
});
