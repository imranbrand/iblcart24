<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display shopping cart.
     */
    public function index(): View
    {
        $cart = session()->get('cart', []);

        $cartItems = collect($cart);

        $subtotal = $cartItems->sum(function ($item) {
            return ((float) ($item['price'] ?? 0))
                * ((int) ($item['quantity'] ?? 0));
        });

        $totalQuantity = $cartItems->sum(function ($item) {
            return (int) ($item['quantity'] ?? 0);
        });

        /*
        |--------------------------------------------------------------------------
        | DELIVERY CHARGE
        |--------------------------------------------------------------------------
        |
        | Keep delivery charge 0 here if your checkout page calculates
        | delivery separately.
        |
        */

        $deliveryCharge = empty($cart) ? 0 : CheckoutController::deliveryFor('inside_dhaka', (float) $subtotal);

        $coupon = null;
        $discount = 0;

        if ($code = session('coupon_code')) {
            $coupon = \App\Models\Coupon::findValidByCode($code);

            if ($coupon) {
                $discount = $coupon->calculateDiscount((float) $subtotal);
            }

            if (!$coupon || $discount <= 0) {
                session()->forget('coupon_code');
                $coupon = null;
                $discount = 0;
            }
        }

        $total = $subtotal + $deliveryCharge - $discount;

        return view('cart.index', [
            'coupon' => $coupon,
            'discount' => $discount,
            'cart' => $cart,
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'deliveryCharge' => $deliveryCharge,
            'total' => $total,
            'totalQuantity' => $totalQuantity,
        ]);
    }

    /**
     * Add product to cart.
     *
     * POST /cart/{product}
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATE QUANTITY
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'quantity' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ]);

        $quantity = (int) ($validated['quantity'] ?? 1);

        /*
        |--------------------------------------------------------------------------
        | CHECK PRODUCT STOCK
        |--------------------------------------------------------------------------
        */

        if ($product->stock <= 0) {
            return back()->with(
                'error',
                'Sorry, this product is currently out of stock.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | GET CURRENT CART
        |--------------------------------------------------------------------------
        */

        $cart = session()->get('cart', []);

        /*
        |--------------------------------------------------------------------------
        | PRODUCT KEY
        |--------------------------------------------------------------------------
        */

        $productId = (string) $product->id;

        /*
        |--------------------------------------------------------------------------
        | CHECK EXISTING CART ITEM
        |--------------------------------------------------------------------------
        */

        if (isset($cart[$productId])) {

            $newQuantity =
                ((int) $cart[$productId]['quantity']) + $quantity;

            /*
            | Do not allow cart quantity to exceed stock.
            */

            if ($newQuantity > $product->stock) {
                $newQuantity = $product->stock;
            }

            $cart[$productId]['quantity'] = $newQuantity;

            /*
            | Refresh product information in case price/name/image
            | changed after the product was added.
            */

            $cart[$productId]['name'] = $product->name;
            $cart[$productId]['price'] = (float) $product->price;
            $cart[$productId]['image'] = $product->image;
            $cart[$productId]['stock'] = (int) $product->stock;
            $cart[$productId]['category_id'] = $product->category_id;

        } else {

            /*
            |--------------------------------------------------------------------------
            | ADD NEW PRODUCT
            |--------------------------------------------------------------------------
            */

            $cart[$productId] = [
                'id' => $product->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'quantity' => min(
                    $quantity,
                    (int) $product->stock
                ),
                'image' => $product->image,
                'stock' => (int) $product->stock,
                'category_id' => $product->category_id,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE CART
        |--------------------------------------------------------------------------
        */

        session()->put('cart', $cart);

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            $product->name . ' added to cart successfully.'
        );
    }

    /**
     * Update cart item quantity.
     *
     * PUT/PATCH /cart/{product}
     */
    public function update(
        Request $request,
        Product $product
    ): RedirectResponse {

        $validated = $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $quantity = (int) $validated['quantity'];

        /*
        |--------------------------------------------------------------------------
        | CHECK STOCK
        |--------------------------------------------------------------------------
        */

        if ($product->stock <= 0) {
            return back()->with(
                'error',
                $product->name . ' is currently out of stock.'
            );
        }

        if ($quantity > $product->stock) {
            $quantity = (int) $product->stock;
        }

        /*
        |--------------------------------------------------------------------------
        | GET CART
        |--------------------------------------------------------------------------
        */

        $cart = session()->get('cart', []);

        $productId = (string) $product->id;

        /*
        |--------------------------------------------------------------------------
        | UPDATE ITEM
        |--------------------------------------------------------------------------
        */

        if (!isset($cart[$productId])) {
            return back()->with(
                'error',
                'This product is not available in your cart.'
            );
        }

        $cart[$productId]['quantity'] = $quantity;

        /*
        | Refresh current product data.
        */

        $cart[$productId]['name'] = $product->name;
        $cart[$productId]['price'] = (float) $product->price;
        $cart[$productId]['image'] = $product->image;
        $cart[$productId]['stock'] = (int) $product->stock;
        $cart[$productId]['category_id'] = $product->category_id;

        /*
        |--------------------------------------------------------------------------
        | SAVE
        |--------------------------------------------------------------------------
        */

        session()->put('cart', $cart);

        return back()->with(
            'success',
            'Cart updated successfully.'
        );
    }

    /**
     * Remove product from cart.
     *
     * DELETE /cart/{product}
     */
    public function remove(Product $product): RedirectResponse
    {
        $cart = session()->get('cart', []);

        $productId = (string) $product->id;

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
        }

        session()->put('cart', $cart);

        return back()->with(
            'success',
            $product->name . ' removed from cart.'
        );
    }

    /**
     * Remove a product from cart using product ID.
     *
     * This method is kept as an additional compatibility method
     * in case an older route/view uses destroy().
     */
    public function destroy(Product $product): RedirectResponse
    {
        return $this->remove($product);
    }

    /**
     * Clear complete cart.
     *
     * DELETE /cart
     */
    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return back()->with(
            'success',
            'Your cart has been cleared successfully.'
        );
    }

    /**
     * Buy now.
     *
     * Adds the product to cart and redirects to checkout.
     */
    public function buyNow(
        Request $request,
        Product $product
    ): RedirectResponse {

        $validated = $request->validate([
            'quantity' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ]);

        $quantity = (int) ($validated['quantity'] ?? 1);

        /*
        |--------------------------------------------------------------------------
        | STOCK CHECK
        |--------------------------------------------------------------------------
        */

        if ($product->stock <= 0) {
            return back()->with(
                'error',
                'Sorry, this product is currently out of stock.'
            );
        }

        if ($quantity > $product->stock) {
            $quantity = (int) $product->stock;
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE CART ITEM
        |--------------------------------------------------------------------------
        */

        $cart = session()->get('cart', []);

        $productId = (string) $product->id;

        $cart[$productId] = [
            'id' => $product->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'quantity' => $quantity,
            'image' => $product->image,
            'stock' => (int) $product->stock,
            'category_id' => $product->category_id,
        ];

        /*
        |--------------------------------------------------------------------------
        | SAVE CART
        |--------------------------------------------------------------------------
        */

        session()->put('cart', $cart);

        /*
        |--------------------------------------------------------------------------
        | GO TO CHECKOUT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('checkout.index')
            ->with(
                'success',
                'Product added to cart. Please complete your order.'
            );
    }

    /**
     * Return cart item count.
     *
     * Useful for AJAX/header cart badge.
     */
    public function count(): int
    {
        $cart = session()->get('cart', []);

        return collect($cart)->sum(function ($item) {
            return (int) ($item['quantity'] ?? 0);
        });
    }

    /**
     * Return cart subtotal.
     *
     * Useful for internal calculations.
     */
    public function subtotal(): float
    {
        $cart = session()->get('cart', []);

        return (float) collect($cart)->sum(function ($item) {
            return ((float) ($item['price'] ?? 0))
                * ((int) ($item['quantity'] ?? 0));
        });
    }
}