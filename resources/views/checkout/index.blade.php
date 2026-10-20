<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>Checkout - IBL CART 24</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pages/checkout-index.css') }}?v=b3">
    <link rel="stylesheet" href="{{ asset('css/ibl-ui.css') }}?v=ui2">
    <link rel="stylesheet" href="{{ asset('css/ibl-polish.css') }}?v=1">
</head>

<body class="ibl-checkout">

<!-- ========================================================= -->
<!-- NAVBAR -->
<!-- ========================================================= -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <div class="container">

        <a
            class="navbar-brand"
            href="{{ route('home') }}"
        >
            <i class="fa-solid fa-cart-shopping me-2"></i>
            IBL CART 24
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div
            class="collapse navbar-collapse"
            id="navbarNav"
        >

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="{{ route('home') }}"
                    >
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="{{ route('products.index') }}"
                    >
                        Products
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="{{ route('cart.index') }}"
                    >
                        <i class="fa-solid fa-cart-shopping me-1"></i>
                        Cart
                    </a>
                </li>

                {{-- FIXED: customer.orders -> orders.index --}}
                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="{{ route('orders.index') }}"
                    >
                        <i class="fa-solid fa-box me-1"></i>
                        My Orders
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- ========================================================= -->
<!-- MAIN -->
<!-- ========================================================= -->

<div class="container py-5">

    <!-- PAGE TITLE -->

    <div class="mb-4">

        <a
            href="{{ route('cart.index') }}"
            class="back-link"
        >
            <i class="fa-solid fa-arrow-left me-2"></i>
            Back to Cart
        </a>

        <h2 class="fw-bold page-title mt-3 mb-1">
            <i class="fa-solid fa-credit-card me-2"></i>
            Checkout
        </h2>

        <p class="text-muted mb-0">
            Complete your information to place your order.
        </p>

    </div>


    <!-- SUCCESS MESSAGE -->

    @if(session('success'))

        <div
            class="alert alert-success alert-dismissible fade show"
            role="alert"
        >

            <i class="fa-solid fa-circle-check me-2"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    <!-- ERROR MESSAGE -->

    @if(session('error'))

        <div
            class="alert alert-danger alert-dismissible fade show"
            role="alert"
        >

            <i class="fa-solid fa-circle-exclamation me-2"></i>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    <!-- VALIDATION ERRORS -->

    @if($errors->any())

        <div class="alert alert-danger">

            <strong>
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                Please fix the following errors:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <!-- CHECKOUT ROW -->

    <div class="row g-4">

        <!-- ================================================= -->
        <!-- CUSTOMER INFORMATION -->
        <!-- ================================================= -->

        <div class="col-lg-7">

            <div class="card checkout-card">

                <div class="card-header">

                    <i class="fa-solid fa-user me-2"></i>

                    Customer Information

                </div>


                <div class="card-body p-4">

                    <form
                        action="{{ route('checkout.store') }}"
                        method="POST"
                        id="checkoutForm"
                    >

                        @csrf


                        <!-- CUSTOMER NAME -->

                        <div class="mb-3">

                            <label
                                for="customer_name"
                                class="form-label fw-semibold"
                            >
                                Full Name
                                <span class="required-star">*</span>
                            </label>

                            <input
                                type="text"
                                name="customer_name"
                                id="customer_name"
                                class="form-control @error('customer_name') is-invalid @enderror"
                                value="{{ old('customer_name', $user->name ?? '') }}"
                                placeholder="Enter your full name"
                                maxlength="255"
                                autocomplete="name"
                                required
                            >

                            @error('customer_name')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        <!-- PHONE -->

                        <div class="mb-3">

                            <label
                                for="phone"
                                class="form-label fw-semibold"
                            >
                                Phone Number
                                <span class="required-star">*</span>
                            </label>

                            <input
                                type="text"
                                name="phone"
                                id="phone"
                                inputmode="tel"
                                class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone') }}"
                                placeholder="01XXXXXXXXX"
                                maxlength="30"
                                autocomplete="tel"
                                required
                            >

                            @error('phone')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        <!-- ADDRESS -->

                        <div class="mb-3">

                            <label
                                for="address"
                                class="form-label fw-semibold"
                            >
                                Delivery Address
                                <span class="required-star">*</span>
                            </label>

                            <textarea
                                name="address"
                                id="address"
                                rows="4"
                                class="form-control @error('address') is-invalid @enderror"
                                placeholder="House, Road, Area, District"
                                maxlength="1000"
                                autocomplete="street-address"
                                required
                            >{{ old('address') }}</textarea>

                            @error('address')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        <!-- DELIVERY AREA -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Delivery Area
                                <span class="required-star">*</span>
                            </label>

                            <div class="row g-2">
                                @foreach(['inside_dhaka' => 'Inside Dhaka', 'outside_dhaka' => 'Outside Dhaka'] as $areaKey => $areaLabel)
                                    <div class="col-6">
                                        <input
                                            type="radio"
                                            class="btn-check"
                                            name="delivery_area"
                                            id="area_{{ $areaKey }}"
                                            value="{{ $areaKey }}"
                                            autocomplete="off"
                                            {{ old('delivery_area', 'inside_dhaka') === $areaKey ? 'checked' : '' }}
                                        >
                                        <label class="btn btn-outline-primary w-100 text-start py-2" for="area_{{ $areaKey }}">
                                            <span class="fw-semibold d-block">{{ $areaLabel }}</span>
                                            <small>Delivery ৳{{ number_format($deliveryRates[$areaKey] ?? 0, 0) }}</small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            @error('delivery_area')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            @if((float) config('shop.delivery.free_above', 0) > 0)
                                <div class="form-text">
                                    Free delivery on orders of ৳{{ number_format((float) config('shop.delivery.free_above'), 0) }} or more.
                                </div>
                            @endif

                        </div>


                        <!-- ================================================= -->

                        <!-- PAYMENT METHOD -->
                        <!-- ================================================= -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                <i class="fa-solid fa-wallet me-1"></i>

                                Payment Method

                                <span class="required-star">*</span>

                            </label>


                            <!-- COD -->

                            <div
                                class="payment-option"
                                data-payment-option="cod"
                            >

                                <div class="form-check">

                                    <input
                                        class="form-check-input payment-radio"
                                        type="radio"
                                        name="payment_method"
                                        id="cod"
                                        value="Cash on Delivery"
                                        {{ old('payment_method', 'Cash on Delivery') === 'Cash on Delivery' ? 'checked' : '' }}
                                        required
                                    >

                                    <label
                                        class="form-check-label"
                                        for="cod"
                                    >

                                        <strong>
                                            <i class="fa-solid fa-money-bill-wave me-2"></i>
                                            Cash on Delivery
                                        </strong>

                                        <div class="text-muted small mt-1">
                                            Pay when your order arrives.
                                        </div>

                                    </label>

                                </div>

                            </div>


                            <!-- BKASH -->

                            <div
                                class="payment-option"
                                data-payment-option="bkash"
                            >

                                <div class="form-check">

                                    <input
                                        class="form-check-input payment-radio"
                                        type="radio"
                                        name="payment_method"
                                        id="bkash"
                                        value="bKash"
                                        {{ old('payment_method') === 'bKash' ? 'checked' : '' }}
                                    >

                                    <label
                                        class="form-check-label"
                                        for="bkash"
                                    >

                                        <strong>

                                            <i class="fa-solid fa-mobile-screen-button me-2"></i>

                                            bKash

                                            <span class="payment-badge badge-manual">
                                                Manual
                                            </span>

                                        </strong>

                                        <div class="text-muted small mt-1">
                                            Send payment to our bKash merchant number.
                                        </div>

                                    </label>

                                </div>

                            </div>


                            <!-- BKASH DETAILS -->

                            <div
                                id="bkashPaymentBox"
                                class="manual-payment-box bkash-box"
                            >

                                <div class="d-flex align-items-start">

                                    <div class="info-icon">
                                        <i class="fa-solid fa-mobile-screen-button"></i>
                                    </div>

                                    <div class="flex-grow-1">

                                        <div class="payment-title">
                                            bKash Payment
                                        </div>

                                        <div class="payment-instruction mb-2">
                                            Send the exact order amount to the
                                            bKash merchant number below.
                                        </div>

                                        <div class="mb-3">

                                            <small class="text-muted d-block">
                                                bKash Merchant Number
                                            </small>

                                            <div class="merchant-number">
                                                {{ $bkashMerchantNumber ?? env('BKASH_MERCHANT_NUMBER', '01XXXXXXXXX') }}
                                            </div>

                                        </div>

                                        <div>

                                            <label
                                                for="bkash_transaction_id"
                                                class="form-label fw-semibold"
                                            >
                                                bKash Transaction ID
                                            </label>

                                            <input
                                                type="text"
                                                id="bkash_transaction_id"
                                                class="form-control transaction-input"
                                                placeholder="Enter bKash Transaction ID"
                                                maxlength="255"
                                                value="{{ old('payment_transaction_id') }}"
                                            >

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <!-- NAGAD -->

                            <div
                                class="payment-option"
                                data-payment-option="nagad"
                            >

                                <div class="form-check">

                                    <input
                                        class="form-check-input payment-radio"
                                        type="radio"
                                        name="payment_method"
                                        id="nagad"
                                        value="Nagad"
                                        {{ old('payment_method') === 'Nagad' ? 'checked' : '' }}
                                    >

                                    <label
                                        class="form-check-label"
                                        for="nagad"
                                    >

                                        <strong>

                                            <i class="fa-solid fa-mobile-screen-button me-2"></i>

                                            Nagad

                                            <span class="payment-badge badge-manual">
                                                Manual
                                            </span>

                                        </strong>

                                        <div class="text-muted small mt-1">
                                            Send payment to our Nagad merchant number.
                                        </div>

                                    </label>

                                </div>

                            </div>


                            <!-- NAGAD DETAILS -->

                            <div
                                id="nagadPaymentBox"
                                class="manual-payment-box nagad-box"
                            >

                                <div class="d-flex align-items-start">

                                    <div class="info-icon">
                                        <i class="fa-solid fa-mobile-screen-button"></i>
                                    </div>

                                    <div class="flex-grow-1">

                                        <div class="payment-title">
                                            Nagad Payment
                                        </div>

                                        <div class="payment-instruction mb-2">
                                            Send the exact order amount to the
                                            Nagad merchant number below.
                                        </div>

                                        <div class="mb-3">

                                            <small class="text-muted d-block">
                                                Nagad Merchant Number
                                            </small>

                                            <div class="merchant-number">
                                                {{ $nagadMerchantNumber ?? env('NAGAD_MERCHANT_NUMBER', '01XXXXXXXXX') }}
                                            </div>

                                        </div>

                                        <div>

                                            <label
                                                for="nagad_transaction_id"
                                                class="form-label fw-semibold"
                                            >
                                                Nagad Transaction ID
                                            </label>

                                            <input
                                                type="text"
                                                id="nagad_transaction_id"
                                                class="form-control transaction-input"
                                                placeholder="Enter Nagad Transaction ID"
                                                maxlength="255"
                                                value="{{ old('payment_transaction_id') }}"
                                            >

                                        </div>

                                    </div>

                                </div>

                            </div>


@if(config('shop.online_payment_enabled', true))
                            <!-- ONLINE PAYMENT -->

                            <div
                                class="payment-option"
                                data-payment-option="online"
                            >

                                <div class="form-check">

                                    <input
                                        class="form-check-input payment-radio"
                                        type="radio"
                                        name="payment_method"
                                        id="online"
                                        value="Online Payment"
                                        {{ old('payment_method') === 'Online Payment' ? 'checked' : '' }}
                                    >

                                    <label
                                        class="form-check-label"
                                        for="online"
                                    >

                                        <strong>

                                            <i class="fa-solid fa-credit-card me-2"></i>

                                            Online Payment

                                            <span class="payment-badge badge-gateway">
                                                SSLCommerz
                                            </span>

                                        </strong>

                                        <div class="text-muted small mt-1">
                                            Card / mobile banking / internet banking.
                                        </div>

                                    </label>

                                </div>

                            </div>


                            <!-- ONLINE PAYMENT INFO -->

                            <div
                                id="onlinePaymentBox"
                                class="manual-payment-box online-box"
                            >

                                <div class="d-flex align-items-start">

                                    <div class="info-icon">
                                        <i class="fa-solid fa-shield-halved"></i>
                                    </div>

                                    <div>

                                        <div class="payment-title">
                                            Secure Online Payment
                                        </div>

                                        <div class="payment-instruction">

                                            Your order will be created first.
                                            You will then be redirected to the
                                            SSLCommerz secure payment gateway.

                                            Card details, PIN and OTP should only
                                            be entered on the secure gateway page.

                                        </div>

                                        <div class="alert alert-info mt-3 mb-0 py-2">

                                            <i class="fa-solid fa-circle-info me-1"></i>

                                            You will be redirected to SSLCommerz
                                            after placing the order.

                                        </div>

                                    </div>

                                </div>

                            </div>


@endif

                            <!-- HIDDEN TRANSACTION ID -->

                            <input
                                type="hidden"
                                name="payment_transaction_id"
                                id="payment_transaction_id"
                                value="{{ old('payment_transaction_id') }}"
                            >


                            @error('payment_method')

                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>

                            @enderror


                            @error('payment_transaction_id')

                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        <!-- ORDER NOTE -->

                        <div class="mb-4">

                            <label
                                for="note"
                                class="form-label fw-semibold"
                            >

                                Order Note

                                <span class="text-muted fw-normal">
                                    (Optional)
                                </span>

                            </label>

                            <textarea
                                name="note"
                                id="note"
                                rows="3"
                                class="form-control @error('note') is-invalid @enderror"
                                placeholder="Any special instructions..."
                                maxlength="1000"
                            >{{ old('note') }}</textarea>

                            @error('note')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        <!-- PAYMENT WARNING -->

                        <div
                            id="paymentWarning"
                            class="alert alert-warning d-none"
                        >

                            <i class="fa-solid fa-triangle-exclamation me-2"></i>

                            Please enter your payment Transaction ID before
                            placing the order.

                        </div>


                        <!-- SUBMIT -->

                        <button
                            type="submit"
                            class="btn btn-primary w-100 place-order-btn"
                            id="placeOrderBtn"
                        >

                            <i class="fa-solid fa-check me-2"></i>

                            Place Order

                        </button>

                    </form>

                </div>

            </div>

        </div>


        <!-- ================================================= -->
        <!-- ORDER SUMMARY -->
        <!-- ================================================= -->

        <div class="col-lg-5">

            <div class="card checkout-card order-summary">

                <div class="card-header">

                    <i class="fa-solid fa-cart-shopping me-2"></i>

                    Your Order

                </div>

                <div class="card-body p-4">

                    @forelse($cartItems as $item)

                        @php

                            $product = $item['product'] ?? null;

                            $quantity = (int) ($item['quantity'] ?? 1);

                            $price = (float) (
                                $item['price']
                                ?? ($product->price ?? 0)
                            );

                            $itemSubtotal = (float) (
                                $item['subtotal']
                                ?? ($price * $quantity)
                            );

                        @endphp


                        @if($product)

                            <div class="cart-item">

                                <div class="row align-items-center">

                                    <!-- PRODUCT IMAGE -->

                                    <div class="col-auto">

                                        @if(!empty($product->image))

                                            @php

                                                $image = $product->image;

                                                $storagePath = public_path(
                                                    'storage/' . $image
                                                );

                                                $uploadPath = public_path(
                                                    'uploads/products/' . $image
                                                );

                                                $imagePath = public_path(
                                                    'images/' . $image
                                                );

                                            @endphp


                                            @if(file_exists($uploadPath))

                                                <img
                                                    src="{{ asset('uploads/products/' . $image) }}"
                                                    alt="{{ $product->name }}"
                                                    class="product-image"
                                                >

                                            @elseif(file_exists($storagePath))

                                                <img
                                                    src="{{ asset('storage/' . $image) }}"
                                                    alt="{{ $product->name }}"
                                                    class="product-image"
                                                >

                                            @elseif(file_exists($imagePath))

                                                <img
                                                    src="{{ asset('images/' . $image) }}"
                                                    alt="{{ $product->name }}"
                                                    class="product-image"
                                                >

                                            @else

                                                <div class="product-image d-flex align-items-center justify-content-center">

                                                    <i class="fa-solid fa-image text-muted"></i>

                                                </div>

                                            @endif

                                        @else

                                            <div class="product-image d-flex align-items-center justify-content-center">

                                                <i class="fa-solid fa-image text-muted"></i>

                                            </div>

                                        @endif

                                    </div>


                                    <!-- PRODUCT DETAILS -->

                                    <div class="col">

                                        <div class="product-name mb-1">

                                            {{ $product->name }}

                                        </div>

                                        <small class="text-muted">

                                            ৳{{ number_format($price, 2) }}

                                            ×

                                            {{ $quantity }}

                                        </small>

                                    </div>


                                    <!-- ITEM TOTAL -->

                                    <div class="col-auto">

                                        <strong>

                                            ৳{{ number_format($itemSubtotal, 2) }}

                                        </strong>

                                    </div>

                                </div>

                            </div>

                        @endif

                    @empty

                        <div class="text-center py-4">

                            <i
                                class="fa-solid fa-cart-shopping fa-2x text-muted mb-3"
                            ></i>

                            <p class="text-muted">
                                Your cart is empty.
                            </p>

                            <a
                                href="{{ route('products.index') }}"
                                class="btn btn-primary"
                            >

                                <i class="fa-solid fa-store me-1"></i>

                                Continue Shopping

                            </a>

                        </div>

                    @endforelse


                    <!-- TOTAL -->

                    @if(count($cartItems) > 0)

                        <div class="summary-row mt-3">

                            <span>
                                Subtotal
                            </span>

                            <strong>
                                ৳{{ number_format($subtotal, 2) }}
                            </strong>

                        </div>


                        <div class="summary-row">

                            <span>
                                Delivery Charge
                            </span>

                            <strong id="deliveryChargeText">
                                ৳{{ number_format($deliveryCharge, 2) }}
                            </strong>

                        </div>


                        @if(!empty($coupon) && $discount > 0)
                            <div class="summary-row">
                                <span>Coupon ({{ $coupon->code }})</span>
                                <strong class="text-success">-৳{{ number_format($discount, 2) }}</strong>
                            </div>
                        @endif

                        <div class="summary-row total-row">

                            <span>
                                Total
                            </span>

                            <span id="orderTotalText">
                                ৳{{ number_format($total, 2) }}
                            </span>

                        </div>


                        <div class="secure-box text-center mt-3">

                            <i class="fa-solid fa-shield-halved me-2"></i>

                            Your order information is securely processed.

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>


<!-- ========================================================= -->
<!-- BOOTSTRAP JS -->
<!-- ========================================================= -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>


<!-- ========================================================= -->
<!-- PAYMENT METHOD SCRIPT -->
<!-- ========================================================= -->

<script src="{{ asset('js/pages/checkout-index.js') }}?v=b3"></script>

<script>
(function () {
    var rates = @json($deliveryRates ?? []);
    var subtotal = {{ (float) ($subtotal ?? 0) }};
    var discount = {{ (float) ($discount ?? 0) }};
    var freeAbove = {{ (float) config('shop.delivery.free_above', 0) }};

    function money(n) {
        return '৳' + Number(n).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function updateDelivery() {
        var picked = document.querySelector('input[name="delivery_area"]:checked');
        if (!picked) { return; }

        var fee = (freeAbove > 0 && subtotal >= freeAbove) ? 0 : (rates[picked.value] || 0);
        var feeEl = document.getElementById('deliveryChargeText');
        var totalEl = document.getElementById('orderTotalText');

        if (feeEl) { feeEl.textContent = fee === 0 ? 'Free' : money(fee); }
        if (totalEl) { totalEl.textContent = money(Math.max(0, subtotal + fee - discount)); }
    }

    document.querySelectorAll('input[name="delivery_area"]').forEach(function (el) {
        el.addEventListener('change', updateDelivery);
    });

    updateDelivery();
})();
</script>

@if(config('shop.abandoned_cart.enabled', true))
<script>
/* Saves the cart + phone once the customer types a number (used for one reminder SMS). */
(function () {
    var phone = document.getElementById('phone');
    var name  = document.querySelector('input[name="customer_name"]');
    var meta  = document.querySelector('meta[name="csrf-token"]');
    if (!phone || !meta) { return; }
    var last = '';
    function save() {
        var digits = phone.value.replace(/\D+/g, '');
        if (digits.length < 10 || digits === last) { return; }
        last = digits;
        fetch(@json(route('checkout.capture')), {
            method: 'POST',
            credentials: 'same-origin',
            keepalive: true,
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': meta.content},
            body: JSON.stringify({phone: phone.value, customer_name: name ? name.value : ''})
        }).catch(function () {});
    }
    phone.addEventListener('blur', save);
    phone.addEventListener('change', save);
})();
</script>
@endif

</body>
</html>