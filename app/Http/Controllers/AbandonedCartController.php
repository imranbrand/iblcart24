<?php

namespace App\Http\Controllers;

use App\Models\AbandonedCart;
use App\Models\Product;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AbandonedCartController extends Controller
{
    /**
     * Called by the checkout page when the customer has typed a phone number.
     * Saves a snapshot of the cart so we can send one reminder SMS later.
     */
    public function capture(Request $request): JsonResponse
    {
        if (! config('shop.abandoned_cart.enabled', true)) {
            return response()->json(['ok' => false]);
        }

        $phone = SmsService::normalizePhone($request->input('phone'));
        $cart  = session()->get('cart', []);

        if ($phone === null || empty($cart)) {
            return response()->json(['ok' => false]);
        }

        $items = collect($cart)
            ->map(fn ($i) => [
                'product_id' => $i['product_id'] ?? $i['id'] ?? null,
                'name'       => (string) ($i['name'] ?? ''),
                'price'      => (float) ($i['price'] ?? 0),
                'quantity'   => max(1, (int) ($i['quantity'] ?? 1)),
            ])
            ->filter(fn ($i) => $i['product_id'])
            ->values()
            ->all();

        if (empty($items)) {
            return response()->json(['ok' => false]);
        }

        $total = collect($items)->sum(fn ($i) => $i['price'] * $i['quantity']);
        $name  = Str::limit(trim((string) $request->input('customer_name')), 250, '');

        $row = AbandonedCart::where('phone', $phone)
            ->whereNull('recovered_at')
            ->where('created_at', '>=', now()->subDays(3))
            ->latest('id')
            ->first();

        if ($row) {
            $row->update([
                'items'         => $items,
                'total'         => $total,
                'customer_name' => $name !== '' ? $name : $row->customer_name,
                'last_seen_at'  => now(),
            ]);
        } else {
            AbandonedCart::create([
                'phone'         => $phone,
                'customer_name' => $name !== '' ? $name : null,
                'items'         => $items,
                'total'         => $total,
                'token'         => Str::random(40),
                'last_seen_at'  => now(),
            ]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Link from the reminder SMS: puts the saved items back in the cart
     * (using today's price and stock) and opens checkout.
     */
    public function restore(string $token)
    {
        $saved = AbandonedCart::where('token', $token)->whereNull('recovered_at')->first();

        if (! $saved) {
            return redirect()->route('cart.index')
                ->with('error', 'This cart link has expired. Please add your items again.');
        }

        $cart = session()->get('cart', []);

        foreach ((array) $saved->items as $item) {
            $product = Product::find($item['product_id'] ?? 0);

            if (! $product || (int) $product->stock <= 0 || isset($cart[$product->id])) {
                continue;
            }

            $cart[$product->id] = [
                'id'          => $product->id,
                'product_id'  => $product->id,
                'name'        => $product->name,
                'price'       => (float) $product->price,
                'quantity'    => min(max(1, (int) ($item['quantity'] ?? 1)), (int) $product->stock),
                'image'       => $product->image,
                'stock'       => (int) $product->stock,
                'category_id' => $product->category_id,
            ];
        }

        session()->put('cart', $cart);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Sorry, those items are no longer available.');
        }

        return redirect()->route('checkout.index')
            ->with('success', 'Welcome back! Your items are in the cart.');
    }
}
