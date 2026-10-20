@extends('layouts.storefront')

@section('title', request()->routeIs('admin.*') ? 'Manage Products' : 'Shop')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/products-index.css') }}?v=b3">
@endpush

@section('content')

@if(! request()->routeIs('admin.*') && ! request()->filled('search') && ! request()->filled('category_id') && $products->currentPage() === 1)
<section class="ibl-hero mb-4">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <span class="ibl-hero-tag"><i class="fa-solid fa-bolt me-1"></i> Shop smart. Shop premium.</span>
            <h1 class="ibl-hero-title">Quality products,<br>delivered to your door.</h1>
            <p class="ibl-hero-text">Browse our collection, pay with Cash on Delivery, bKash, Nagad or card, and track your order any time.</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="#product-list" class="btn btn-light btn-lg rounded-pill px-4 fw-semibold">Shop now <i class="fa-solid fa-arrow-down ms-1"></i></a>
                <a href="{{ route('track.order') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">Track order</a>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row g-3">
                <div class="col-6"><div class="ibl-perk"><i class="fa-solid fa-hand-holding-dollar"></i><b>Cash on Delivery</b><span>Pay when you receive</span></div></div>
                <div class="col-6"><div class="ibl-perk"><i class="fa-solid fa-mobile-screen-button"></i><b>bKash &amp; Nagad</b><span>Easy mobile payment</span></div></div>
                <div class="col-6"><div class="ibl-perk"><i class="fa-solid fa-truck-fast"></i><b>Order tracking</b><span>Know where it is</span></div></div>
                <div class="col-6"><div class="ibl-perk"><i class="fa-solid fa-tags"></i><b>Coupons</b><span>Save on every order</span></div></div>
            </div>
        </div>
    </div>
</section>
@if(isset($categories) && $categories->count())
<div class="ibl-chips mb-4">
    @foreach($categories as $chip)
        <a href="{{ route('products.index', ['category_id' => $chip->id]) }}" class="ibl-chip">{{ $chip->name }}</a>
    @endforeach
</div>
@endif
@endif
<span id="product-list"></span>
<div class="container py-4">

    <!-- =====================================================
         PAGE HEADER
    ====================================================== -->

    <div class="page-header mb-4">

        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3"
        >

            <div>

                <h2 class="fw-bold mb-1">

                    <i class="fa-solid fa-box-open me-2"></i>

                    Products

                </h2>

                <p class="mb-0 text-white-50">

                    Explore all products from IBL CART 24

                </p>

            </div>

            <div class="d-flex gap-2 flex-wrap">

                @auth

                    <a
                        href="{{ route('wishlist.index') }}"
                        class="btn btn-outline-light"
                    >

                        <i class="fa-solid fa-heart me-1"></i>

                        Wishlist

                    </a>

                @endauth


                @if($isAdmin ?? false)

                    <a
                        href="{{ route('admin.products.create') }}"
                        class="btn btn-light"
                    >

                        <i class="fa-solid fa-plus me-1"></i>

                        Add Product

                    </a>

                @endif

            </div>

        </div>

    </div>


    <!-- =====================================================
         SEARCH + CATEGORY FILTER
    ====================================================== -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('products.index') }}"
            >

                <div class="row g-2">

                    <div class="col-md-6">

                        <label class="form-label fw-semibold">

                            Search Product

                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control search-box"
                            placeholder="Search by product name..."
                            value="{{ request('search') }}"
                        >

                    </div>


                    <div class="col-md-3">

                        <label class="form-label fw-semibold">

                            Category

                        </label>

                        <select
                            name="category_id"
                            class="form-select filter-box"
                        >

                            <option value="">

                                All Categories

                            </option>

                            @foreach($categories as $category)

                                <option
                                    value="{{ $category->id }}"
                                    @selected(
                                        (string) request('category_id')
                                        ===
                                        (string) $category->id
                                    )
                                >

                                    {{ $category->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-3 d-flex align-items-end gap-2">

                        <button
                            type="submit"
                            class="btn btn-dark flex-fill"
                        >

                            <i class="fa-solid fa-filter me-1"></i>

                            Filter

                        </button>


                        @if(request('search') || request('category_id'))

                            <a
                                href="{{ route('products.index') }}"
                                class="btn btn-outline-secondary"
                                title="Clear Filters"
                            >

                                <i class="fa-solid fa-xmark"></i>

                            </a>

                        @endif

                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- =====================================================
         FLASH MESSAGES
    ====================================================== -->


    @if(session('info'))

        <div
            class="alert alert-info alert-dismissible fade show"
            role="alert"
        >

            <i class="fa-solid fa-circle-info me-1"></i>

            {{ session('info') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    @if($errors->any())

        <div
            class="alert alert-danger alert-dismissible fade show"
            role="alert"
        >

            <strong>

                <i class="fa-solid fa-triangle-exclamation me-1"></i>

                Please fix the following errors:

            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>

                        {{ $error }}

                    </li>

                @endforeach

            </ul>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    <!-- =====================================================
         PRODUCT COUNT
    ====================================================== -->

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h5 class="fw-bold mb-0">

                All Products

            </h5>

            <small class="text-muted">

                {{ $products->total() }}

                {{ $products->total() == 1 ? 'product' : 'products' }}

                found

            </small>

        </div>

    </div>


    <!-- =====================================================
         PRODUCTS
    ====================================================== -->

    <div class="row g-4">

        @forelse($products as $product)

            <div class="col-sm-6 col-lg-4 col-xl-3">

                <div class="card product-card shadow-sm">


                    <!-- =================================================
                         WISHLIST
                    ================================================== -->

                    @auth

                        @php

                            $inWishlist = $wishlistProductIds->contains(
                                (int) $product->id
                            );

                        @endphp

                        <div class="wishlist-label">

                            <span class="wishlist-tooltip">

                                {{ $inWishlist
                                    ? 'Already in Wishlist'
                                    : 'Add to Wishlist'
                                }}

                            </span>

                            @if($inWishlist)

                                <form
                                    action="{{ route('wishlist.destroy', $product) }}"
                                    method="POST"
                                    class="m-0"
                                >

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="wishlist-btn active"
                                        title="Remove from Wishlist"
                                    >

                                        <i class="fa-solid fa-heart"></i>

                                    </button>

                                </form>

                            @else

                                <form
                                    action="{{ route('wishlist.store', $product) }}"
                                    method="POST"
                                    class="m-0"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="wishlist-btn"
                                        title="Add to Wishlist"
                                    >

                                        <i class="fa-regular fa-heart"></i>

                                    </button>

                                </form>

                            @endif

                        </div>

                    @else

                        <div class="wishlist-label">

                            <span class="wishlist-tooltip">

                                Login to use Wishlist

                            </span>

                            <a
                                href="{{ route('login') }}"
                                class="wishlist-btn text-decoration-none"
                                title="Login to use Wishlist"
                            >

                                <i class="fa-regular fa-heart"></i>

                            </a>

                        </div>

                    @endauth


                    <!-- =================================================
                         PRODUCT IMAGE
                    ================================================== -->

                    @if($product->image)

                        <img
                            src="{{ asset('uploads/products/' . $product->image) }}"
                            class="product-image"
                            alt="{{ $product->name }}"
                            loading="lazy"
                        >

                    @else

                        <div
                            class="product-image d-flex align-items-center justify-content-center"
                        >

                            <div class="text-center text-muted">

                                <i class="fa-solid fa-image fa-3x mb-2"></i>

                                <div>

                                    No Image

                                </div>

                            </div>

                        </div>

                    @endif


                    <!-- =================================================
                         PRODUCT BODY
                    ================================================== -->

                    <div class="card-body d-flex flex-column">

                        @if($product->created_at && $product->created_at->gt(now()->subDays(7)))
                            <div class="mb-2">
                                <span class="badge bg-success">New</span>
                            </div>
                        @endif

                        @if($product->category)

                            <div class="mb-2">

                                <span class="badge bg-secondary category-badge">

                                    <i class="fa-solid fa-tag me-1"></i>

                                    {{ $product->category->name }}

                                </span>

                            </div>

                        @endif


                        <h5 class="card-title fw-bold product-title">

                            {{ $product->name }}

                        </h5>


                        @if($product->description)

                            <p class="card-text text-muted small product-description">

                                {{ Str::limit($product->description, 80) }}

                            </p>

                        @else

                            <p class="card-text text-muted small product-description">

                                No description available.

                            </p>

                        @endif


                        <!-- PRICE -->

                        <div class="mb-3">

                            <span class="price text-dark">

                                ৳{{ number_format($product->price, 0) }}
                                @if($product->is_on_sale)
                                <small class="text-muted text-decoration-line-through ms-1" style="font-weight:400;">৳{{ number_format($product->compare_price, 0) }}</small>
                                <span class="badge bg-danger ms-1">-{{ $product->discount_percent }}%</span>
                            @endif

                            </span>

                        </div>


                        <!-- STOCK -->

                        <div class="mb-3">

                            @if($product->stock > 5)

                                <span class="badge bg-success">

                                    <i class="fa-solid fa-check me-1"></i>

                                    {{ $product->stock }} in stock

                                </span>

                            @elseif($product->stock > 0)

                                <span class="badge stock-low">

                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>

                                    {{ $product->stock }} in stock

                                </span>

                            @else

                                <span class="badge bg-danger">

                                    <i class="fa-solid fa-xmark me-1"></i>

                                    Out of stock

                                </span>

                            @endif

                        </div>


                        <!-- =================================================
                             BUTTONS
                        ================================================== -->

                        <div class="mt-auto">

                            <div class="d-grid gap-2">

                                <a
                                    href="{{ route('products.show', $product) }}"
                                    class="btn btn-outline-dark"
                                >

                                    <i class="fa-solid fa-eye me-1"></i>

                                    View Details

                                </a>


                                @if($product->stock > 0)

                                    @auth

                                        <a
                                            href="{{ route('buy.now', $product) }}"
                                            class="btn btn-success"
                                        >

                                            <i class="fa-solid fa-bag-shopping me-1"></i>

                                            Order Now

                                        </a>

                                    @else

                                        <a
                                            href="{{ route('login') }}"
                                            class="btn btn-success"
                                        >

                                            <i class="fa-solid fa-right-to-bracket me-1"></i>

                                            Login to Order

                                        </a>

                                    @endauth

                                @else

                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        disabled
                                    >

                                        <i class="fa-solid fa-ban me-1"></i>

                                        Out of Stock

                                    </button>

                                @endif


                                <!-- =================================================
                                     ADMIN ACTIONS
                                ================================================== -->

                                @if($isAdmin ?? false)

                                    <div class="d-flex gap-2 admin-actions">

                                        <a
                                            href="{{ route('admin.products.edit', $product) }}"
                                            class="btn btn-warning flex-fill"
                                        >

                                            <i class="fa-solid fa-pen-to-square me-1"></i>

                                            Edit

                                        </a>


                                        <form
                                            action="{{ route('admin.products.destroy', $product) }}"
                                            method="POST"
                                            class="flex-fill"
                                            onsubmit="return confirmDeleteProduct();"
                                        >

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-danger w-100"
                                            >

                                                <i class="fa-solid fa-trash me-1"></i>

                                                Delete

                                            </button>

                                        </form>

                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        @empty

            <!-- =================================================
                 EMPTY PRODUCTS
            ================================================== -->

            <div class="col-12">

                <div class="card border-0 shadow-sm">

                    <div class="card-body text-center py-5">

                        <i
                            class="fa-solid fa-box-open fa-4x text-muted mb-3 empty-product-icon"
                        ></i>

                        <h4 class="fw-bold">

                            No Products Found

                        </h4>


                        @if(request('search') || request('category_id'))

                            <p class="text-muted">

                                No products matched your current filters.

                            </p>

                            <a
                                href="{{ route('products.index') }}"
                                class="btn btn-dark"
                            >

                                <i class="fa-solid fa-arrow-left me-1"></i>

                                Show All Products

                            </a>

                        @else

                            <p class="text-muted">

                                You haven't added any products yet.

                            </p>


                            @if($isAdmin ?? false)

                                <a
                                    href="{{ route('admin.products.create') }}"
                                    class="btn btn-dark"
                                >

                                    <i class="fa-solid fa-plus me-1"></i>

                                    Add Your First Product

                                </a>

                            @endif

                        @endif

                    </div>

                </div>

            </div>

        @endforelse

    </div>


    <!-- =====================================================
         PAGINATION
    ====================================================== -->

    @if($products->hasPages())

        <div class="mt-5">

            {{ $products->withQueryString()->links() }}

        </div>

    @endif

</div>
@endsection

@push('scripts')
<script>

    function confirmDeleteProduct() {

        return confirm(
            'Are you sure you want to delete this product?\n\n' +
            'Products already used in order history cannot be deleted.'
        );

    }

</script>
@endpush
