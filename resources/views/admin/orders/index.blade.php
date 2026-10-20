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
        Order Management - IBL CART 24
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >


    <link rel="stylesheet" href="{{ asset('css/pages/admin-orders-index.css') }}?v=b3">

</head>


<body>


<!-- =========================================================
     NAVBAR
========================================================= -->

<nav class="top-navbar">

    <div class="container-fluid px-3 px-lg-4">

        <div
            class="d-flex align-items-center justify-content-between"
            style="min-height:72px;"
        >

            <a
                href="{{ route('admin.dashboard') }}"
                class="brand"
            >

                <span class="brand-icon">

                    <i class="fa-solid fa-cart-shopping"></i>

                </span>

                <span>
                    IBL CART 24
                </span>

            </a>


            <div class="d-flex align-items-center gap-2">

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="btn btn-outline-dark btn-sm"
                >

                    <i class="fa-solid fa-chart-line me-1"></i>

                    Dashboard

                </a>


                <a
                    href="{{ route('home') }}"
                    class="btn btn-primary btn-sm"
                    target="_blank"
                >

                    <i class="fa-solid fa-store me-1"></i>

                    View Store

                </a>

            </div>

        </div>

    </div>

</nav>



<!-- =========================================================
     MAIN
========================================================= -->

<main class="page-container">


    <!-- =====================================================
         PAGE HEADER
    ====================================================== -->

    <div class="page-header">

        <div>

            <div class="eyebrow">
                IBL CART 24 • ADMIN PANEL
            </div>

            <h1 class="page-title">
                Order Management
            </h1>

            <p class="page-description">
                Search, filter and manage customer orders from one place.
            </p>

        </div>


        <div class="admin-profile">

            <div class="admin-avatar">

                <i class="fa-solid fa-user-shield"></i>

            </div>

            <div>

                <div class="admin-name">
                    IBL CART Admin
                </div>

                <div class="admin-role">
                    Administrator
                </div>

            </div>

        </div>

    </div>



    <!-- =====================================================
         STATISTICS
    ====================================================== -->

    <div class="stats-grid">


        <!-- TOTAL -->

        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon blue">

                    <i class="fa-solid fa-receipt"></i>

                </div>

            </div>

            <div class="stat-label">
                Total Orders
            </div>

            <div class="stat-number">
                {{ $totalOrders ?? $orders->total() ?? 0 }}
            </div>

        </div>


        <!-- PENDING -->

        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon orange">

                    <i class="fa-solid fa-clock"></i>

                </div>

            </div>

            <div class="stat-label">
                Pending
            </div>

            <div class="stat-number">
                {{ $pendingOrders ?? 0 }}
            </div>

        </div>


        <!-- PROCESSING -->

        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon cyan">

                    <i class="fa-solid fa-gears"></i>

                </div>

            </div>

            <div class="stat-label">
                Processing
            </div>

            <div class="stat-number">
                {{ $processingOrders ?? 0 }}
            </div>

        </div>


        <!-- DELIVERED -->

        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon green">

                    <i class="fa-solid fa-box-open"></i>

                </div>

            </div>

            <div class="stat-label">
                Delivered
            </div>

            <div class="stat-number">
                {{ $deliveredOrders ?? 0 }}
            </div>

        </div>


        <!-- CANCELLED -->

        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon red">

                    <i class="fa-solid fa-circle-xmark"></i>

                </div>

            </div>

            <div class="stat-label">
                Cancelled
            </div>

            <div class="stat-number">
                {{ $cancelledOrders ?? 0 }}
            </div>

        </div>


        <!-- PAID -->

        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon purple">

                    <i class="fa-solid fa-money-check-dollar"></i>

                </div>

            </div>

            <div class="stat-label">
                Paid Orders
            </div>

            <div class="stat-number">
                {{ $paidOrders ?? 0 }}
            </div>

        </div>

    </div>



    <!-- =====================================================
         FILTERS
    ====================================================== -->

    <div class="filter-card">

        <div class="filter-header">

            <div class="filter-title">

                <span class="filter-icon">

                    <i class="fa-solid fa-filter"></i>

                </span>

                <span>
                    Search & Filter Orders
                </span>

            </div>


            <span class="text-muted small d-none d-md-block">

                Refine your order list

            </span>

        </div>


        <div class="filter-body">

            <form
                method="GET"
                action="{{ route('admin.orders.index') }}"
            >

                <div class="row g-3 align-items-end">


                    <!-- SEARCH -->

                    <div class="col-xl-5 col-lg-4">

                        <label
                            class="form-label"
                            for="search"
                        >

                            Search Orders

                        </label>


                        <div class="search-input-wrapper">

                            <i class="fa-solid fa-magnifying-glass"></i>

                            <input
                                type="text"
                                name="search"
                                id="search"
                                class="form-control"
                                value="{{ request('search') }}"
                                placeholder="Order ID, customer name, phone..."
                            >

                        </div>

                    </div>


                    <!-- ORDER STATUS -->

                    <div class="col-xl-3 col-lg-3">

                        <label
                            class="form-label"
                            for="status"
                        >

                            Order Status

                        </label>


                        <select
                            name="status"
                            id="status"
                            class="form-select"
                        >

                            <option value="">
                                All Status
                            </option>

                            <option
                                value="pending"
                                @selected(request('status') === 'pending')
                            >
                                Pending
                            </option>

                            <option
                                value="confirmed"
                                @selected(request('status') === 'confirmed')
                            >
                                Confirmed
                            </option>

                            <option
                                value="processing"
                                @selected(request('status') === 'processing')
                            >
                                Processing
                            </option>

                            <option
                                value="shipped"
                                @selected(request('status') === 'shipped')
                            >
                                Shipped
                            </option>

                            <option
                                value="delivered"
                                @selected(request('status') === 'delivered')
                            >
                                Delivered
                            </option>

                            <option
                                value="cancelled"
                                @selected(request('status') === 'cancelled')
                            >
                                Cancelled
                            </option>

                        </select>

                    </div>


                    <!-- PAYMENT STATUS -->

                    <div class="col-xl-2 col-lg-3">

                        <label
                            class="form-label"
                            for="payment_status"
                        >

                            Payment Status

                        </label>


                        <select
                            name="payment_status"
                            id="payment_status"
                            class="form-select"
                        >

                            <option value="">
                                All Payments
                            </option>

                            <option
                                value="Pending"
                                @selected(request('payment_status') === 'Pending')
                            >
                                Pending
                            </option>

                            <option
                                value="Paid"
                                @selected(request('payment_status') === 'Paid')
                            >
                                Paid
                            </option>

                            <option
                                value="Failed"
                                @selected(request('payment_status') === 'Failed')
                            >
                                Failed
                            </option>

                            <option
                                value="Refunded"
                                @selected(request('payment_status') === 'Refunded')
                            >
                                Refunded
                            </option>

                        </select>

                    </div>


                    <!-- BUTTONS -->

                    <div class="col-xl-2 col-lg-2">

                        <div class="d-grid gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >

                                <i class="fa-solid fa-filter me-1"></i>

                                Apply Filters

                            </button>


                            @if(
                                request()->filled('search')
                                ||
                                request()->filled('status')
                                ||
                                request()->filled('payment_status')
                            )

                                <a
                                    href="{{ route('admin.orders.index') }}"
                                    class="btn btn-outline-secondary"
                                >

                                    <i class="fa-solid fa-xmark me-1"></i>

                                    Clear Filters

                                </a>

                            @endif

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>



    <!-- =====================================================
         ORDERS
    ====================================================== -->

    <div class="orders-card">


        <!-- HEADER -->

        <div class="orders-header">

            <div class="orders-heading">

                <div class="orders-heading-icon">

                    <i class="fa-solid fa-list-check"></i>

                </div>

                <div>

                    <h2 class="orders-title">
                        All Orders
                    </h2>

                    <div class="orders-subtitle">
                        Customer order records
                    </div>

                </div>

            </div>


            @if(isset($orders))

                <div class="result-count">

                    Showing
                    {{ $orders->firstItem() ?? 0 }}
                    –
                    {{ $orders->lastItem() ?? 0 }}
                    of
                    {{ $orders->total() }}
                    orders

                </div>

            @endif

        </div>



        <!-- =================================================
             TABLE
        ================================================== -->

        @if(isset($orders) && $orders->count() > 0)

            <div class="table-responsive">

                <table class="orders-table">

                    <thead>

                        <tr>

                            <th>
                                Order
                            </th>

                            <th>
                                Customer
                            </th>

                            <th>
                                Contact
                            </th>

                            <th>
                                Total
                            </th>

                            <th>
                                Payment
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Date
                            </th>

                            <th class="text-end">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($orders as $order)

                            @php

                                $orderStatus = strtolower(
                                    trim($order->status ?? 'pending')
                                );

                                $paymentStatus = strtolower(
                                    trim($order->payment_status ?? 'pending')
                                );

                                $orderTotal =
                                    $order->total_amount
                                    ??
                                    $order->total
                                    ??
                                    0;

                                $customerName =
                                    $order->customer_name
                                    ?: optional($order->user)->name
                                    ?: 'Guest';

                                $initials = collect(
                                    preg_split(
                                        '/\s+/',
                                        trim($customerName)
                                    )
                                )
                                ->filter()
                                ->take(2)
                                ->map(
                                    fn($word) =>
                                        strtoupper(
                                            substr($word, 0, 1)
                                        )
                                )
                                ->implode('');

                            @endphp


                            <tr>


                                <!-- ORDER -->

                                <td>

                                    <div class="order-id">

                                        #{{ $order->id }}

                                    </div>

                                    <div class="order-label">
                                        Order
                                    </div>

                                </td>



                                <!-- CUSTOMER -->

                                <td>

                                    <div class="customer-cell">

                                        <div class="customer-avatar">

                                            {{ $initials ?: 'U' }}

                                        </div>


                                        <div>

                                            <div
                                                class="customer-name"
                                                title="{{ $customerName }}"
                                            >

                                                {{ $customerName }}

                                            </div>


                                            <div class="customer-type">

                                                @if($order->user_id)

                                                    <i class="fa-solid fa-circle-check text-success me-1"></i>

                                                    Registered Customer

                                                @else

                                                    <i class="fa-solid fa-user-clock me-1"></i>

                                                    Guest Customer

                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                </td>



                                <!-- CONTACT -->

                                <td>

                                    @if($order->phone)

                                        <div class="phone">

                                            <i class="fa-solid fa-phone"></i>

                                            {{ $order->phone }}

                                        </div>

                                    @else

                                        <span class="text-muted small">
                                            N/A
                                        </span>

                                    @endif

                                </td>



                                <!-- TOTAL -->

                                <td>

                                    <div class="amount">

                                        ৳{{ number_format(
                                            (float) $orderTotal,
                                            2
                                        ) }}

                                    </div>

                                    <div class="amount-label">
                                        Total Amount
                                    </div>

                                </td>



                                <!-- PAYMENT -->

                                <td>

                                    @if($paymentStatus === 'paid')

                                        <span class="payment-badge payment-paid">

                                            <i class="fa-solid fa-circle-check"></i>

                                            Paid

                                        </span>

                                    @elseif($paymentStatus === 'failed')

                                        <span class="payment-badge payment-failed">

                                            <i class="fa-solid fa-circle-xmark"></i>

                                            Failed

                                        </span>

                                    @elseif($paymentStatus === 'refunded')

                                        <span class="payment-badge payment-refunded">

                                            <i class="fa-solid fa-rotate-left"></i>

                                            Refunded

                                        </span>

                                    @else

                                        <span class="payment-badge payment-pending">

                                            <i class="fa-solid fa-clock"></i>

                                            Pending

                                        </span>

                                    @endif


                                    <div class="payment-method">

                                        {{ $order->payment_method ?: 'Cash on Delivery' }}

                                    </div>

                                </td>



                                <!-- ORDER STATUS -->

                                <td>

                                    @if($orderStatus === 'pending')

                                        <span class="order-status pending">

                                            <i class="fa-solid fa-clock"></i>

                                            Pending

                                        </span>

                                    @elseif($orderStatus === 'confirmed')

                                        <span class="order-status confirmed">

                                            <i class="fa-solid fa-circle-check"></i>

                                            Confirmed

                                        </span>

                                    @elseif($orderStatus === 'processing')

                                        <span class="order-status processing">

                                            <i class="fa-solid fa-gears"></i>

                                            Processing

                                        </span>

                                    @elseif($orderStatus === 'shipped')

                                        <span class="order-status shipped">

                                            <i class="fa-solid fa-truck"></i>

                                            Shipped

                                        </span>

                                    @elseif($orderStatus === 'delivered')

                                        <span class="order-status delivered">

                                            <i class="fa-solid fa-box-open"></i>

                                            Delivered

                                        </span>

                                    @elseif($orderStatus === 'cancelled')

                                        <span class="order-status cancelled">

                                            <i class="fa-solid fa-circle-xmark"></i>

                                            Cancelled

                                        </span>

                                    @else

                                        <span class="order-status default">

                                            {{ ucfirst($orderStatus) }}

                                        </span>

                                    @endif

                                </td>



                                <!-- DATE -->

                                <td>

                                    @if($order->created_at)

                                        <div class="date-main">

                                            {{ $order->created_at->format('d M Y') }}

                                        </div>

                                        <div class="date-time">

                                            <i class="fa-regular fa-clock me-1"></i>

                                            {{ $order->created_at->format('h:i A') }}

                                        </div>

                                    @else

                                        <span class="text-muted">
                                            N/A
                                        </span>

                                    @endif

                                </td>



                                <!-- ACTION -->

                                <td class="text-end">

                                    <a
                                        href="{{ route('admin.orders.show', $order) }}"
                                        class="view-btn"
                                    >

                                        <i class="fa-solid fa-eye"></i>

                                        View

                                    </a>

                                </td>


                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            <!-- =================================================
                 PAGINATION
            ================================================== -->

            <div class="pagination-wrapper">

                <div class="pagination-info">

                    @if($orders->total() > 0)

                        Showing

                        <strong>
                            {{ $orders->firstItem() }}
                        </strong>

                        to

                        <strong>
                            {{ $orders->lastItem() }}
                        </strong>

                        of

                        <strong>
                            {{ $orders->total() }}
                        </strong>

                        results

                    @else

                        No results found.

                    @endif

                </div>


                <div>

                    {{ $orders->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}

                </div>

            </div>


        @else


            <!-- =================================================
                 EMPTY STATE
            ================================================== -->

            <div class="empty-state">

                <div class="empty-icon">

                    <i class="fa-solid fa-box-open"></i>

                </div>


                <div class="empty-title">

                    No Orders Found

                </div>


                <p class="empty-text">

                    No orders match your current search or filter criteria.

                </p>


                @if(
                    request()->filled('search')
                    ||
                    request()->filled('status')
                    ||
                    request()->filled('payment_status')
                )

                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="btn btn-outline-primary"
                    >

                        <i class="fa-solid fa-rotate-left me-1"></i>

                        Reset Filters

                    </a>

                @endif

            </div>

        @endif

    </div>



    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4">

        <div class="text-muted small">

            <i class="fa-solid fa-shield-halved me-1"></i>

            IBL CART 24 Admin Panel

        </div>


        <div class="text-muted small">

            Order Management

            <span class="mx-1">
                •
            </span>

            {{ now()->format('Y') }}

        </div>

    </div>


</main>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>