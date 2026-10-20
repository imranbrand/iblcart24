@extends('layouts.storefront')

@section('title', 'Shop Smart. Shop Premium.')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/home.css') }}?v=premium-v2">
@endpush

@section('content')
@php
    $visibleCategories = $categories->filter(fn ($category) => (int) ($category->products_count ?? 0) > 0)->values();
    $featured = $products instanceof \Illuminate\Pagination\LengthAwarePaginator ? $products->getCollection() : collect($products);
@endphp

<div class="home-v2">
    {{-- HERO --}}
    <section class="container pt-4 pt-lg-5">
        <div class="premium-hero">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="hero-kicker"><i class="fa-solid fa-sparkles"></i> IBL CART 24 • PREMIUM STORE</span>
                    <h1>Shop Smart.<br><span>Shop Premium.</span></h1>
                    <p class="hero-copy">Curated fashion, jewellery, accessories and lifestyle essentials — with easy ordering and reliable delivery across Bangladesh.</p>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <a href="{{ route('products.index') }}" class="btn btn-light btn-lg px-4 hero-primary"><i class="fa-solid fa-bag-shopping me-2"></i>Shop Collection</a>
                        <a href="#featured" class="btn btn-outline-light btn-lg px-4"><i class="fa-solid fa-arrow-down me-2"></i>Explore</a>
                    </div>
                    <div class="hero-trust-row">
                        <span><i class="fa-solid fa-shield-halved"></i> Secure checkout</span>
                        <span><i class="fa-solid fa-truck-fast"></i> Reliable delivery</span>
                        <span><i class="fa-solid fa-headset"></i> Customer care</span>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="hero-showcase">
                        <div class="floating-badge badge-one"><i class="fa-solid fa-star"></i> Premium picks</div>
                        <div class="hero-orbit hero-orbit-one"></div>
                        <div class="hero-orbit hero-orbit-two"></div>
                        <div class="hero-product-icon"><i class="fa-solid fa-gem"></i></div>
                        <div class="floating-badge badge-two"><i class="fa-solid fa-bolt"></i> Fresh deals</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- QUICK BENEFITS --}}
    <section class="container mt-4">
        <div class="benefit-grid">
            <div class="benefit-card"><div class="benefit-icon"><i class="fa-solid fa-credit-card"></i></div><div><strong>Flexible payment</strong><small>COD & online payment options</small></div></div>
            <div class="benefit-card"><div class="benefit-icon"><i class="fa-solid fa-truck-fast"></i></div><div><strong>Delivery support</strong><small>Dhaka & outside Dhaka delivery</small></div></div>
            <div class="benefit-card"><div class="benefit-icon"><i class="fa-solid fa-rotate-left"></i></div><div><strong>Customer-first service</strong><small>Clear policies & order tracking</small></div></div>
            <div class="benefit-card"><div class="benefit-icon"><i class="fa-solid fa-lock"></i></div><div><strong>Safe shopping</strong><small>Protected account & checkout flow</small></div></div>
        </div>
    </section>

    {{-- CATEGORIES --}}
    <section class="container section-space">
        <div class="section-heading">
            <div><span class="eyebrow">DISCOVER</span><h2>Shop by Category</h2><p>Find your next favourite in a few clicks.</p></div>
            <a href="{{ route('categories.index') }}" class="section-link">View all <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="row g-3">
            @forelse($visibleCategories->take(8) as $category)
                @php
                    $categoryIcon = match (strtolower($category->name)) {
                        'jewellery', 'jewelry' => 'fa-gem',
                        'fashion' => 'fa-shirt',
                        'accessories' => 'fa-bag-shopping',
                        'gift items', 'gifts' => 'fa-gift',
                        'bracelets' => 'fa-circle-dot',
                        'necklaces' => 'fa-gem',
                        'watches' => 'fa-clock',
                        'bags' => 'fa-bag-shopping',
                        'shoes' => 'fa-shoe-prints',
                        default => 'fa-layer-group',
                    };
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('categories.show', $category) }}" class="category-tile">
                        <div class="category-icon"><i class="fa-solid {{ $categoryIcon }}"></i></div>
                        <div class="category-name">{{ $category->name }}</div>
                        <small>{{ $category->products_count }} {{ $category->products_count == 1 ? 'item' : 'items' }}</small>
                    </a>
                </div>
            @empty
                <div class="col-12"><div class="empty-state"><i class="fa-solid fa-layer-group"></i><h5>No categories available yet</h5><p>New collections will appear here soon.</p></div></div>
            @endforelse
        </div>
    </section>

    {{-- DEAL BANNER --}}
    <section class="container">
        <div class="deal-banner">
            <div><span class="eyebrow text-white-50">LIMITED-TIME PICKS</span><h2>Fresh deals, better value.</h2><p>Explore products with attractive prices while stock lasts.</p></div>
            <a href="{{ route('products.index', ['sort' => 'price_low']) }}" class="btn btn-light rounded-pill px-4">Shop deals <i class="fa-solid fa-arrow-right ms-2"></i></a>
        </div>
    </section>

    {{-- PRODUCTS --}}
    <section class="container section-space" id="featured">
        <div class="section-heading">
            <div><span class="eyebrow">CURATED FOR YOU</span><h2>Featured Products</h2><p>Popular products currently available in stock.</p></div>
            <a href="{{ route('products.index') }}" class="section-link">Browse all <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            @forelse($featured->take(8) as $product)
                @php $wishlisted = isset($wishlistProductIds) && $wishlistProductIds->contains((int) $product->id); @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <article class="premium-product-card h-100">
                        <div class="product-media">
                            @if($product->is_on_sale)<span class="product-badge sale">-{{ $product->discount_percent }}%</span>@endif
                            @if($product->stock > 0 && $product->stock <= 5)<span class="product-badge stock">Only {{ $product->stock }} left</span>@endif
                            <a href="{{ route('products.show', $product) }}">
                                @if($product->image)
                                    <img src="{{ asset('uploads/products/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <div class="product-placeholder"><i class="fa-regular fa-image"></i></div>
                                @endif
                            </a>
                            <div class="media-actions">
                                <a href="{{ route('products.show', $product) }}" aria-label="View {{ $product->name }}"><i class="fa-regular fa-eye"></i></a>
                                @auth
                                    <form method="POST" action="{{ $wishlisted ? route('wishlist.destroy', $product) : route('wishlist.store', $product) }}">
                                        @csrf
                                        @if($wishlisted) @method('DELETE') @endif
                                        <button type="submit" aria-label="{{ $wishlisted ? 'Remove from wishlist' : 'Add to wishlist' }}" class="{{ $wishlisted ? 'active' : '' }}"><i class="fa-{{ $wishlisted ? 'solid' : 'regular' }} fa-heart"></i></button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" aria-label="Login to use wishlist"><i class="fa-regular fa-heart"></i></a>
                                @endauth
                            </div>
                        </div>
                        <div class="p-3 p-lg-4 d-flex flex-column h-100">
                            <span class="product-category">{{ $product->category->name ?? 'Collection' }}</span>
                            <h3><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h3>
                            <div class="rating-row"><span class="stars"><i class="fa-solid fa-star"></i></span><span>{{ number_format((float) ($product->approved_reviews_avg_rating ?? 0), 1) }}</span><span class="muted">({{ $product->approved_reviews_count ?? 0 }})</span></div>
                            <div class="price-row mt-auto pt-3"><div><strong>৳{{ number_format($product->price, 0) }}</strong>@if($product->is_on_sale)<del>৳{{ number_format($product->compare_price, 0) }}</del>@endif</div><a href="{{ route('products.show', $product) }}" class="mini-buy" aria-label="View product"><i class="fa-solid fa-arrow-right"></i></a></div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="empty-state"><i class="fa-solid fa-bag-shopping"></i><h5>No products available</h5><p>Please check again soon.</p></div></div>
            @endforelse
        </div>
    </section>

    {{-- WHY US --}}
    <section class="container section-space">
        <div class="why-section">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5"><span class="eyebrow">WHY IBL CART 24</span><h2>Designed around a better shopping experience.</h2><p>From discovery to delivery, every important step is designed to stay simple, transparent and mobile-friendly.</p><a href="{{ route('pages.about') }}" class="btn btn-dark rounded-pill px-4">Learn more <i class="fa-solid fa-arrow-right ms-2"></i></a></div>
                <div class="col-lg-7"><div class="row g-3"><div class="col-6"><div class="why-card"><i class="fa-solid fa-magnifying-glass"></i><h5>Easy discovery</h5><p>Search, categories and product details stay close at hand.</p></div></div><div class="col-6"><div class="why-card"><i class="fa-solid fa-bolt"></i><h5>Fast decisions</h5><p>Price, stock, offers and reviews are easy to understand.</p></div></div><div class="col-6"><div class="why-card"><i class="fa-solid fa-location-dot"></i><h5>Track orders</h5><p>Follow your order journey from checkout to delivery.</p></div></div><div class="col-6"><div class="why-card"><i class="fa-solid fa-headset"></i><h5>Support</h5><p>Customer care and WhatsApp support are always within reach.</p></div></div></div></div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="container pb-5">
        <div class="newsletter-card">
            <div><span class="eyebrow text-white-50">STAY IN THE LOOP</span><h2>New drops. Fresh offers. No noise.</h2><p>Keep IBL CART 24 in your favourites and discover new collections as they arrive.</p></div>
            <a href="{{ route('products.index') }}" class="btn btn-light btn-lg rounded-pill px-4">Start shopping <i class="fa-solid fa-arrow-right ms-2"></i></a>
        </div>
    </section>
</div>
@endsection
