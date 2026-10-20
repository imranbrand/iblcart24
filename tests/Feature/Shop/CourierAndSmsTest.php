<?php

use App\Models\User;
use App\Services\SmsService;
use Illuminate\Support\Facades\Http;

require_once __DIR__ . '/ShopHelpers.php';

test('phone numbers are normalised to the 8801XXXXXXXXX format', function () {
    expect(SmsService::normalizePhone('01712345678'))->toBe('8801712345678');
    expect(SmsService::normalizePhone('+880 1712-345678'))->toBe('8801712345678');
    expect(SmsService::normalizePhone('8801712345678'))->toBe('8801712345678');
    expect(SmsService::normalizePhone('12345'))->toBeNull();
    expect(SmsService::normalizePhone(null))->toBeNull();
});

test('the bulksmsbd driver calls the api and reports success', function () {
    config([
        'logistics.sms.driver'    => 'bulksmsbd',
        'logistics.sms.api_key'   => 'test-key',
        'logistics.sms.sender_id' => 'TESTSENDER',
    ]);

    Http::fake([
        'bulksmsbd.net/*' => Http::response(['response_code' => 202, 'success_message' => 'SMS Submitted Successfully'], 200),
    ]);

    $sent = app(SmsService::class)->send('01712345678', 'Hello');

    expect($sent)->toBeTrue();

    Http::assertSent(fn ($request) => str_contains($request->url(), 'bulksmsbd.net')
        && str_contains($request->url(), 'number=8801712345678'));
});

test('an sms failure never throws an exception', function () {
    config([
        'logistics.sms.driver'    => 'bulksmsbd',
        'logistics.sms.api_key'   => 'test-key',
        'logistics.sms.sender_id' => 'TESTSENDER',
    ]);

    Http::fake(['bulksmsbd.net/*' => Http::response('server error', 500)]);

    expect(app(SmsService::class)->send('01712345678', 'Hello'))->toBeFalse();
});

test('changing an order status sends an sms to the customer', function () {
    config([
        'logistics.sms.driver'    => 'bulksmsbd',
        'logistics.sms.api_key'   => 'test-key',
        'logistics.sms.sender_id' => 'TESTSENDER',
    ]);

    Http::fake(['bulksmsbd.net/*' => Http::response(['response_code' => 202], 200)]);

    $admin    = User::factory()->create(['is_admin' => true]);
    $customer = User::factory()->create();
    $order    = shop_order($customer, ['phone' => '01712345678']);

    $this->actingAs($admin)
        ->patch(route('admin.orders.status.patch', $order), ['status' => 'confirmed'])
        ->assertRedirect(route('admin.orders.show', $order));

    expect($order->fresh()->status)->toBe('confirmed');

    Http::assertSent(fn ($request) => str_contains($request->url(), 'bulksmsbd.net'));
});

test('admin can send an order to steadfast and the order becomes shipped', function () {
    config([
        'logistics.steadfast.enabled'    => true,
        'logistics.steadfast.api_key'    => 'k',
        'logistics.steadfast.secret_key' => 's',
    ]);

    Http::fake([
        'portal.packzy.com/*' => Http::response([
            'status'      => 200,
            'message'     => 'Consignment has been created successfully.',
            'consignment' => [
                'consignment_id' => 1424107,
                'tracking_code'  => '15BAEB8A',
                'status'         => 'in_review',
            ],
        ], 200),
    ]);

    $admin    = User::factory()->create(['is_admin' => true]);
    $customer = User::factory()->create();
    $order    = shop_order($customer, ['status' => 'confirmed', 'phone' => '01712345678']);

    $this->actingAs($admin)
        ->post(route('admin.orders.courier.send', $order))
        ->assertRedirect(route('admin.orders.show', $order));

    $order->refresh();

    expect($order->courier_name)->toBe('Steadfast');
    expect((string) $order->courier_consignment_id)->toBe('1424107');
    expect($order->courier_tracking_code)->toBe('15BAEB8A');
    expect($order->status)->toBe('shipped');

    Http::assertSent(fn ($request) => str_contains($request->url(), '/create_order')
        && $request->hasHeader('Api-Key', 'k')
        && $request->hasHeader('Secret-Key', 's'));
});

test('an order already sent to the courier is not sent twice', function () {
    config([
        'logistics.steadfast.enabled'    => true,
        'logistics.steadfast.api_key'    => 'k',
        'logistics.steadfast.secret_key' => 's',
    ]);

    Http::fake();

    $admin    = User::factory()->create(['is_admin' => true]);
    $customer = User::factory()->create();
    $order    = shop_order($customer, [
        'status'                 => 'shipped',
        'courier_name'           => 'Steadfast',
        'courier_consignment_id' => '999',
    ]);

    $this->actingAs($admin)->post(route('admin.orders.courier.send', $order));

    Http::assertNothingSent();
});

test('steadfast does nothing when it is not enabled', function () {
    config(['logistics.steadfast.enabled' => false]);

    Http::fake();

    $admin    = User::factory()->create(['is_admin' => true]);
    $customer = User::factory()->create();
    $order    = shop_order($customer, ['phone' => '01712345678']);

    $this->actingAs($admin)->post(route('admin.orders.courier.send', $order));

    Http::assertNothingSent();
    expect($order->fresh()->courier_consignment_id)->toBeNull();
});

test('a normal customer cannot send orders to a courier', function () {
    $customer = User::factory()->create();
    $order    = shop_order($customer);

    $this->actingAs($customer)
        ->post(route('admin.orders.courier.send', $order))
        ->assertForbidden();
});
