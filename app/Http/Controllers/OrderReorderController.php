<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderReorderController extends Controller
{
    /**
     * Put the items of a previous order back into the cart.
     * Uses today's price and stock, never the old order's price.
     */
    public function __invoke(Request $request, Order $order)
    {
        abort_unless(
            $order->user_id !== null && (int) $order->user_id === (int) $request->user()->id,
            403
        );

        $cart = session()->get('cart', []);
        $added = 0;
        $skipped = [];

        foreach ($order->items as $item) {
            $product = $item->product_id ? Product::find($item->product_id) : null;

            if (!$product || $product->stock <= 0) {
                $skipped[] = $item->product_name;
                continue;
            }

            $already = (int) ($cart[$product->id]['quantity'] ?? 0);
            $quantity = min((int) $item->quantity + $already, (int) $product->stock);

            $cart[$product->id] = [
                'id'          => $product->id,
                'product_id'  => $product->id,
                'name'        => $product->name,
                'price'       => (float) $product->price,
                'quantity'    => $quantity,
                'image'       => $product->image,
                'stock'       => (int) $product->stock,
                'category_id' => $product->category_id,
            ];

            $added++;
        }

        session()->put('cart', $cart);

        if ($added === 0) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Sorry, none of these products are available right now.');
        }

        $redirect = redirect()
            ->route('cart.index')
            ->with('success', $added . ' item(s) from your previous order were added to the cart.');

        if ($skipped) {
            $redirect->with('error', 'Not available anymore: ' . implode(', ', $skipped));
        }

        return $redirect;
    }
}
