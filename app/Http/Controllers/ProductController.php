<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->withCount('approvedReviews')
            ->withAvg('approvedReviews', 'rating');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Category Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category_id')) {
            $categoryId = (int) $request->input('category_id');

            if ($categoryId > 0) {
                $query->where('category_id', $categoryId);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Budget Filters
        |--------------------------------------------------------------------------
        */

        if ($request->filled('min_price') && is_numeric($request->input('min_price'))) {
            $query->where('price', '>=', max(0, (float) $request->input('min_price')));
        }

        if ($request->filled('max_price') && is_numeric($request->input('max_price'))) {
            $query->where('price', '<=', max(0, (float) $request->input('max_price')));
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        match ($request->input('sort')) {
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        $products = $query
            ->paginate(12)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        $categories = Category::query()
            ->withCount(['products' => fn ($query) => $query->where('stock', '>', 0)])
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Wishlist Optimization
        |--------------------------------------------------------------------------
        |
        | Instead of querying Wishlist for every product inside Blade,
        | load all wishlist product IDs for the logged-in user once.
        |
        */

        $wishlistProductIds = collect();

        if (auth()->check()) {
            $wishlistProductIds = Wishlist::where(
                'user_id',
                auth()->id()
            )
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id);
        }

        /*
        |--------------------------------------------------------------------------
        | Admin Check
        |--------------------------------------------------------------------------
        |
        | The route itself is protected by AdminMiddleware.
        | This variable controls admin-only buttons in the shared
        | product listing view.
        |
        */

        $isAdmin = auth()->check() && auth()->user()->isAdmin();

        return view(
            'products.index',
            compact(
                'products',
                'categories',
                'wishlistProductIds',
                'isAdmin'
            )
        );
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view(
            'products.create',
            compact('categories')
        );
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'compare_price' => [
                'nullable',
                'numeric',
                'gt:price',
                'max:99999999.99',
            ],

            'stock' => [
                'required',
                'integer',
                'min:0',
                'max:999999',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $imageName = null;

        /*
        |--------------------------------------------------------------------------
        | Upload Image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {
            $imageName = $this->uploadImage(
                $request->file('image')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create Product
        |--------------------------------------------------------------------------
        */

        $product = Product::create([
            'category_id' => $validated['category_id'],
            'name' => trim($validated['name']),
            'description' => isset($validated['description'])
                ? trim($validated['description'])
                : null,
            'price' => $validated['price'],
            'compare_price' => $validated['compare_price'] ?? null,
            'stock' => $validated['stock'],
            'image' => $imageName,
        ]);

        $this->saveGallery($product, $request);

        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                'Product created successfully.'
            );
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'images', 'approvedReviews.user']);

        // Related products: same category first, then top up with newest.
        $relatedProducts = Product::query()
            ->where('id', '!=', $product->id)
            ->where('stock', '>', 0)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->inRandomOrder()
            ->limit(4)
            ->get();

        if ($relatedProducts->count() < 4) {
            $fill = Product::query()
                ->where('id', '!=', $product->id)
                ->where('stock', '>', 0)
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->latest()
                ->limit(4 - $relatedProducts->count())
                ->get();

            $relatedProducts = $relatedProducts->concat($fill);
        }

        // Recently viewed (kept in the visitor's session, newest first).
        $viewed = collect(session('recently_viewed', []))
            ->map(fn ($id) => (int) $id)
            ->reject(fn ($id) => $id === (int) $product->id)
            ->prepend((int) $product->id)
            ->take(9)
            ->values()
            ->all();

        session(['recently_viewed' => $viewed]);

        $recentIds = array_slice(
            array_values(array_filter($viewed, fn ($id) => $id !== (int) $product->id)),
            0,
            4
        );

        $recentlyViewed = $recentIds
            ? Product::query()
                ->whereIn('id', $recentIds)
                ->get()
                ->sortBy(fn ($p) => array_search($p->id, $recentIds))
                ->values()
            : collect();

        return view(
            'products.show',
            compact('product', 'relatedProducts', 'recentlyViewed')
        );
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view(
            'products.edit',
            compact(
                'product',
                'categories'
            )
        );
    }

    /**
     * Update the specified product.
     */
    public function update(
        Request $request,
        Product $product
    ) {
        $validated = $request->validate([
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'compare_price' => [
                'nullable',
                'numeric',
                'gt:price',
                'max:99999999.99',
            ],

            'stock' => [
                'required',
                'integer',
                'min:0',
                'max:999999',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $imageName = $product->image;

        /*
        |--------------------------------------------------------------------------
        | Replace Product Image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {
            $this->deleteImage($product->image);

            $imageName = $this->uploadImage(
                $request->file('image')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Update Product
        |--------------------------------------------------------------------------
        */

        $product->update([
            'category_id' => $validated['category_id'],
            'name' => trim($validated['name']),
            'description' => isset($validated['description'])
                ? trim($validated['description'])
                : null,
            'price' => $validated['price'],
            'compare_price' => $validated['compare_price'] ?? null,
            'stock' => $validated['stock'],
            'image' => $imageName,
        ]);

        $this->saveGallery($product, $request);

        $this->removeGalleryImages($product, (array) $request->input('remove_gallery', []));

        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                'Product updated successfully.'
            );
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        /*
        |--------------------------------------------------------------------------
        | Protect Order History
        |--------------------------------------------------------------------------
        */

        if ($product->orderItems()->exists()) {
            return redirect()
                ->route('admin.products.index')
                ->with(
                    'error',
                    'This product cannot be deleted because it is already used in order history.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Delete Image
        |--------------------------------------------------------------------------
        */

        foreach ($product->images as $galleryImage) {
            $this->deleteImage($galleryImage->image);
        }

        $this->deleteImage($product->image);

        /*
        |--------------------------------------------------------------------------
        | Delete Wishlist References
        |--------------------------------------------------------------------------
        */

        Wishlist::where(
            'product_id',
            $product->id
        )->delete();

        /*
        |--------------------------------------------------------------------------
        | Delete Product
        |--------------------------------------------------------------------------
        */

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                'Product deleted successfully.'
            );
    }

    /**
     * Save extra gallery images (gallery[] input).
     */
    private function saveGallery(Product $product, Request $request): void
    {
        if (!$request->hasFile('gallery')) {
            return;
        }

        $request->validate([
            'gallery' => ['array', 'max:8'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $order = (int) $product->images()->max('sort_order');

        foreach ($request->file('gallery') as $file) {
            $product->images()->create([
                'image' => $this->uploadImage($file),
                'sort_order' => ++$order,
            ]);
        }
    }

    /**
     * Remove selected gallery images.
     */
    private function removeGalleryImages(Product $product, array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        $product->images()->whereIn('id', $ids)->get()->each(function ($img) {
            $this->deleteImage($img->image);
            $img->delete();
        });
    }

    /**
     * Upload product image.
     */
    private function uploadImage($image): string
    {
        $uploadPath = public_path(
            'uploads/products'
        );

        /*
        |--------------------------------------------------------------------------
        | Create Directory
        |--------------------------------------------------------------------------
        */

        if (!File::exists($uploadPath)) {
            File::makeDirectory(
                $uploadPath,
                0755,
                true
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Generate Safe Unique Filename
        |--------------------------------------------------------------------------
        */

        $extension = strtolower(
            $image->getClientOriginalExtension()
        );

        $baseName = Str::slug(
            pathinfo(
                $image->getClientOriginalName(),
                PATHINFO_FILENAME
            )
        );

        if ($baseName === '') {
            $baseName = 'product';
        }

        $fileName = $baseName
            . '-'
            . time()
            . '-'
            . Str::random(8)
            . '.'
            . $extension;

        /*
        |--------------------------------------------------------------------------
        | Move Image
        |--------------------------------------------------------------------------
        */

        return app(\App\Services\ImageOptimizer::class)
            ->save($image, $uploadPath, $fileName);
    }

    /**
     * Delete product image safely.
     */
    private function deleteImage(?string $imageName): void
    {
        if (empty($imageName)) {
            return;
        }

        $imagePath = public_path(
            'uploads/products/' . $imageName
        );

        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }
    }
}