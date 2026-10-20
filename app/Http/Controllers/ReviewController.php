<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    /**
     * Store a new review for a product.
     *
     * Only customers who have an order containing this product,
     * and who haven't already reviewed it, may submit one.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:150'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $userId = $request->user()->id;

        /*
        |--------------------------------------------------------------------------
        | ONE REVIEW PER CUSTOMER PER PRODUCT
        |--------------------------------------------------------------------------
        */

        $existing = Review::where('product_id', $product->id)
            ->where('user_id', $userId)
            ->first();

        $needsApproval = (bool) config('shop.reviews_require_approval', false);

        if ($existing) {
            // An admin-hidden review stays hidden after the customer edits it.
            $existing->update(
                $needsApproval ? $validated + ['is_approved' => false] : $validated
            );

            return back()->with(
                'success',
                $needsApproval
                    ? 'Your review has been updated and will appear after approval.'
                    : 'Your review has been updated.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFIED PURCHASE CHECK
        |--------------------------------------------------------------------------
        |
        | Only customers with a non-cancelled order containing this product
        | may create a review. This prevents fabricated product reviews.
        */

        $hasPurchased = \App\Models\OrderItem::query()
            ->where('product_id', $product->id)
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where(function ($q) {
                        $q->whereNull('status')
                            ->orWhereNotIn('status', ['cancelled']);
                    });
            })
            ->exists();

        if (!$hasPurchased) {
            return back()->with('error', 'You can only review products you have purchased.');
        }

        Review::create([
            'product_id' => $product->id,
            'user_id' => $userId,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'] ?? null,
            'is_approved' => !$needsApproval,
        ]);

        return back()->with(
            'success',
            $needsApproval
                ? 'Thank you! Your review will appear after approval.'
                : 'Thank you! Your review has been posted.'
        );
    }

    /**
     * Delete a review (owner or admin only).
     */
    public function destroy(Request $request, Review $review): RedirectResponse
    {
        $user = $request->user();

        if ($review->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}