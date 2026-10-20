@extends('layouts.storefront')

@section('title', $product->name)

@section('seo')
    @php
        $seoImage = $product->image ? asset('uploads/products/' . $product->image) : null;
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->meta_description,
            'image' => $seoImage,
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'BDT',
                'price' => (float) $product->price,
                'availability' => $product->stock > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'url' => url()->current(),
            ],
        ];
        if ($product->approvedReviews->count() > 0) {
            $jsonLd['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => round($product->approvedReviews->avg('rating'), 1),
                'reviewCount' => $product->approvedReviews->count(),
            ];
        }
    @endphp
    <meta name="description" content="{{ $product->meta_description }}">
    <meta property="og:type" content="product">
    <meta property="og:site_name" content="IBL CART 24">
    <meta property="og:title" content="{{ $product->name }}">
    <meta property="og:description" content="{{ $product->meta_description }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($seoImage)
        <meta property="og:image" content="{{ $seoImage }}">
    @endif
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@section('content')

<link rel="stylesheet" href="{{ asset('css/pages/products-show.css') }}?v=b3">


<div class="container-fluid py-4">

    {{-- ========================= --}}
    {{-- HEADER --}}
    {{-- ========================= --}}

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">

        <div>
            <h2 class="fw-bold mb-1">
                📦 Product Details
            </h2>

            <small class="text-muted">
                View product information and purchase this product
            </small>
        </div>

        <div class="d-flex gap-2">

            @auth

                <a href="{{ route('wishlist.index') }}"
                   class="btn btn-outline-danger">
                    ❤️ Wishlist
                </a>

            @endauth

            <a href="{{ route('products.index') }}"
               class="btn btn-secondary">
                ← Back to Products
            </a>

        </div>

    </div>


    {{-- ========================= --}}
    {{-- ALERT MESSAGES --}}
    {{-- ========================= --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show shadow-sm"
             role="alert">

            ✓ {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show shadow-sm"
             role="alert">

            ⚠ {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    @if(session('info'))

        <div class="alert alert-info alert-dismissible fade show shadow-sm"
             role="alert">

            ℹ {{ session('info') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- ========================= --}}
    {{-- PRODUCT CARD --}}
    {{-- ========================= --}}

    <div class="card shadow-sm border-0 product-details-card">

        <div class="card-body p-4">

            <div class="row g-4">


                {{-- ========================= --}}
                {{-- PRODUCT IMAGE --}}
                {{-- ========================= --}}

                <div class="col-lg-5">

                    @php
                        $galleryImages = collect();
                        if ($product->image) {
                            $galleryImages->push($product->image);
                        }
                        foreach ($product->images as $extra) {
                            $galleryImages->push($extra->image);
                        }
                    @endphp

                    <div class="product-image-box">
                        <span class="zoom-hint"><i class="fa-solid fa-magnifying-glass-plus me-1"></i> Hover to zoom</span>

                        @if($galleryImages->isNotEmpty())

                            <img
                                id="mainProductImage"
                                src="{{ asset('uploads/products/' . $galleryImages->first()) }}"
                                alt="{{ $product->name }}"
                            >

                        @else

                            <div class="text-center text-muted">

                                <div style="font-size:70px;">
                                    📷
                                </div>

                                <h5 class="mt-2">
                                    No Image Available
                                </h5>

                            </div>

                        @endif

                    </div>

                    @if($galleryImages->count() > 1)
                        <div class="d-flex gap-2 flex-wrap mt-3">
                            @foreach($galleryImages as $img)
                                <img
                                    src="{{ asset('uploads/products/' . $img) }}"
                                    alt="{{ $product->name }}"
                                    class="img-thumbnail product-thumb"
                                    style="cursor:pointer;"
                                    onclick="document.getElementById('mainProductImage').src=this.src"
                                >
                            @endforeach
                        </div>
                    @endif

                </div>


                {{-- ========================= --}}
                {{-- PRODUCT INFORMATION --}}
                {{-- ========================= --}}

                <div class="col-lg-7">


                    {{-- PRODUCT TITLE + WISHLIST --}}

                    <div class="d-flex justify-content-between align-items-start gap-3">

                        <div>

                            <h2 class="fw-bold mb-2">
                                {{ $product->name }}
                            </h2>

                            <span class="badge bg-secondary px-3 py-2">

                                Category:

                                {{ $product->category->name ?? 'Uncategorized' }}

                            </span>

                        </div>


                        {{-- ========================= --}}
                        {{-- WISHLIST BUTTON --}}
                        {{-- ========================= --}}

                        @auth

                            @php

                                $inWishlist = \App\Models\Wishlist::where(
                                    'user_id',
                                    Auth::id()
                                )
                                ->where(
                                    'product_id',
                                    $product->id
                                )
                                ->exists();

                            @endphp


                            @if($inWishlist)

                                {{-- REMOVE FROM WISHLIST --}}

                                <form
                                    action="{{ route('wishlist.destroy', $product) }}"
                                    method="POST"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="wishlist-btn active"
                                        title="Remove from Wishlist"
                                    >

                                        ❤️

                                    </button>

                                </form>

                            @else

                                {{-- ADD TO WISHLIST --}}

                                <form
                                    action="{{ route('wishlist.store', $product) }}"
                                    method="POST"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="wishlist-btn"
                                        title="Add to Wishlist"
                                    >

                                        ♡

                                    </button>

                                </form>

                            @endif

                        @else

                            {{-- LOGIN REQUIRED --}}

                            <a
                                href="{{ route('login') }}"
                                class="wishlist-btn"
                                title="Login to add to Wishlist"
                            >

                                ♡

                            </a>

                        @endauth

                    </div>


                    {{-- ========================= --}}
                    {{-- PRICE --}}
                    {{-- ========================= --}}

                    <div class="price-box mt-4 mb-4">

                        <div class="small text-muted mb-1">
                            Price
                        </div>

                        <h2 class="text-success fw-bold mb-0">

                            ৳ {{ number_format($product->price, 2) }}

                            @if($product->is_on_sale)
                                <small class="text-muted text-decoration-line-through fs-5 fw-normal ms-2">৳ {{ number_format($product->compare_price, 2) }}</small>
                                <span class="badge bg-danger align-middle fs-6 ms-1">Save {{ $product->discount_percent }}%</span>
                            @endif

                        </h2>

                    </div>


                    {{-- ========================= --}}
                    {{-- STOCK STATUS --}}
                    {{-- ========================= --}}

                    <div class="mb-4">

                        @if($product->stock > 5)

                            <span class="badge bg-success fs-6 px-3 py-2">

                                ✓ In Stock

                            </span>

                            <div class="mt-2">

                                <small class="text-success fw-semibold">

                                    Available:
                                    {{ $product->stock }} pieces

                                </small>

                            </div>


                        @elseif($product->stock > 0)

                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">

                                ⚠ Low Stock

                            </span>

                            <div class="mt-2">

                                <small class="text-danger fw-semibold">

                                    Only {{ $product->stock }} pieces left!

                                </small>

                            </div>


                        @else

                            <span class="badge bg-danger fs-6 px-3 py-2">

                                ❌ Out of Stock

                            </span>

                            <div class="mt-2">

                                <small class="text-danger fw-semibold">

                                    Currently unavailable

                                </small>

                            </div>

                        @endif

                    </div>


                    <hr>


                    {{-- ========================= --}}
                    {{-- DESCRIPTION --}}
                    {{-- ========================= --}}

                    <h5 class="fw-bold mb-2">

                        Product Description

                    </h5>

                    <p class="text-muted mb-0">

                        {{ $product->description ?: 'No description available.' }}

                    </p>


                    <hr class="my-4">


                    {{-- ========================= --}}
                    {{-- PURCHASE SECTION --}}
                    {{-- ========================= --}}

                    @if($product->stock > 0)

                        <div class="card border purchase-card">

                            <div class="card-body p-4">

                                <h5 class="fw-bold mb-4">

                                    🛒 Purchase Product

                                </h5>


                                {{-- ADD TO CART FORM --}}

                                <form
                                    action="{{ route('cart.add', $product->id) }}"
                                    method="POST"
                                >

                                    @csrf

                                    <div class="row align-items-end g-3">


                                        {{-- QUANTITY --}}

                                        <div class="col-md-4">

                                            <label
                                                for="quantity"
                                                class="form-label fw-bold"
                                            >

                                                Quantity

                                            </label>

                                            <input
                                                type="number"
                                                id="quantity"
                                                name="quantity"
                                                value="1"
                                                min="1"
                                                max="{{ $product->stock }}"
                                                step="1"
                                                class="form-control quantity-input"
                                                required
                                            >

                                            <small class="text-muted">

                                                Maximum:
                                                {{ $product->stock }}

                                            </small>

                                        </div>


                                        {{-- BUTTONS --}}

                                        <div class="col-md-8">

                                            <div class="d-flex flex-wrap gap-2">


                                                {{-- ADD TO CART --}}

                                                <button
                                                    type="submit"
                                                    class="btn btn-success btn-lg px-4 action-btn"
                                                >

                                                    🛒 Add to Cart

                                                </button>


                                                {{-- BUY NOW --}}

                                                @auth

                                                    <button
                                                        type="button"
                                                        class="btn btn-primary btn-lg px-4 action-btn"
                                                        onclick="buyNow()"
                                                    >

                                                        ⚡ Buy Now

                                                    </button>

                                                @else

                                                    <a
                                                        href="{{ route('login') }}"
                                                        class="btn btn-primary btn-lg px-4 action-btn"
                                                    >

                                                        ⚡ Login to Buy

                                                    </a>

                                                @endauth

                                            </div>

                                        </div>

                                    </div>

                                </form>


                                {{-- ========================= --}}
                                {{-- BUY NOW FORM --}}
                                {{-- ========================= --}}

                                @auth

                                    <form
                                        id="buyNowForm"
                                        action="{{ route('checkout.index') }}"
                                        method="GET"
                                        class="d-none"
                                    >

                                        <input
                                            type="hidden"
                                            name="product"
                                            value="{{ $product->id }}"
                                        >

                                        <input
                                            type="hidden"
                                            name="quantity"
                                            id="buyNowQuantity"
                                            value="1"
                                        >

                                    </form>

                                @endauth


                                {{-- STOCK INFORMATION --}}

                                <div class="mt-4">

                                    @if($product->stock <= 5)

                                        <div class="alert alert-warning info-box mb-0">

                                            <strong>⚠ Hurry!</strong>

                                            Only
                                            {{ $product->stock }}
                                            {{ $product->stock == 1 ? 'piece' : 'pieces' }}
                                            remaining in stock.

                                        </div>

                                    @else

                                        <div class="alert alert-success info-box mb-0">

                                            ✓ This product is available for purchase.

                                        </div>

                                    @endif

                                </div>

                            </div>

                        </div>


                    @else

                        {{-- ========================= --}}
                        {{-- OUT OF STOCK --}}
                        {{-- ========================= --}}

                        <div class="card border-danger">

                            <div class="card-body p-4 text-center">

                                <div style="font-size:55px;">
                                    😔
                                </div>

                                <h4 class="fw-bold text-danger mt-2">

                                    Out of Stock

                                </h4>

                                <p class="text-muted mb-3">

                                    This product is currently unavailable.

                                </p>


                                <button
                                    type="button"
                                    class="btn btn-secondary btn-lg me-2"
                                    disabled
                                >

                                    🛒 Add to Cart

                                </button>


                                <button
                                    type="button"
                                    class="btn btn-secondary btn-lg"
                                    disabled
                                >

                                    ⚡ Buy Now

                                </button>

                            </div>

                        </div>

                    @endif


                    {{-- ========================= --}}
                    {{-- ADMIN ACTIONS --}}
                    {{-- ========================= --}}

                    @auth

                        @if(auth()->user()->isAdmin())

                            <div class="admin-actions mt-4">

                                <div class="mb-3">

                                    <small class="text-muted fw-semibold">
                                        ADMIN ACTIONS
                                    </small>

                                </div>


                                {{-- EDIT PRODUCT --}}

                                <a
                                    href="{{ route('admin.products.edit', $product->id) }}"
                                    class="btn btn-warning me-2"
                                >

                                    ✏️ Edit Product

                                </a>


                                {{-- DELETE PRODUCT --}}

                                <form
                                    action="{{ route('admin.products.destroy', $product->id) }}"
                                    method="POST"
                                    class="d-inline"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger me-2"
                                        onclick="return confirm('Are you sure you want to delete this product?')"
                                    >

                                        🗑️ Delete

                                    </button>

                                </form>


                                {{-- ADMIN PRODUCT LIST --}}

                                <a
                                    href="{{ route('admin.products.index') }}"
                                    class="btn btn-dark"
                                >

                                    📦 Admin Products

                                </a>

                            </div>

                        @endif

                    @endauth

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ========================= --}}
{{-- BUY NOW JAVASCRIPT --}}
{{-- ========================= --}}


@include('products._trust')

@include('products._whatsapp')

@include('products._related')

@include('products._recent')

@if($product->stock > 0)
<div class="sticky-buybar" id="stickyBuyBar">
    <div class="inner">
        <div class="summary">
            <strong>{{ $product->name }}</strong>
            <small>৳{{ number_format($product->price, 0) }} • {{ $product->stock }} in stock</small>
        </div>
        <button type="button" class="btn btn-dark rounded-pill px-4" onclick="document.getElementById('quantity')?.focus(); window.scrollTo({top: document.querySelector('.purchase-card')?.getBoundingClientRect().top + window.scrollY - 100, behavior:'smooth'});">Quantity</button>
        <button type="button" class="btn btn-primary rounded-pill px-4" onclick="buyNow()">Buy Now</button>
    </div>
</div>
@endif

{{-- ========================= --}}
{{-- REVIEWS --}}
{{-- ========================= --}}

@php
    $reviews = $product->approvedReviews;
    $avgRating = round((float) $reviews->avg('rating'), 1);
    $myReview = auth()->check() ? $reviews->firstWhere('user_id', auth()->id()) : null;
@endphp

<div class="card border-0 shadow-sm mt-4" id="reviews" style="border-radius:20px;">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">Customer Reviews</h4>
            <div class="text-end">
                <span class="fs-4 fw-bold">{{ $reviews->count() ? number_format($avgRating, 1) : '—' }}</span>
                <span class="text-warning">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-{{ $i <= round($avgRating) ? 'solid' : 'regular' }} fa-star"></i>
                    @endfor
                </span>
                <div class="small text-muted">{{ $reviews->count() }} review(s)</div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2">{{ session('success') }}</div>
        @endif

        @auth
            <form method="POST" action="{{ route('reviews.store', $product) }}" class="border rounded p-3 mb-4">
                @csrf
                <h6 class="fw-semibold">{{ $myReview ? 'Update your review' : 'Write a review' }}</h6>

                <div class="mb-2">
                    <select name="rating" class="form-select w-auto @error('rating') is-invalid @enderror" required>
                        <option value="">Rating</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" @selected(old('rating', $myReview->rating ?? null) == $i)>
                                {{ str_repeat('★', $i) }}{{ str_repeat('☆', 5 - $i) }} ({{ $i }})
                            </option>
                        @endfor
                    </select>
                    @error('rating') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <input type="text" name="title" class="form-control mb-2" maxlength="150"
                    placeholder="Title (optional)" value="{{ old('title', $myReview->title ?? '') }}">

                <textarea name="comment" rows="3" class="form-control mb-2" maxlength="2000"
                    placeholder="Share your experience...">{{ old('comment', $myReview->comment ?? '') }}</textarea>

                <button class="btn btn-primary btn-sm">Submit review</button>
            </form>
        @else
            <p class="text-muted">
                <a href="{{ route('login') }}">Login</a> to write a review.
            </p>
        @endauth

        @forelse($reviews as $review)
            <div class="border-top py-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong>{{ $review->user->name ?? 'Customer' }}</strong>
                        <span class="text-warning ms-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                            @endfor
                        </span>
                    </div>
                    <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                </div>

                @if($review->title)
                    <div class="fw-semibold mt-1">{{ $review->title }}</div>
                @endif

                @if($review->comment)
                    <p class="mb-1 mt-1">{{ $review->comment }}</p>
                @endif

                @auth
                    @if(auth()->id() === $review->user_id || auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('reviews.destroy', $review) }}"
                              onsubmit="return confirm('Delete this review?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-link btn-sm text-danger p-0">Delete</button>
                        </form>
                    @endif
                @endauth
            </div>
        @empty
            <p class="text-muted mb-0">No reviews yet. Be the first to review this product.</p>
        @endforelse

    </div>
</div>

<script>

function buyNow()
{
    const quantityInput = document.getElementById('quantity');

    const buyNowQuantity = document.getElementById('buyNowQuantity');

    const buyNowForm = document.getElementById('buyNowForm');


    if (!quantityInput || !buyNowQuantity || !buyNowForm) {
        return;
    }


    let quantity = parseInt(quantityInput.value);

    const maxStock = {{ (int) $product->stock }};


    // Invalid quantity

    if (isNaN(quantity) || quantity < 1) {

        alert('Please select a valid quantity.');

        quantityInput.value = 1;

        quantityInput.focus();

        return;
    }


    // Quantity greater than stock

    if (quantity > maxStock) {

        alert(
            'Only ' +
            maxStock +
            ' ' +
            (maxStock === 1 ? 'piece' : 'pieces') +
            ' available in stock.'
        );

        quantityInput.value = maxStock;

        quantityInput.focus();

        return;
    }


    // Set quantity

    buyNowQuantity.value = quantity;


    // Submit Buy Now

    buyNowForm.submit();
}



// =========================
// PREVENT INVALID QUANTITY
// =========================

document.addEventListener('DOMContentLoaded', function () {
    const mainImage = document.getElementById('mainProductImage');
    const thumbs = document.querySelectorAll('.product-thumb');
    if (mainImage) {
        thumbs.forEach(function (thumb, index) {
            if (index === 0) thumb.classList.add('active');
            thumb.addEventListener('click', function () {
                mainImage.src = this.src;
                thumbs.forEach(function (item) { item.classList.remove('active'); });
                this.classList.add('active');
            });
        });
        mainImage.addEventListener('click', function () {
            if (window.innerWidth > 767) {
                this.classList.toggle('product-image-zoomed');
                this.style.transform = this.classList.contains('product-image-zoomed') ? 'scale(1.35)' : '';
                this.style.cursor = this.classList.contains('product-image-zoomed') ? 'zoom-out' : 'zoom-in';
            }
        });
    }



    const quantityInput = document.getElementById('quantity');


    if (!quantityInput) {
        return;
    }


    quantityInput.addEventListener('input', function () {

        const maxStock = {{ (int) $product->stock }};

        let value = parseInt(this.value);


        if (isNaN(value) || value < 1) {

            this.value = 1;

            return;
        }


        if (value > maxStock) {

            this.value = maxStock;

        }

    });


    quantityInput.addEventListener('change', function () {

        const maxStock = {{ (int) $product->stock }};

        let value = parseInt(this.value);


        if (isNaN(value) || value < 1) {

            this.value = 1;

        }


        if (value > maxStock) {

            this.value = maxStock;

        }

    });

});

</script>

@endsection

@push('scripts')
<script>
    window.iblTrack && window.iblTrack('ViewContent', {content_type:'product', content_name:@json($product->name), contents:[{id:@json($product->id), quantity:1, item_price:{{ (float) $product->price }}}], value:{{ (float) $product->price }}, currency:'BDT'});
</script>
@endpush