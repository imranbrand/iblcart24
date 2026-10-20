<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display category listing.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));

        $categories = Category::query()
            ->withCount('products')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%');
                });
            })
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        $view = request()->routeIs('admin.categories.*')
            ? 'admin.categories.index'
            : 'categories.index';

        return view($view, compact(
            'categories',
            'search'
        ));
    }

    /**
     * Show category creation form.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a new category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:categories,name',
                ],
            ],
            [
                'name.required' => 'Category name is required.',
                'name.unique' => 'This category already exists.',
            ]
        );

        $name = trim($validated['name']);

        $slug = $this->generateUniqueSlug($name);

        Category::create([
            'name' => $name,
            'slug' => $slug,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'Category "' . $name . '" created successfully.'
            );
    }

    /**
     * Display a single category.
     */
    public function show(Category $category): View
    {
        $category->loadCount('products');

        $products = $category->products()
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('categories.show', compact(
            'category',
            'products'
        ));
    }

    /**
     * Show category edit form.
     */
    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update category.
     */
    public function update(
        Request $request,
        Category $category
    ): RedirectResponse {

        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:categories,name,' . $category->id,
                ],
            ],
            [
                'name.required' => 'Category name is required.',
                'name.unique' => 'This category already exists.',
            ]
        );

        $name = trim($validated['name']);

        /*
        |--------------------------------------------------------------------------
        | Generate slug
        |--------------------------------------------------------------------------
        |
        | Keep the existing slug if the category name did not change.
        |
        */

        if (strcasecmp($category->name, $name) === 0) {
            $slug = $category->slug;
        } else {
            $slug = $this->generateUniqueSlug(
                $name,
                $category->id
            );
        }

        $category->update([
            'name' => $name,
            'slug' => $slug,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'Category "' . $name . '" updated successfully.'
            );
    }

    /**
     * Delete category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Prevent deleting category containing products
        |--------------------------------------------------------------------------
        |
        | This protects existing product records from losing their
        | category relationship.
        |
        */

        $productCount = $category->products()->count();

        if ($productCount > 0) {
            return redirect()
                ->route('admin.categories.index')
                ->with(
                    'error',
                    'Cannot delete "' . $category->name .
                    '" because it contains ' .
                    $productCount .
                    ' product(s). Please move or delete those products first.'
                );
        }

        $categoryName = $category->name;

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'Category "' . $categoryName . '" deleted successfully.'
            );
    }

    /**
     * Generate a unique category slug.
     */
    private function generateUniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {

        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'category';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            Category::query()
                ->where('slug', $slug)
                ->when(
                    $ignoreId !== null,
                    function ($query) use ($ignoreId) {
                        $query->where('id', '!=', $ignoreId);
                    }
                )
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}