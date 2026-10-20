@extends('layouts.storefront')

@section('content')

<div class="container py-5">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                <i class="fas fa-heart text-danger me-2"></i>
                My Wishlist
            </h2>

            <p class="text-muted mb-0">
                Save your favorite products and buy them whenever you want.
            </p>
        </div>

        <a href="{{ route('products.index') }}"
           class="btn btn-dark mt-3 mt-md-0">
            <i class="fas fa-shopping-bag me-2"></i>
            Continue Shopping
        </a>

    </div>


    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- Error Message --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- Info Message --}}
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show shadow-sm">
            <i class="fas fa-info-circle me-2"></i>
            {{ session('info') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- Wishlist --}}
    @if($wishlists->count() > 0)

        <div class="row g-4">

            @foreach($wishlists as $wishlist)

                @php
                    $product = $wishlist->product;
                @endphp

                @if($product)

                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">

                        <div class="card wishlist-card h-100 border-0 shadow-sm">

                            {{-- Product Image --}}
                            <div class="product-image-wrapper position-relative">

                                @if($product->image)

                                    <img src="{{ asset('storage/' . $product->image) }}"
                                         class="card-img-top product-image"
                                         alt="{{ $product->name }}">

                                @else

                                    <div class="no-image">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>

                                @endif


                                {{-- Stock Badge --}}
                                @if($product->stock <= 0)

                                    <span class="position-absolute top-0 start-0 m-3 badge bg-danger">
                                        Out of Stock
                                    </span>

                                @elseif($product->stock <= 5)

                                    <span class="position-absolute top-0 start-0 m-3 badge bg-warning text-dark">
                                        Low Stock
                                    </span>

                                @else

                                    <span class="position-absolute top-0 start-0 m-3 badge bg-success">
                                        In Stock
                                    </span>

                                @endif


                                {{-- Remove Wishlist --}}
                                <form action="{{ route('wishlist.destroy', $product) }}"
                                      method="POST"
                                      class="position-absolute top-0 end-0 m-3">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-light rounded-circle shadow-sm remove-btn"
                                            title="Remove from wishlist"
                                            onclick="return confirm('Remove this product from wishlist?')">

                                        <i class="fas fa-trash text-danger"></i>

                                    </button>

                                </form>

                            </div>


                            {{-- Product Details --}}
                            <div class="card-body d-flex flex-column">

                                {{-- Category --}}
                                @if($product->category)

                                    <small class="text-muted mb-1">
                                        <i class="fas fa-tag me-1"></i>
                                        {{ $product->category->name }}
                                    </small>

                                @endif


                                {{-- Product Name --}}
                                <h5 class="fw-bold product-name">
                                    {{ $product->name }}
                                </h5>


                                {{-- Price --}}
                                <h5 class="fw-bold text-danger mb-3">
                                    ৳{{ number_format($product->price, 2) }}
                                </h5>


                                {{-- Stock --}}
                                <div class="mb-3">

                                    @if($product->stock > 5)

                                        <small class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>
                                            {{ $product->stock }} pieces available
                                        </small>

                                    @elseif($product->stock > 0)

                                        <small class="text-warning">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Only {{ $product->stock }} left
                                        </small>

                                    @else

                                        <small class="text-danger">
                                            <i class="fas fa-times-circle me-1"></i>
                                            Currently unavailable
                                        </small>

                                    @endif

                                </div>


                                {{-- Buttons --}}
                                <div class="mt-auto">

                                    @if($product->stock > 0)

                                        {{-- Add To Cart --}}
                                        <form action="{{ route('cart.add', $product) }}"
                                              method="POST"
                                              class="mb-2">

                                            @csrf

                                            <input type="hidden"
                                                   name="quantity"
                                                   value="1">

                                            <button type="submit"
                                                    class="btn btn-dark w-100">

                                                <i class="fas fa-shopping-cart me-2"></i>
                                                Add to Cart

                                            </button>

                                        </form>


                                        {{-- Buy Now --}}
                                        <form action="{{ route('buy.now', $product) }}"
                                              method="POST"
                                              class="mb-2">

                                            @csrf

                                            <input type="hidden"
                                                   name="quantity"
                                                   value="1">

                                            <button type="submit"
                                                    class="btn btn-danger w-100">

                                                <i class="fas fa-bolt me-2"></i>
                                                Buy Now

                                            </button>

                                        </form>

                                    @else

                                        <button class="btn btn-secondary w-100"
                                                disabled>

                                            <i class="fas fa-ban me-2"></i>
                                            Out of Stock

                                        </button>

                                    @endif


                                    {{-- View Product --}}
                                    <a href="{{ route('products.show', $product) }}"
                                       class="btn btn-outline-secondary w-100 mt-2">

                                        <i class="fas fa-eye me-2"></i>
                                        View Product

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                @endif

            @endforeach

        </div>


        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-5">

            {{ $wishlists->links() }}

        </div>

    @else

        {{-- Empty Wishlist --}}
        <div class="empty-wishlist text-center py-5">

            <div class="empty-icon mb-4">
                <i class="far fa-heart"></i>
            </div>

            <h3 class="fw-bold">
                Your Wishlist is Empty
            </h3>

            <p class="text-muted mb-4">
                You haven't added any products to your wishlist yet.
            </p>

            <a href="{{ route('products.index') }}"
               class="btn btn-danger px-4 py-2">

                <i class="fas fa-shopping-bag me-2"></i>
                Explore Products

            </a>

        </div>

    @endif

</div>


{{-- Custom CSS --}}
<link rel="stylesheet" href="{{ asset('css/pages/wishlist-index.css') }}?v=b3">

@endsection