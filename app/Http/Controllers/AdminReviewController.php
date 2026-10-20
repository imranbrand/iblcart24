<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = Review::query()->with(['product', 'user'])->latest();

        if ($status === 'visible') {
            $query->where('is_approved', true);
        } elseif ($status === 'hidden') {
            $query->where('is_approved', false);
        } else {
            $status = 'all';
        }

        $reviews = $query->paginate(20)->withQueryString();

        $counts = [
            'all'     => Review::count(),
            'visible' => Review::where('is_approved', true)->count(),
            'hidden'  => Review::where('is_approved', false)->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'status', 'counts'));
    }

    public function toggle(Review $review)
    {
        $review->update(['is_approved' => !$review->is_approved]);

        return back()->with(
            'success',
            $review->is_approved ? 'Review is now visible.' : 'Review hidden from the shop.'
        );
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
