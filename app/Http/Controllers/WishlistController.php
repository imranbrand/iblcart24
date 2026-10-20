<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Display the customer's wishlist.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Wishlist Pagination
        |--------------------------------------------------------------------------
        | Use paginate() instead of get() so the Blade view can safely use
        | $wishlists->links().
        */

        $wishlists = Wishlist::query()
            ->where('user_id', $userId)
            ->with([
                'product.category',
            ])
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Wishlist Summary
        |--------------------------------------------------------------------------
        */

        $wishlistCount = Wishlist::query()
            ->where('user_id', $userId)
            ->count();

        return view('wishlist.index', compact(
            'wishlists',
            'wishlistCount'
        ));
    }


    /**
     * Add a product to wishlist.
     */
    public function store(
        Request $request,
        Product $product
    ): RedirectResponse {

        $userId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Wishlist Items
        |--------------------------------------------------------------------------
        */

        $exists = Wishlist::query()
            ->where('user_id', $userId)
            ->where('product_id', $product->id)
            ->exists();

        if ($exists) {
            return back()->with(
                'info',
                'This product is already in your wishlist.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create Wishlist Item
        |--------------------------------------------------------------------------
        */

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $product->id,
        ]);

        return back()->with(
            'success',
            'Product "' . $product->name . '" added to your wishlist.'
        );
    }


    /**
     * Alternative wishlist add route.
     */
    public function add(
        Request $request,
        Product $product
    ): RedirectResponse {

        return $this->store($request, $product);
    }


    /**
     * Remove a product from wishlist.
     */
    public function remove(
        Request $request,
        Product $product
    ): RedirectResponse {

        $wishlist = Wishlist::query()
            ->where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if (!$wishlist) {
            return back()->with(
                'error',
                'This product is not in your wishlist.'
            );
        }

        $wishlist->delete();

        return back()->with(
            'success',
            'Product "' . $product->name . '" removed from your wishlist.'
        );
    }


    /**
     * Destroy a wishlist item.
     */
    public function destroy(
        Request $request,
        Product $product
    ): RedirectResponse {

        return $this->remove($request, $product);
    }


    /**
     * Move wishlist product to cart.
     */
    public function cart(
        Request $request,
        Product $product
    ): RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | Check Stock
        |--------------------------------------------------------------------------
        */

        if ((int) $product->stock <= 0) {
            return back()->with(
                'error',
                'Sorry, "' . $product->name . '" is currently out of stock.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Add to Session Cart
        |--------------------------------------------------------------------------
        */

        $cart = session()->get('cart', []);

        $productId = (int) $product->id;

        if (isset($cart[$productId])) {

            $currentQuantity = (int) $cart[$productId]['quantity'];

            if ($currentQuantity >= (int) $product->stock) {
                return back()->with(
                    'error',
                    'You cannot add more than the available stock.'
                );
            }

            $cart[$productId]['quantity'] = $currentQuantity + 1;

        } else {

            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'quantity' => 1,
                'image' => $product->image,
                'stock' => (int) $product->stock,
            ];
        }

        session()->put('cart', $cart);

        /*
        |--------------------------------------------------------------------------
        | Remove From Wishlist
        |--------------------------------------------------------------------------
        */

        Wishlist::query()
            ->where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->delete();

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                '"' . $product->name . '" moved to your cart.'
            );
    }
}