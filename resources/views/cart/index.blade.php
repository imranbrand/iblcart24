@extends('layouts.storefront')

@section('content')

<link rel="stylesheet" href="{{ asset('css/pages/cart-index.css') }}?v=b3">


<div class="cart-page">

    <div class="container">

        <div class="mb-4">
            <h1 class="cart-title">
                <i class="fa-solid fa-cart-shopping me-2"></i>
                My Cart
            </h1>

            <p class="cart-subtitle">
                Review your selected products before checkout.
            </p>
        </div>


        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa-solid fa-circle-check me-2"></i>
                {{ session('success') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>
        @endif


        {{-- Error Message --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                {{ session('error') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>
        @endif


        @if(empty($cart))

            <div class="empty-cart">

                <div class="empty-cart-icon">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>

                <h2>Your Cart is Empty</h2>

                <p>
                    You haven't added any products to your cart yet.
                </p>

                <a
                    href="{{ route('products.index') }}"
                    class="btn-checkout"
                    style="max-width:240px;margin:0 auto;"
                >
                    <i class="fa-solid fa-store me-2"></i>
                    Continue Shopping
                </a>

            </div>

        @else

            <div class="row g-4">

                {{-- CART ITEMS --}}
                <div class="col-lg-8">

                    <div class="cart-card">

                        @foreach($cart as $itemKey => $item)

                            @php
                                $itemId = $item['id'] ?? $item['product_id'] ?? $itemKey;
                                $itemName = $item['name'] ?? 'Product';
                                $itemPrice = (float) ($item['price'] ?? 0);
                                $itemQuantity = (int) ($item['quantity'] ?? 1);
                                $itemStock = (int) ($item['stock'] ?? 1);
                                $itemImage = $item['image'] ?? null;

                                $itemSubtotal =
                                    $itemPrice * $itemQuantity;
                            @endphp


                            <div class="cart-item">

                                <div class="row align-items-center g-3">

                                    {{-- IMAGE --}}
                                    <div class="col-auto">

                                        @if($itemImage)

                                            <img
                                                src="{{ asset('uploads/products/' . $itemImage) }}"
                                                alt="{{ $itemName }}"
                                                class="cart-product-image"
                                                onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                                            >

                                            <div
                                                class="cart-product-placeholder"
                                                style="display:none;"
                                            >
                                                <i class="fa-solid fa-image"></i>
                                            </div>

                                        @else

                                            <div class="cart-product-placeholder">
                                                <i class="fa-solid fa-image"></i>
                                            </div>

                                        @endif

                                    </div>


                                    {{-- PRODUCT INFO --}}
                                    <div class="col">

                                        <a
                                            href="{{ $itemId ? route('products.show', $itemId) : '#' }}"
                                            class="cart-product-name"
                                        >
                                            {{ $itemName }}
                                        </a>

                                        <div class="cart-category">
                                            <i class="fa-solid fa-tag me-1"></i>
                                            Product
                                        </div>

                                        <div class="cart-price">
                                            ৳{{ number_format($itemPrice, 2) }}
                                        </div>

                                    </div>


                                    {{-- QUANTITY --}}
                                    <div class="col-lg-3">

                                        <form
                                            method="POST"
                                            action="{{ route('cart.update', $itemId) }}"
                                            class="quantity-form"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="number"
                                                name="quantity"
                                                value="{{ $itemQuantity }}"
                                                min="1"
                                                max="{{ max(1, $itemStock) }}"
                                                class="quantity-input"
                                                required
                                            >

                                            <button
                                                type="submit"
                                                class="btn-update"
                                            >
                                                <i class="fa-solid fa-arrows-rotate"></i>
                                            </button>

                                        </form>


                                        @if($itemStock > 0)

                                            <small class="text-muted">
                                                Stock: {{ $itemStock }}
                                            </small>

                                        @else

                                            <small class="text-danger">
                                                Out of stock
                                            </small>

                                        @endif

                                    </div>


                                    {{-- SUBTOTAL / REMOVE --}}
                                    <div class="col-lg-2 text-lg-end">

                                        <div class="cart-subtotal">
                                            ৳{{ number_format($itemSubtotal, 2) }}
                                        </div>


                                        <form
                                            method="POST"
                                            action="{{ route('cart.destroy', $itemId) }}"
                                            class="remove-form"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn-remove"
                                                onclick="return confirm('Remove this product from your cart?')"
                                            >
                                                <i class="fa-solid fa-trash-can me-1"></i>
                                                Remove
                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>


                {{-- ORDER SUMMARY --}}
                <div class="col-lg-4">

                    <div class="summary-card">

                        <h2 class="summary-title">
                            <i class="fa-solid fa-receipt me-2"></i>
                            Order Summary
                        </h2>


                        <div class="summary-row">
                            <span>Subtotal</span>

                            <strong>
                                ৳{{ number_format($subtotal, 2) }}
                            </strong>
                        </div>


                        <div class="summary-row">

                            <span>Delivery</span>

                            <strong>
                                @if($deliveryCharge > 0)
                                    ৳{{ number_format($deliveryCharge, 2) }}
                                @else
                                    Free
                                @endif
                            </strong>

                        </div>


                        @if(!empty($coupon) && $discount > 0)
                            <div class="summary-row text-success">
                                <span>Coupon ({{ $coupon->code }})</span>
                                <strong>-৳{{ number_format($discount, 2) }}</strong>
                            </div>
                        @endif

                        <div class="summary-row total">

                            <span>Total</span>

                            <span>
                                ৳{{ number_format($total, 2) }}
                            </span>

                        </div>


                        {{-- COUPON BOX --}}
                        <div class="my-3">
                            @if(!empty($coupon))
                                <form method="POST" action="{{ route('coupon.remove') }}" class="d-flex justify-content-between align-items-center">
                                    @csrf
                                    @method('DELETE')
                                    <span class="text-success small"><i class="fa-solid fa-tag me-1"></i>{{ $coupon->code }} applied</span>
                                    <button class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('coupon.apply') }}" class="input-group">
                                    @csrf
                                    <input type="text" name="code" class="form-control" placeholder="Coupon code" required>
                                    <button class="btn btn-outline-primary">Apply</button>
                                </form>
                            @endif
                        </div>

                        <a
                            href="{{ route('checkout.index') }}"
                            class="btn-checkout"
                        >
                            <i class="fa-solid fa-lock me-2"></i>
                            Proceed to Checkout
                        </a>


                        <a
                            href="{{ route('products.index') }}"
                            class="btn-continue"
                        >
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Continue Shopping
                        </a>


                        <form
                            method="POST"
                            action="{{ route('cart.clear') }}"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn-clear"
                                onclick="return confirm('Are you sure you want to clear your entire cart?')"
                            >
                                <i class="fa-solid fa-trash me-2"></i>
                                Clear Cart
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        @endif

    </div>

</div>

@endsection