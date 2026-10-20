<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Premium storefront homepage.
     */
    public function index()
    {
        $categories = Category::query()
            ->withCount(['products' => fn ($query) => $query->where('stock', '>', 0)])
            ->orderBy('name')
            ->get();

        $featuredProducts = Product::query()
            ->with('category')
            ->where('stock', '>', 0)
            ->latest()
            ->take(8)
            ->get();

        $dealProducts = Product::query()
            ->with('category')
            ->where('stock', '>', 0)
            ->orderBy('price')
            ->take(4)
            ->get();

        return view('home', compact(
            'categories',
            'featuredProducts',
            'dealProducts'
        ));
    }
}
