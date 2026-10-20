@extends('layouts.storefront')

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | ORDER STATUS
    |--------------------------------------------------------------------------
    */

    $currentStatus = strtolower(trim((string) ($order->status ?? 'pending')));

    $statusConfig = [
        'pending' => [
            'label' => 'Pending',
            'icon' => 'fa-clock',
            'class' => 'status-pending',
            'description' => 'Your order has been received and is waiting for confirmation.',
        ],

        'confirmed' => [
            'label' => 'Confirmed',
            'icon' => 'fa-circle-check',
            'class' => 'status-confirmed',
            'description' => 'Your order has been confirmed and will be prepared soon.',
        ],

        'processing' => [
            'label' => 'Processing',
            'icon' => 'fa-box-open',
            'class' => 'status-processing',
            'description' => 'Your order is currently being prepared.',
        ],

        'shipped' => [
            'label' => 'Shipped',
            'icon' => 'fa-truck-fast',
            'class' => 'status-shipped',
            'description' => 'Your order is on the way to your delivery address.',
        ],

        'delivered' => [
            'label' => 'Delivered',
            'icon' => 'fa-house-circle-check',
            'class' => 'status-delivered',
            'description' => 'Your order has been successfully delivered.',
        ],

        'cancelled' => [
            'label' => 'Cancelled',
            'icon' => 'fa-circle-xmark',
            'class' => 'status-cancelled',
            'description' => 'This order has been cancelled.',
        ],
    ];

    $status = $statusConfig[$currentStatus]
        ?? [
            'label' => ucfirst($currentStatus),
            'icon' => 'fa-circle-info',
            'class' => 'status-default',
            'description' => 'Your order status has been updated.',
        ];

    /*
    |--------------------------------------------------------------------------
    | TRACKING
    |--------------------------------------------------------------------------
    */

    $trackingStatuses = [
        'pending',
        'confirmed',
        'processing',
        'shipped',
        'delivered',
    ];

    $currentIndex = array_search(
        $currentStatus,
        $trackingStatuses,
        true
    );

    if ($currentIndex === false) {
        $currentIndex = 0;
    }

    $progressPercent = count($trackingStatuses) > 1
        ? ($currentIndex / (count($trackingStatuses) - 1)) * 100
        : 0;

    /*
    |--------------------------------------------------------------------------
    | PAYMENT STATUS
    |--------------------------------------------------------------------------
    */

    $paymentStatus = strtolower(
        trim((string) ($order->payment_status ?? 'pending'))
    );

    $paymentConfig = [
        'paid' => [
            'label' => 'Paid',
            'icon' => 'fa-circle-check',
            'class' => 'payment-paid',
        ],

        'failed' => [
            'label' => 'Failed',
            'icon' => 'fa-circle-xmark',
            'class' => 'payment-failed',
        ],

        'refunded' => [
            'label' => 'Refunded',
            'icon' => 'fa-rotate-left',
            'class' => 'payment-refunded',
        ],

        'pending' => [
            'label' => 'Pending',
            'icon' => 'fa-clock',
            'class' => 'payment-pending',
        ],
    ];

    $payment = $paymentConfig[$paymentStatus]
        ?? [
            'label' => ucfirst($paymentStatus),
            'icon' => 'fa-circle-info',
            'class' => 'payment-default',
        ];

    /*
    |--------------------------------------------------------------------------
    | ORDER TOTALS
    |--------------------------------------------------------------------------
    */

    $subtotal = (float) (
        $order->subtotal
        ?? 0
    );

    $deliveryCharge = (float) (
        $order->delivery_charge
        ?? 0
    );

    $totalAmount = (float) (
        $order->total_amount
        ?? $order->total
        ?? ($subtotal + $deliveryCharge)
    );

    /*
    |--------------------------------------------------------------------------
    | ORDER DATE
    |--------------------------------------------------------------------------
    */

    $orderDate = $order->created_at
        ? $order->created_at->format('d M Y, h:i A')
        : 'N/A';

    /*
    |--------------------------------------------------------------------------
    | PAYMENT DATE
    |--------------------------------------------------------------------------
    */

    $paidDate = null;

    if (!empty($order->paid_at)) {
        try {
            $paidDate = \Carbon\Carbon::parse(
                $order->paid_at
            )->format('d M Y, h:i A');
        } catch (\Throwable $e) {
            $paidDate = null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT METHOD
    |--------------------------------------------------------------------------
    */

    $paymentMethod = $order->payment_method
        ?? 'Cash on Delivery';
@endphp


<link rel="stylesheet" href="{{ asset('css/pages/orders-show.css') }}?v=b3">


<div class="order-page">

    <div class="container">

        {{-- ============================================================
             HEADER
        ============================================================= --}}

        <div class="order-header no-print">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div class="order-header-left">

                    <div class="order-header-icon">

                        <i class="fa-solid fa-receipt"></i>

                    </div>

                    <div>

                        <div class="order-title">

                            Order #{{ $order->id }}

                        </div>

                        <div class="order-subtitle">

                            <i class="fa-regular fa-calendar me-1"></i>

                            Placed on {{ $orderDate }}

                        </div>

                    </div>

                </div>


                <div class="d-flex gap-2 flex-wrap">

                    @if($currentStatus === 'pending' && strtolower((string) $order->payment_status) !== 'paid' && (int) $order->user_id === (int) auth()->id())
                        <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Cancel this order? This cannot be undone.');">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger order-action-btn">
                                <i class="fa-solid fa-ban me-1"></i>
                                Cancel Order
                            </button>
                        </form>
                    @endif

                    <a
                        href="{{ route('orders.invoice', $order) }}"
                        class="btn btn-outline-primary order-action-btn"
                    >
                        <i class="fa-solid fa-file-pdf me-1"></i>
                        Invoice PDF
                    </a>

                    <form method="POST" action="{{ route('orders.reorder', $order) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-success order-action-btn">
                            <i class="fa-solid fa-rotate-right me-1"></i>
                            Order again
                        </button>
                    </form>

                    <button
                        type="button"
                        onclick="window.print()"
                        class="btn btn-print order-action-btn"
                    >

                        <i class="fa-solid fa-print me-1"></i>

                        Print Order

                    </button>


                    <a
                        href="{{ route('orders.index') }}"
                        class="btn btn-dark order-action-btn"
                    >

                        <i class="fa-solid fa-arrow-left me-1"></i>

                        My Orders

                    </a>

                </div>

            </div>

        </div>


        {{-- ============================================================
             SUCCESS MESSAGE
        ============================================================= --}}

        @if(session('success'))

            <div class="alert alert-success alert-dismissible fade show order-alert no-print">

                <div class="d-flex align-items-center">

                    <i class="fa-solid fa-circle-check fs-5 me-3"></i>

                    <div>

                        <strong>Order Successful</strong>

                        <div class="small mt-1">

                            {{ session('success') }}

                        </div>

                    </div>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        @endif


        {{-- ============================================================
             ERROR
        ============================================================= --}}

        @if(session('error'))

            <div class="alert alert-danger alert-dismissible fade show order-alert no-print">

                <div class="d-flex align-items-center">

                    <i class="fa-solid fa-circle-exclamation fs-5 me-3"></i>

                    <div>

                        <strong>Something went wrong</strong>

                        <div class="small mt-1">

                            {{ session('error') }}

                        </div>

                    </div>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        @endif


        {{-- ============================================================
             VALIDATION ERRORS
        ============================================================= --}}

        @if($errors->any())

            <div class="alert alert-danger order-alert no-print">

                <strong>

                    <i class="fa-solid fa-triangle-exclamation me-1"></i>

                    Please fix the following errors:

                </strong>

                <ul class="mb-0 mt-2">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- ============================================================
             PREMIUM ORDER HERO
        ============================================================= --}}

        <div class="order-hero mb-4">

            <div class="order-hero-content">

                <div class="row align-items-center g-4">

                    <div class="col-lg-7">

                        <div class="order-hero-label">

                            IBL CART 24

                        </div>

                        <div class="order-hero-number">

                            Order #{{ $order->id }}

                        </div>

                        <div class="order-hero-date">

                            Thank you for shopping with us.

                        </div>

                    </div>


                    <div class="col-lg-5 text-lg-end">

                        <div class="small opacity-75 mb-2">

                            Current Order Status

                        </div>

                        <span class="status-badge {{ $status['class'] }}">

                            <i class="fa-solid {{ $status['icon'] }}"></i>

                            {{ $status['label'] }}

                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- ============================================================
             TRACK ORDER
        ============================================================= --}}

        <div class="order-card mb-4">

            <div class="order-card-header">

                <h5 class="order-card-title">

                    <i class="fa-solid fa-location-crosshairs"></i>

                    Track Your Order

                </h5>

                <span class="small text-muted">

                    Order #{{ $order->id }}

                </span>

            </div>


            <div class="order-card-body">

                @if($currentStatus === 'cancelled')

                    <div class="cancelled-box">

                        <div class="cancelled-icon">

                            <i class="fa-solid fa-xmark"></i>

                        </div>

                        <div class="cancelled-title">

                            Order Cancelled

                        </div>

                        <p class="text-muted mb-0 mt-2">

                            {{ $status['description'] }}

                        </p>

                    </div>

                @else

                    {{-- CURRENT STATUS --}}

                    <div class="current-status mb-4">

                        <div>

                            <div class="current-status-label">

                                Current Status

                            </div>

                            <h4 class="current-status-title">

                                {{ $status['label'] }}

                            </h4>

                            <p class="current-status-description">

                                {{ $status['description'] }}

                            </p>

                        </div>


                        <div>

                            @if($currentStatus === 'delivered')

                                <span class="badge bg-success px-3 py-2">

                                    <i class="fa-solid fa-circle-check me-1"></i>

                                    Order Completed

                                </span>

                            @else

                                <span class="badge bg-primary px-3 py-2">

                                    <i class="fa-solid fa-truck-fast me-1"></i>

                                    In Progress

                                </span>

                            @endif

                        </div>

                    </div>


                    {{-- TIMELINE --}}

                    <div class="tracking-wrapper">

                        <div class="tracking-line"></div>

                        <div
                            class="tracking-progress"
                            style="width: {{ $progressPercent }}%;"
                        ></div>


                        <div class="tracking-steps">

                            {{-- PENDING --}}

                            <div
                                class="tracking-step
                                {{ $currentIndex > 0 ? 'completed' : '' }}
                                {{ $currentStatus === 'pending' ? 'active' : '' }}"
                            >

                                <div class="tracking-icon">

                                    @if($currentIndex > 0)

                                        <i class="fa-solid fa-check"></i>

                                    @else

                                        <i class="fa-solid fa-clock"></i>

                                    @endif

                                </div>

                                <div class="tracking-title">

                                    Pending

                                </div>

                                <div class="tracking-date">

                                    Order Received

                                </div>

                            </div>


                            {{-- CONFIRMED --}}

                            <div
                                class="tracking-step
                                {{ $currentIndex > 1 ? 'completed' : '' }}
                                {{ $currentStatus === 'confirmed' ? 'active' : '' }}"
                            >

                                <div class="tracking-icon">

                                    @if($currentIndex > 1)

                                        <i class="fa-solid fa-check"></i>

                                    @else

                                        <i class="fa-solid fa-clipboard-check"></i>

                                    @endif

                                </div>

                                <div class="tracking-title">

                                    Confirmed

                                </div>

                                <div class="tracking-date">

                                    Order Confirmed

                                </div>

                            </div>


                            {{-- PROCESSING --}}

                            <div
                                class="tracking-step
                                {{ $currentIndex > 2 ? 'completed' : '' }}
                                {{ $currentStatus === 'processing' ? 'active' : '' }}"
                            >

                                <div class="tracking-icon">

                                    @if($currentIndex > 2)

                                        <i class="fa-solid fa-check"></i>

                                    @else

                                        <i class="fa-solid fa-box-open"></i>

                                    @endif

                                </div>

                                <div class="tracking-title">

                                    Processing

                                </div>

                                <div class="tracking-date">

                                    Preparing Order

                                </div>

                            </div>


                            {{-- SHIPPED --}}

                            <div
                                class="tracking-step
                                {{ $currentIndex > 3 ? 'completed' : '' }}
                                {{ $currentStatus === 'shipped' ? 'active' : '' }}"
                            >

                                <div class="tracking-icon">

                                    @if($currentIndex > 3)

                                        <i class="fa-solid fa-check"></i>

                                    @else

                                        <i class="fa-solid fa-truck-fast"></i>

                                    @endif

                                </div>

                                <div class="tracking-title">

                                    Shipped

                                </div>

                                <div class="tracking-date">

                                    On The Way

                                </div>

                            </div>


                            {{-- DELIVERED --}}

                            <div
                                class="tracking-step
                                {{ $currentStatus === 'delivered' ? 'completed active' : '' }}"
                            >

                                <div class="tracking-icon">

                                    @if($currentStatus === 'delivered')

                                        <i class="fa-solid fa-check"></i>

                                    @else

                                        <i class="fa-solid fa-house"></i>

                                    @endif

                                </div>

                                <div class="tracking-title">

                                    Delivered

                                </div>

                                <div class="tracking-date">

                                    Order Completed

                                </div>

                            </div>

                        </div>

                    </div>

                @endif

            </div>

        </div>


        {{-- ============================================================
             CUSTOMER + PAYMENT
        ============================================================= --}}

        <div class="row g-4">


            {{-- CUSTOMER INFORMATION --}}

            <div class="col-lg-5">

                <div class="order-card h-100">

                    <div class="order-card-header">

                        <h5 class="order-card-title">

                            <i class="fa-solid fa-user"></i>

                            Customer Information

                        </h5>

                    </div>


                    <div class="order-card-body">

                        <div class="info-list">


                            {{-- NAME --}}

                            <div class="info-item">

                                <div class="info-icon">

                                    <i class="fa-solid fa-user"></i>

                                </div>

                                <div>

                                    <span class="info-label">

                                        Customer Name

                                    </span>

                                    <div class="info-value">

                                        {{ $order->customer_name ?? 'N/A' }}

                                    </div>

                                </div>

                            </div>


                            {{-- PHONE --}}

                            <div class="info-item">

                                <div class="info-icon">

                                    <i class="fa-solid fa-phone"></i>

                                </div>

                                <div>

                                    <span class="info-label">

                                        Phone Number

                                    </span>

                                    <div class="info-value">

                                        {{ $order->phone ?? 'N/A' }}

                                    </div>

                                </div>

                            </div>


                            {{-- ADDRESS --}}

                            <div class="info-item">

                                <div class="info-icon">

                                    <i class="fa-solid fa-location-dot"></i>

                                </div>

                                <div>

                                    <span class="info-label">

                                        Delivery Address

                                    </span>

                                    <div
                                        class="info-value"
                                        style="white-space: pre-line;"
                                    >

                                        {{ $order->address ?? 'N/A' }}

                                    </div>

                                </div>

                            </div>


                            {{-- ORDER DATE --}}

                            <div class="info-item">

                                <div class="info-icon">

                                    <i class="fa-regular fa-calendar"></i>

                                </div>

                                <div>

                                    <span class="info-label">

                                        Order Date

                                    </span>

                                    <div class="info-value">

                                        {{ $orderDate }}

                                    </div>

                                </div>

                            </div>


                            {{-- ORDER ID --}}

                            <div class="info-item">

                                <div class="info-icon">

                                    <i class="fa-solid fa-hashtag"></i>

                                </div>

                                <div>

                                    <span class="info-label">

                                        Order ID

                                    </span>

                                    <div class="info-value">

                                        #{{ $order->id }}

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- PAYMENT INFORMATION --}}

            <div class="col-lg-7">

                <div class="order-card h-100">

                    <div class="order-card-header">

                        <h5 class="order-card-title">

                            <i class="fa-solid fa-credit-card"></i>

                            Payment & Order Information

                        </h5>

                    </div>


                    <div class="order-card-body">


                        {{-- STATUS --}}

                        <div class="mb-4">

                            <span class="info-label">

                                Current Order Status

                            </span>

                            <div class="mt-2">

                                <span class="status-badge {{ $status['class'] }}">

                                    <i class="fa-solid {{ $status['icon'] }}"></i>

                                    {{ $status['label'] }}

                                </span>

                            </div>

                        </div>


                        <div class="payment-grid">


                            {{-- PAYMENT METHOD --}}

                            <div class="payment-item">

                                <span class="payment-label">

                                    Payment Method

                                </span>

                                <div class="payment-value">

                                    <i class="fa-solid fa-wallet me-1 text-muted"></i>

                                    {{ $paymentMethod }}

                                </div>

                            </div>


                            {{-- PAYMENT STATUS --}}

                            <div class="payment-item">

                                <span class="payment-label">

                                    Payment Status

                                </span>

                                <div>

                                    <span class="payment-badge {{ $payment['class'] }}">

                                        <i class="fa-solid {{ $payment['icon'] }}"></i>

                                        {{ $payment['label'] }}

                                    </span>

                                </div>

                            </div>


                            {{-- TRANSACTION ID --}}

                            @if(!empty($order->payment_transaction_id))

                                <div class="payment-item">

                                    <span class="payment-label">

                                        Transaction ID

                                    </span>

                                    <div class="payment-value">

                                        {{ $order->payment_transaction_id }}

                                    </div>

                                </div>

                            @endif


                            {{-- PAYMENT GATEWAY --}}

                            @if(!empty($order->payment_gateway))

                                <div class="payment-item">

                                    <span class="payment-label">

                                        Payment Gateway

                                    </span>

                                    <div class="payment-value">

                                        {{ $order->payment_gateway }}

                                    </div>

                                </div>

                            @endif


                            {{-- PAID DATE --}}

                            @if($paidDate)

                                <div class="payment-item">

                                    <span class="payment-label">

                                        Paid At

                                    </span>

                                    <div class="payment-value">

                                        <i class="fa-regular fa-clock me-1 text-muted"></i>

                                        {{ $paidDate }}

                                    </div>

                                </div>

                            @endif


                            {{-- ORDER TOTAL --}}

                            <div class="payment-item">

                                <span class="payment-label">

                                    Order Total

                                </span>

                                <div class="payment-value text-success">

                                    ৳{{ number_format($totalAmount, 2) }}

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ============================================================
             ORDERED PRODUCTS
        ============================================================= --}}

        <div class="order-card mt-4">

            <div class="order-card-header">

                <h5 class="order-card-title">

                    <i class="fa-solid fa-bag-shopping"></i>

                    Ordered Products

                </h5>

                <span class="small text-muted">

                    {{ $order->items->count() }}

                    {{ $order->items->count() === 1 ? 'Item' : 'Items' }}

                </span>

            </div>


            <div class="table-responsive">

                <table class="table product-table align-middle mb-0">

                    <thead>

                        <tr>

                            <th>

                                Product

                            </th>

                            <th>

                                Price

                            </th>

                            <th>

                                Quantity

                            </th>

                            <th>

                                Subtotal

                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @forelse($order->items as $item)

                        @php

                            $productName = $item->product_name
                                ?? ($item->product->name ?? 'Product');

                            $productImage = $item->product->image
                                ?? null;

                            $itemPrice = (float) (
                                $item->price
                                ?? 0
                            );

                            $itemQuantity = (int) (
                                $item->quantity
                                ?? 1
                            );

                            $itemSubtotal = (float) (
                                $item->subtotal
                                ?? ($itemPrice * $itemQuantity)
                            );

                        @endphp


                        <tr class="product-row">

                            {{-- PRODUCT --}}

                            <td>

                                <div class="d-flex align-items-center">

                                    <div class="product-image-wrapper me-3">

                                        @if($productImage)

                                            <img
                                                src="{{ asset('uploads/products/' . $productImage) }}"
                                                alt="{{ $productName }}"
                                                class="product-image"
                                                loading="lazy"
                                            >

                                        @else

                                            <div class="product-placeholder">

                                                <i class="fa-solid fa-image"></i>

                                            </div>

                                        @endif

                                    </div>


                                    <div>

                                        <div class="product-name">

                                            {{ $productName }}

                                        </div>

                                        <div class="product-id">

                                            Product ID:

                                            #{{ $item->product_id }}

                                        </div>

                                    </div>

                                </div>

                            </td>


                            {{-- PRICE --}}

                            <td>

                                <span class="price-text">

                                    ৳{{ number_format($itemPrice, 2) }}

                                </span>

                            </td>


                            {{-- QUANTITY --}}

                            <td>

                                <span class="quantity-badge">

                                    {{ $itemQuantity }}

                                </span>

                            </td>


                            {{-- SUBTOTAL --}}

                            <td>

                                <span class="subtotal-text">

                                    ৳{{ number_format($itemSubtotal, 2) }}

                                </span>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="4">

                                <div class="empty-products">

                                    <div class="empty-products-icon">

                                        <i class="fa-solid fa-box-open"></i>

                                    </div>

                                    <h5 class="fw-bold">

                                        No Products Found

                                    </h5>

                                    <p class="text-muted mb-0">

                                        There are no products associated with this order.

                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ========================================================
                 ORDER SUMMARY
            ========================================================= --}}

            <div class="p-4 border-top">

                <div class="summary-box">

                    {{-- SUBTOTAL --}}

                    <div class="summary-row">

                        <span>

                            Subtotal

                        </span>

                        <strong>

                            ৳{{ number_format($subtotal, 2) }}

                        </strong>

                    </div>


                    {{-- DELIVERY --}}

                    <div class="summary-row">

                        <span>

                            Delivery Charge

                        </span>

                        <strong>

                            ৳{{ number_format($deliveryCharge, 2) }}

                        </strong>

                    </div>


                    <hr class="summary-divider">


                    {{-- TOTAL --}}

                    <div class="d-flex align-items-center justify-content-between">

                        <span class="summary-total-label">

                            Total

                        </span>

                        <span class="summary-total">

                            ৳{{ number_format($totalAmount, 2) }}

                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- ============================================================
             ORDER NOTE
        ============================================================= --}}

        @if(!empty($order->note))

            <div class="order-card mt-4">

                <div class="order-card-header">

                    <h5 class="order-card-title">

                        <i class="fa-solid fa-note-sticky"></i>

                        Order Note

                    </h5>

                </div>


                <div class="order-card-body">

                    <div class="order-note">

                        {{ $order->note }}

                    </div>

                </div>

            </div>

        @endif


        {{-- ============================================================
             BOTTOM ACTIONS
        ============================================================= --}}

        <div class="order-bottom-actions no-print">

            <a
                href="{{ route('orders.index') }}"
                class="btn btn-dark bottom-btn"
            >

                <i class="fa-solid fa-list me-1"></i>

                View All Orders

            </a>


            <a
                href="{{ route('home') }}"
                class="btn btn-outline-dark bottom-btn"
            >

                <i class="fa-solid fa-bag-shopping me-1"></i>

                Continue Shopping

            </a>

        </div>


        {{-- ============================================================
             BRAND FOOTER
        ============================================================= --}}

        <div class="text-center mt-5 text-muted no-print">

            <div class="fw-bold text-dark">

                IBL CART 24

            </div>

            <div class="small mt-1">

                Shop Smart. Shop Premium.

            </div>

        </div>

    </div>

</div>

@endsection

@if(session('track_purchase'))
@push('scripts')
<script>
    window.iblTrack && window.iblTrack('Purchase', {value:{{ (float) ($order->total ?? $order->total_amount ?? 0) }}, currency:'BDT', transaction_id:@json((string) $order->id), contents:[], content_type:'product'});
</script>
@endpush
@endif