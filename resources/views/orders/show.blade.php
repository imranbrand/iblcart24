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

    <title>
        Order #{{ $order->id }} - IBL CART 24
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

    <link rel="stylesheet" href="{{ asset('css/pages/admin-orders-show.css') }}?v=b3">

</head>


<body>


<!-- =========================================================
     NAVBAR
========================================================= -->

<nav class="navbar navbar-expand-lg bg-white shadow-sm no-print">

    <div class="container-fluid px-4">

        <div class="d-flex align-items-center">

            @if($isAdmin ?? false)

                <a
                    class="navbar-brand text-dark text-decoration-none d-flex align-items-center gap-2"
                    href="{{ route('admin.dashboard') }}"
                >

                    <span class="brand-icon">

                        <i class="fa-solid fa-cart-shopping"></i>

                    </span>

                    <span>
                        IBL CART 24
                    </span>

                </a>

            @else

                <a
                    class="navbar-brand text-dark text-decoration-none d-flex align-items-center gap-2"
                    href="{{ route('home') }}"
                >

                    <span class="brand-icon">

                        <i class="fa-solid fa-cart-shopping"></i>

                    </span>

                    <span>
                        IBL CART 24
                    </span>

                </a>

            @endif

        </div>


        <div class="ms-auto d-flex flex-wrap gap-2">

            @if($isAdmin ?? false)

                <a
                    href="{{ route('orders.invoice', $order) }}"
                    class="btn btn-outline-secondary btn-sm"
                >
                    <i class="fa-solid fa-file-pdf me-1"></i>
                    Invoice PDF
                </a>


                <a
                    href="{{ route('admin.dashboard') }}"
                    class="btn btn-outline-dark btn-sm"
                >

                    <i class="fa-solid fa-chart-line me-1"></i>

                    Dashboard

                </a>

                <a
                    href="{{ route('admin.orders.index') }}"
                    class="btn btn-primary btn-sm"
                >

                    <i class="fa-solid fa-arrow-left me-1"></i>

                    Back to Orders

                </a>

            @else

                <a
                    href="{{ route('orders.index') }}"
                    class="btn btn-outline-primary btn-sm"
                >

                    <i class="fa-solid fa-receipt me-1"></i>

                    My Orders

                </a>

                <a
                    href="{{ route('products.index') }}"
                    class="btn btn-outline-dark btn-sm"
                >

                    <i class="fa-solid fa-shop me-1"></i>

                    Products

                </a>

            @endif

        </div>

    </div>

</nav>



<!-- =========================================================
     MAIN
========================================================= -->

<main class="page-container container-fluid py-4 px-3 px-lg-4">


    <!-- FLASH SUCCESS -->

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show shadow-sm no-print mb-4">

            <i class="fa-solid fa-circle-check me-2"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    <!-- FLASH ERROR -->

    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show shadow-sm no-print mb-4">

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

        <div class="alert alert-danger shadow-sm no-print mb-4">

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

        </div>

    @endif



    <!-- =====================================================
         PRINT HEADER
    ====================================================== -->

    <div class="print-only mb-4">

        <h2 class="fw-bold">
            IBL CART 24
        </h2>

        <h4>
            Order #{{ $order->id }}
        </h4>

    </div>



    <!-- =====================================================
         ORDER HERO
    ====================================================== -->

    <div class="card card-box hero-card mb-4">

        <div class="card-body p-4 p-lg-5 hero-content">

            <div class="row align-items-center g-4">

                <div class="col-lg-7">

                    <div class="eyebrow mb-2">
                        Order Details
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-3">

                        <h1 class="order-number mb-0">
                            Order #{{ $order->id }}
                        </h1>

                        @php

                            $status = strtolower(
                                trim($order->status ?? 'pending')
                            );

                        @endphp


                        @if($status === 'pending')

                            <span class="status-badge status-pending">

                                <i class="fa-solid fa-clock"></i>

                                Pending

                            </span>

                        @elseif($status === 'confirmed')

                            <span class="status-badge status-confirmed">

                                <i class="fa-solid fa-circle-check"></i>

                                Confirmed

                            </span>

                        @elseif($status === 'processing')

                            <span class="status-badge status-processing">

                                <i class="fa-solid fa-gears"></i>

                                Processing

                            </span>

                        @elseif($status === 'shipped')

                            <span class="status-badge status-shipped">

                                <i class="fa-solid fa-truck"></i>

                                Shipped

                            </span>

                        @elseif($status === 'delivered')

                            <span class="status-badge status-delivered">

                                <i class="fa-solid fa-box-open"></i>

                                Delivered

                            </span>

                        @elseif($status === 'cancelled')

                            <span class="status-badge status-cancelled">

                                <i class="fa-solid fa-circle-xmark"></i>

                                Cancelled

                            </span>

                        @else

                            <span class="status-badge status-default">

                                {{ ucfirst($status) }}

                            </span>

                        @endif

                    </div>


                    <div class="order-date mt-2">

                        <i class="fa-regular fa-calendar me-1"></i>

                        @if($order->created_at)

                            {{ $order->created_at->format('d M Y, h:i A') }}

                        @else

                            N/A

                        @endif

                    </div>

                </div>


                <div class="col-lg-5">

                    <div class="d-flex flex-wrap justify-content-lg-end gap-2 no-print">

                        <button
                            type="button"
                            class="btn btn-dark"
                            onclick="window.print();"
                        >

                            <i class="fa-solid fa-print me-1"></i>

                            Print Order

                        </button>


                        @if($isAdmin ?? false)

                            <a
                                href="{{ route('admin.orders.index') }}"
                                class="btn btn-outline-primary"
                            >

                                <i class="fa-solid fa-arrow-left me-1"></i>

                                Orders

                            </a>

                        @else

                            <a
                                href="{{ route('orders.index') }}"
                                class="btn btn-outline-primary"
                            >

                                <i class="fa-solid fa-arrow-left me-1"></i>

                                My Orders

                            </a>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>



        <!-- =====================================================
         ORDER PROGRESS (icon-driven, admin can act from here)
    ====================================================== -->

    <style>
        .ibl-flow{display:flex;align-items:flex-start;overflow-x:auto;padding:8px 4px 4px;gap:0}
        .ibl-step{flex:1 1 0;min-width:88px;text-align:center;position:relative}
        .ibl-step::before{content:"";position:absolute;top:25px;left:-50%;width:100%;height:4px;background:#e5e7eb;z-index:0}
        .ibl-step:first-child::before{display:none}
        .ibl-step.done::before,.ibl-step.current::before{background:#198754}
        .ibl-step form{margin:0}
        .ibl-dot{position:relative;z-index:1;width:54px;height:54px;border-radius:50%;border:3px solid #e5e7eb;background:#fff;color:#9ca3af;display:inline-flex;align-items:center;justify-content:center;font-size:20px;transition:all .2s}
        button.ibl-dot{cursor:pointer;padding:0}
        .ibl-step.done .ibl-dot{background:#198754;border-color:#198754;color:#fff}
        .ibl-step.current .ibl-dot{background:#0d6efd;border-color:#0d6efd;color:#fff;box-shadow:0 0 0 6px rgba(13,110,253,.15)}
        .ibl-step.next .ibl-dot{border-color:#0d6efd;color:#0d6efd;border-style:dashed}
        .ibl-step.next button.ibl-dot:hover{background:#0d6efd;color:#fff;transform:scale(1.08)}
        .ibl-label{margin-top:8px;font-size:13px;font-weight:600;color:#6b7280}
        .ibl-step.done .ibl-label,.ibl-step.current .ibl-label{color:#111827}
        .ibl-hint{font-size:11px;color:#0d6efd;font-weight:600}
        .ibl-actions{display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between;margin-top:22px;padding-top:18px;border-top:1px solid #eef0f3}
    </style>

    @if($status !== 'cancelled')

        @php
            $flow = [
                ['key' => 'pending',    'label' => 'Pending',    'icon' => 'fa-clock',        'action' => null],
                ['key' => 'confirmed',  'label' => 'Confirmed',  'icon' => 'fa-circle-check', 'action' => 'Confirm Order'],
                ['key' => 'processing', 'label' => 'Processing', 'icon' => 'fa-gears',        'action' => 'Start Processing'],
                ['key' => 'shipped',    'label' => 'Shipped',    'icon' => 'fa-truck',        'action' => 'Mark as Shipped'],
                ['key' => 'delivered',  'label' => 'Delivered',  'icon' => 'fa-box-open',     'action' => 'Mark as Delivered'],
            ];

            $currentStep = collect($flow)->search(fn ($s) => $s['key'] === $status);
            $currentStep = $currentStep === false ? 0 : $currentStep;
            $nextStep = $flow[$currentStep + 1] ?? null;
            $adminView = (bool) ($isAdmin ?? false);
        @endphp

        <div class="card card-box mb-4">

            <div class="section-header">
                <div class="d-flex align-items-center">
                    <span class="icon-heading"><i class="fa-solid fa-route"></i></span>
                    <div>
                        <h5 class="section-title">Order Progress</h5>
                        <div class="section-subtitle">
                            {{ $adminView ? 'Click the highlighted icon to move this order to the next stage' : 'Current order fulfillment stage' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-body">

                <div class="ibl-flow">

                    @foreach($flow as $i => $step)

                        @php
                            $state = $i < $currentStep ? 'done'
                                   : ($i === $currentStep ? 'current'
                                   : ($i === $currentStep + 1 ? 'next' : 'locked'));
                        @endphp

                        <div class="ibl-step {{ $state }}">

                            @if($adminView && $state === 'next')

                                <form
                                    action="{{ route('admin.orders.status.update', $order) }}"
                                    method="POST"
                                    onsubmit="return confirm('{{ $step['action'] }} for Order #{{ $order->id }}?');"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $step['key'] }}">
                                    <button type="submit" class="ibl-dot" title="{{ $step['action'] }}">
                                        <i class="fa-solid {{ $step['icon'] }}"></i>
                                    </button>
                                </form>

                            @else

                                <span class="ibl-dot">
                                    <i class="fa-solid {{ $state === 'done' ? 'fa-check' : $step['icon'] }}"></i>
                                </span>

                            @endif

                            <div class="ibl-label">{{ $step['label'] }}</div>

                            @if($adminView && $state === 'next')
                                <div class="ibl-hint">Click to {{ strtolower($step['action']) }}</div>
                            @endif

                        </div>

                    @endforeach

                </div>


                @if($adminView)

                    <div class="ibl-actions no-print">

                        @if($nextStep)

                            <form
                                action="{{ route('admin.orders.status.update', $order) }}"
                                method="POST"
                                onsubmit="return confirm('{{ $nextStep['action'] }} for Order #{{ $order->id }}?');"
                            >
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $nextStep['key'] }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid {{ $nextStep['icon'] }} me-1"></i>
                                    {{ $nextStep['action'] }}
                                </button>
                            </form>

                        @else

                            <span class="text-success fw-semibold">
                                <i class="fa-solid fa-circle-check me-1"></i>
                                Order completed
                            </span>

                        @endif


                        @if($status !== 'delivered')

                            <form
                                action="{{ route('admin.orders.status.update', $order) }}"
                                method="POST"
                                onsubmit="return confirm('Cancel Order #{{ $order->id }}? This cannot be undone easily.');"
                            >
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fa-solid fa-circle-xmark me-1"></i>
                                    Cancel Order
                                </button>
                            </form>

                        @endif

                    </div>

                @endif

            </div>

        </div>

    @else

        <div class="card card-box mb-4">
            <div class="section-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="action-icon danger" style="width:48px;height:48px;">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <div>
                        <div class="fw-bold">Order Cancelled</div>
                        <div class="small text-muted">
                            This order has been cancelled and is no longer active.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endif



    <!-- =====================================================
         CUSTOMER / PAYMENT / TOTAL
    ====================================================== -->

    <div class="row g-4">


        <!-- CUSTOMER -->

        <div class="col-xl-4 col-lg-6">

            <div class="card card-box section-card">

                <div class="section-header">

                    <div class="d-flex align-items-center">

                        <span class="icon-heading">

                            <i class="fa-solid fa-user"></i>

                        </span>

                        <div>

                            <h5 class="section-title">
                                Customer Information
                            </h5>

                            <div class="section-subtitle">
                                Delivery recipient details
                            </div>

                        </div>

                    </div>

                </div>


                <div class="section-body">

                    <div class="info-item">

                        <div class="info-label">
                            Customer Name
                        </div>

                        <div class="info-value">

                            {{ $order->customer_name ?: 'Guest' }}

                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            Phone Number
                        </div>

                        <div class="info-value">

                            @if($order->phone)

                                <a
                                    href="tel:{{ $order->phone }}"
                                    class="phone-link"
                                >

                                    <i class="fa-solid fa-phone me-1"></i>

                                    {{ $order->phone }}

                                </a>

                            @else

                                N/A

                            @endif

                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            Delivery Address
                        </div>

                        <div class="info-value">

                            <i class="fa-solid fa-location-dot text-danger me-1"></i>

                            {!! nl2br(e($order->address ?: 'N/A')) !!}

                        </div>

                    </div>


                    @if($isAdmin ?? false)

                        <div class="info-item">

                            <div class="info-label">
                                User ID
                            </div>

                            <div class="info-value">

                                <span class="badge bg-light text-dark border">

                                    #{{ $order->user_id ?? 'Guest' }}

                                </span>

                            </div>

                        </div>

                    @endif

                </div>

            </div>

        </div>



        <!-- PAYMENT -->

        <div class="col-xl-4 col-lg-6">

            <div class="card card-box section-card">

                <div class="section-header">

                    <div class="d-flex align-items-center">

                        <span class="icon-heading success">

                            <i class="fa-solid fa-credit-card"></i>

                        </span>

                        <div>

                            <h5 class="section-title">
                                Payment Information
                            </h5>

                            <div class="section-subtitle">
                                Payment and transaction details
                            </div>

                        </div>

                    </div>

                </div>


                <div class="section-body">

                    <div class="info-item">

                        <div class="info-label">
                            Payment Method
                        </div>

                        <div class="payment-method-box">

                            <i class="fa-solid fa-wallet text-success"></i>

                            {{ $order->payment_method ?: 'Cash on Delivery' }}

                        </div>

                    </div>


                    @php

                        $paymentStatus = strtolower(
                            trim($order->payment_status ?? 'pending')
                        );

                    @endphp


                    <div class="info-item">

                        <div class="info-label">
                            Payment Status
                        </div>


                        @if($paymentStatus === 'paid')

                            <span class="status-badge bg-success text-white">

                                <i class="fa-solid fa-circle-check"></i>

                                Paid

                            </span>

                        @elseif($paymentStatus === 'failed')

                            <span class="status-badge bg-danger text-white">

                                <i class="fa-solid fa-circle-xmark"></i>

                                Failed

                            </span>

                        @elseif($paymentStatus === 'refunded')

                            <span class="status-badge bg-secondary text-white">

                                <i class="fa-solid fa-rotate-left"></i>

                                Refunded

                            </span>

                        @else

                            <span class="status-badge status-pending">

                                <i class="fa-solid fa-clock"></i>

                                Pending

                            </span>

                        @endif

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            Subtotal
                        </div>

                        <div class="info-value">

                            ৳{{ number_format(
                                (float) ($order->subtotal ?? 0),
                                2
                            ) }}

                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            Delivery Charge
                        </div>

                        <div class="info-value">

                            ৳{{ number_format(
                                (float) ($order->delivery_charge ?? 0),
                                2
                            ) }}

                        </div>

                    </div>


                    @if($order->payment_transaction_id)

                        <div class="info-item">

                            <div class="info-label">
                                Transaction ID
                            </div>

                            <div class="transaction-box">

                                <code>
                                    {{ $order->payment_transaction_id }}
                                </code>

                            </div>

                            @if(auth()->user()?->isAdmin() && in_array($order->payment_method, ['bKash', 'Nagad'], true) && strtolower((string) $order->payment_status) !== 'paid' && !$order->isCancelled())
                                <form
                                    method="POST"
                                    action="{{ route('admin.orders.payment.verify', $order) }}"
                                    class="mt-2"
                                    onsubmit="return confirm('Did you check this Transaction ID in your {{ $order->payment_method }} account and confirm the amount was received?');"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-circle-check me-1"></i>
                                        Verify payment &amp; mark Paid
                                    </button>
                                </form>
                            @endif

                        </div>

                    @endif

                    @if($order->paid_at)

                        <div class="info-item">

                            <div class="info-label">
                                Paid At
                            </div>

                            <div class="info-value">

                                <i class="fa-regular fa-clock text-success me-1"></i>

                                {{ $order->paid_at->format('d M Y, h:i A') }}

                            </div>

                        </div>

                    @endif

                </div>

            </div>

        </div>



        <!-- TOTAL -->

        <div class="col-xl-4 col-lg-12">

            @php

                $orderTotal =
                    $order->total_amount
                    ?? $order->total
                    ?? 0;

            @endphp


            <div class="card total-card">

                <div class="card-body p-4 p-lg-5 total-content">

                    <div class="total-label mb-2">
                        Order Total
                    </div>

                    <div class="total-price mb-4">

                        ৳{{ number_format(
                            (float) $orderTotal,
                            2
                        ) }}

                    </div>


                    <div class="total-line">

                        <span>
                            Subtotal
                        </span>

                        <strong>

                            ৳{{ number_format(
                                (float) ($order->subtotal ?? 0),
                                2
                            ) }}

                        </strong>

                    </div>


                    <div class="total-line">

                        <span>
                            Delivery Charge
                        </span>

                        <strong>

                            ৳{{ number_format(
                                (float) ($order->delivery_charge ?? 0),
                                2
                            ) }}

                        </strong>

                    </div>


                    <hr class="border-secondary opacity-50 my-4">


                    <div class="total-line total-grand mb-0">

                        <span class="text-white">
                            Grand Total
                        </span>

                        <strong class="text-white">

                            ৳{{ number_format(
                                (float) $orderTotal,
                                2
                            ) }}

                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <!-- =====================================================
         ORDER ITEMS
    ====================================================== -->

    <div class="card card-box mt-4">

        <div class="section-header">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div class="d-flex align-items-center">

                    <span class="icon-heading">

                        <i class="fa-solid fa-box-open"></i>

                    </span>

                    <div>

                        <h5 class="section-title">
                            Order Items
                        </h5>

                        <div class="section-subtitle">
                            Products included in this order
                        </div>

                    </div>

                </div>


                <span class="badge bg-light text-dark border px-3 py-2">

                    {{ $order->items?->count() ?? 0 }}

                    {{ ($order->items?->count() ?? 0) === 1 ? 'Item' : 'Items' }}

                </span>

            </div>

        </div>


        <div class="table-responsive">

            <table class="table product-table mb-0">

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

                    @forelse($order->items ?? collect() as $item)

                        @php

                            $product = $item->product ?? null;

                            $image = $product->image ?? null;

                            $itemSubtotal =
                                $item->subtotal
                                ??
                                (
                                    ($item->price ?? 0)
                                    *
                                    ($item->quantity ?? 0)
                                );

                        @endphp


                        <tr>

                            <td>

                                <div class="d-flex align-items-center gap-3">

                                    @if($image)

                                        @php

                                            if (
                                                str_starts_with(
                                                    $image,
                                                    'uploads/'
                                                )
                                                ||
                                                str_starts_with(
                                                    $image,
                                                    'products/'
                                                )
                                            ) {

                                                $imageUrl =
                                                    asset($image);

                                            } else {

                                                $imageUrl =
                                                    asset(
                                                        'storage/' .
                                                        ltrim(
                                                            $image,
                                                            '/'
                                                        )
                                                    );

                                            }

                                        @endphp


                                        <img
                                            src="{{ $imageUrl }}"
                                            alt="{{ $product->name ?? 'Product' }}"
                                            class="product-image"
                                            loading="lazy"
                                            onerror="this.style.display='none';"
                                        >

                                    @else

                                        <div class="product-placeholder">

                                            <i class="fa-solid fa-box"></i>

                                        </div>

                                    @endif


                                    <div>

                                        <div class="product-name">

                                            {{ $item->product_name
                                                ?? optional($item->product)->name
                                                ?? 'Product'
                                            }}

                                        </div>


                                        @if($product)

                                            <div class="product-id">

                                                Product #{{ $product->id }}

                                            </div>

                                        @endif

                                    </div>

                                </div>

                            </td>


                            <td>

                                <span class="price-text">

                                    ৳{{ number_format(
                                        (float) ($item->price ?? 0),
                                        2
                                    ) }}

                                </span>

                            </td>


                            <td>

                                <span class="quantity-badge">

                                    {{ $item->quantity ?? 0 }}

                                </span>

                            </td>


                            <td>

                                <span class="subtotal-text">

                                    ৳{{ number_format(
                                        (float) $itemSubtotal,
                                        2
                                    ) }}

                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4">

                                <div class="empty-state">

                                    <div class="empty-state-icon">

                                        <i class="fa-solid fa-box-open"></i>

                                    </div>

                                    <h6 class="fw-bold">
                                        No Order Items Found
                                    </h6>

                                    <p class="text-muted mb-0 small">

                                        This order does not contain any item records.

                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>



    <!-- =====================================================
         CUSTOMER NOTE
    ====================================================== -->

    @if(!empty($order->note))

        <div class="card card-box mt-4">

            <div class="section-body">

                <div class="note-box">

                    <div class="d-flex gap-3">

                        <div class="note-icon">

                            <i class="fa-solid fa-note-sticky"></i>

                        </div>

                        <div>

                            <div class="fw-bold mb-1">
                                Customer Note
                            </div>

                            <div class="text-muted small">

                                {{ $order->note }}

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    @endif



    <!-- =====================================================
         ORDER INFORMATION
    ====================================================== -->

    <div class="card card-box mt-4">

        <div class="section-header">

            <div class="d-flex align-items-center">

                <span class="icon-heading">

                    <i class="fa-solid fa-circle-info"></i>

                </span>

                <div>

                    <h5 class="section-title">
                        Order Information
                    </h5>

                    <div class="section-subtitle">
                        Complete order metadata
                    </div>

                </div>

            </div>

        </div>


        <div class="section-body">

            <div class="row g-3">


                <div class="col-lg-3 col-md-6">

                    <div class="meta-box">

                        <div class="info-label">
                            Order Number
                        </div>

                        <div class="meta-value">

                            #{{ $order->id }}

                        </div>

                    </div>

                </div>


                <div class="col-lg-3 col-md-6">

                    <div class="meta-box">

                        <div class="info-label">
                            Order Date
                        </div>

                        <div class="meta-value">

                            {{ optional($order->created_at)->format('d M Y') ?? 'N/A' }}

                        </div>

                    </div>

                </div>


                <div class="col-lg-3 col-md-6">

                    <div class="meta-box">

                        <div class="info-label">
                            Payment Status
                        </div>

                        <div class="meta-value">

                            {{ ucfirst($paymentStatus) }}

                        </div>

                    </div>

                </div>


                <div class="col-lg-3 col-md-6">

                    <div class="meta-box">

                        <div class="info-label">
                            Order Status
                        </div>

                        <div class="meta-value">

                            {{ ucfirst($status) }}

                        </div>

                    </div>

                </div>

            </div>


            @if($order->payment_gateway)

                <div class="row g-3 mt-1">

                    <div class="col-md-6">

                        <div class="meta-box">

                            <div class="info-label">
                                Payment Gateway
                            </div>

                            <div class="meta-value">

                                {{ $order->payment_gateway }}

                            </div>

                        </div>

                    </div>


                    @if($order->paid_at)

                        <div class="col-md-6">

                            <div class="meta-box">

                                <div class="info-label">
                                    Paid At
                                </div>

                                <div class="meta-value">

                                    {{ $order->paid_at->format('d M Y, h:i A') }}

                                </div>

                            </div>

                        </div>

                    @endif

                </div>

            @endif


            @if($order->updated_at)

                <div class="mt-4 pt-3 border-top">

                    <div class="small text-muted">

                        <i class="fa-regular fa-clock me-1"></i>

                        Last updated:

                        <strong>

                            {{ $order->updated_at->format('d M Y, h:i A') }}

                        </strong>

                    </div>

                </div>

            @endif

        </div>

    </div>



    <!-- =====================================================
         ADMIN ACTIONS
    ====================================================== -->

    @if($isAdmin ?? false)


        <!-- ORDER STATUS -->

        <div class="action-card mt-4 no-print">

            <div class="action-header">

                <div class="d-flex align-items-center gap-3">

                    <div class="action-icon">

                        <i class="fa-solid fa-arrows-rotate"></i>

                    </div>

                    <div>

                        <h5 class="fw-bold mb-1">
                            Update Order Status
                        </h5>

                        <div class="small text-muted">
                            Change the current fulfillment status of this order.
                        </div>

                    </div>

                </div>

            </div>


            <div class="action-body">

                <form
                    action="{{ route('admin.orders.status.update', $order) }}"
                    method="POST"
                >

                    @csrf

                    @method('PATCH')


                    <div class="row g-3 align-items-end">

                        <div class="col-lg-8">

                            <label
                                class="form-label fw-semibold"
                                for="status"
                            >

                                Order Status

                            </label>


                            <select
                                name="status"
                                id="status"
                                class="form-select"
                                required
                            >

                                <option
                                    value="pending"
                                    @selected($status === 'pending')
                                >
                                    Pending
                                </option>

                                <option
                                    value="confirmed"
                                    @selected($status === 'confirmed')
                                >
                                    Confirmed
                                </option>

                                <option
                                    value="processing"
                                    @selected($status === 'processing')
                                >
                                    Processing
                                </option>

                                <option
                                    value="shipped"
                                    @selected($status === 'shipped')
                                >
                                    Shipped
                                </option>

                                <option
                                    value="delivered"
                                    @selected($status === 'delivered')
                                >
                                    Delivered
                                </option>

                                <option
                                    value="cancelled"
                                    @selected($status === 'cancelled')
                                >
                                    Cancelled
                                </option>

                            </select>

                        </div>


                        <div class="col-lg-4">

                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >

                                <i class="fa-solid fa-floppy-disk me-1"></i>

                                Update Order Status

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>



        <!-- PAYMENT STATUS -->

        <div class="action-card mt-4 no-print">

            <div class="action-header">

                <div class="d-flex align-items-center gap-3">

                    <div class="action-icon success">

                        <i class="fa-solid fa-money-check-dollar"></i>

                    </div>

                    <div>

                        <h5 class="fw-bold mb-1">
                            Update Payment Status
                        </h5>

                        <div class="small text-muted">
                            Update the payment record for this order.
                        </div>

                    </div>

                </div>

            </div>


            <div class="action-body">

                <form
                    action="{{ route('admin.orders.payment.status.update', $order) }}"
                    method="POST"
                >

                    @csrf

                    @method('PATCH')


                    <div class="row g-3 align-items-end">

                        <div class="col-lg-8">

                            <label
                                class="form-label fw-semibold"
                                for="payment_status"
                            >

                                Payment Status

                            </label>


                            <select
                                name="payment_status"
                                id="payment_status"
                                class="form-select"
                                required
                            >

                                <option
                                    value="Pending"
                                    @selected($paymentStatus === 'pending')
                                >
                                    Pending
                                </option>

                                <option
                                    value="Paid"
                                    @selected($paymentStatus === 'paid')
                                >
                                    Paid
                                </option>

                                <option
                                    value="Failed"
                                    @selected($paymentStatus === 'failed')
                                >
                                    Failed
                                </option>

                                <option
                                    value="Refunded"
                                    @selected($paymentStatus === 'refunded')
                                >
                                    Refunded
                                </option>

                            </select>

                        </div>


                        <div class="col-lg-4">

                            <button
                                type="submit"
                                class="btn btn-success w-100"
                            >

                                <i class="fa-solid fa-check me-1"></i>

                                Update Payment Status

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>



        <!-- COURIER -->

        @include('admin.orders._courier')


        <!-- DELETE ORDER -->

        @php

            $canDeleteOrder =
                $status !== 'delivered'
                &&
                $paymentStatus !== 'paid';

        @endphp


        <div class="action-card mt-4 mb-4 no-print">

            <div class="action-body">

                <div class="danger-zone p-4">

                    <div class="row align-items-center g-4">

                        <div class="col-lg-8">

                            <div class="d-flex gap-3">

                                <div class="action-icon danger">

                                    <i class="fa-solid fa-trash"></i>

                                </div>

                                <div>

                                    <h5 class="fw-bold text-danger mb-2">

                                        Delete Order

                                    </h5>


                                    @if($canDeleteOrder)

                                        <p class="text-muted mb-0 small">

                                            This action permanently removes Order
                                            #{{ $order->id }} and its order items.

                                            @if($status !== 'cancelled')

                                                Stock reserved by this order will
                                                also be restored automatically.

                                            @else

                                                Stock will not be restored again
                                                because this order is already
                                                cancelled.

                                            @endif

                                        </p>

                                    @else

                                        <p class="text-muted mb-0 small">

                                            This order cannot be deleted because it is

                                            @if($status === 'delivered')

                                                already delivered.

                                            @elseif($paymentStatus === 'paid')

                                                marked as paid.

                                            @endif

                                        </p>

                                    @endif

                                </div>

                            </div>

                        </div>


                        <div class="col-lg-4">

                            @if($canDeleteOrder)

                                <form
                                    action="{{ route('admin.orders.destroy', $order) }}"
                                    method="POST"
                                    onsubmit="return confirmDeleteOrder();"
                                >

                                    @csrf

                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        class="btn btn-danger w-100 py-2"
                                    >

                                        <i class="fa-solid fa-trash me-1"></i>

                                        Delete Order

                                    </button>

                                </form>

                            @else

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary w-100 py-2"
                                    disabled
                                >

                                    <i class="fa-solid fa-lock me-1"></i>

                                    Delete Protected

                                </button>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>

    @endif



    <!-- =====================================================
         FOOTER ACTIONS
    ====================================================== -->

    <div class="footer-actions d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4 mb-5 no-print">

        <div>

            @if($isAdmin ?? false)

                <a
                    href="{{ route('admin.orders.index') }}"
                    class="btn btn-primary"
                >

                    <i class="fa-solid fa-arrow-left me-1"></i>

                    Back to Orders

                </a>

            @else

                <a
                    href="{{ route('orders.index') }}"
                    class="btn btn-primary"
                >

                    <i class="fa-solid fa-arrow-left me-1"></i>

                    Back to My Orders

                </a>

            @endif

        </div>


        <div class="d-flex gap-2">

            <button
                type="button"
                class="btn btn-outline-dark"
                onclick="window.print();"
            >

                <i class="fa-solid fa-print me-1"></i>

                Print

            </button>


            @if($isAdmin ?? false)

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="btn btn-outline-secondary"
                >

                    <i class="fa-solid fa-chart-line me-1"></i>

                    Dashboard

                </a>

            @else

                <a
                    href="{{ route('products.index') }}"
                    class="btn btn-outline-secondary"
                >

                    <i class="fa-solid fa-shop me-1"></i>

                    Continue Shopping

                </a>

            @endif

        </div>

    </div>


</main>



<!-- =========================================================
     DELETE CONFIRMATION
========================================================= -->

<script>

    function confirmDeleteOrder() {

        return confirm(
            "Are you sure you want to permanently delete Order #" +
            "{{ $order->id }}" +
            "?\n\n" +
            "This action cannot be undone."
        );

    }

</script>



<!-- =========================================================
     BOOTSTRAP JS
========================================================= -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>