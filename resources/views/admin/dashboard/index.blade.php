@extends('layouts.admin')

@section('title', 'Dashboard')

@section('page-title', 'Admin Dashboard')

@section('content')

<link rel="stylesheet" href="{{ asset('css/pages/admin-dashboard-index.css') }}?v=b3">


{{-- =========================================================
     DASHBOARD HEADER
========================================================= --}}

<div class="dashboard-header">

    <h2 class="dashboard-welcome">
        Welcome to IBL CART 24
    </h2>

    <p class="dashboard-description">
        Manage your store, products, customers and orders from your admin dashboard.
    </p>

</div>


{{-- =========================================================
     STAT CARDS
========================================================= --}}

<div class="row g-3 mb-4">


    {{-- Total Sales --}}

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card stat-sales">

            <div class="stat-icon">
                <i class="fa-solid fa-bangladeshi-taka-sign"></i>
            </div>

            <div class="stat-label">
                Total Sales
            </div>

            <div class="stat-value">
                ৳{{ number_format($totalSales ?? 0, 2) }}
            </div>

            <div class="stat-meta">
                Excluding cancelled orders
            </div>

        </div>

    </div>


    {{-- Today's Sales --}}

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card stat-today">

            <div class="stat-icon">
                <i class="fa-solid fa-calendar-day"></i>
            </div>

            <div class="stat-label">
                Today's Sales
            </div>

            <div class="stat-value">
                ৳{{ number_format($todaySales ?? 0, 2) }}
            </div>

            <div class="stat-meta">
                {{ now()->format('d M Y') }}
            </div>

        </div>

    </div>


    {{-- Total Orders --}}

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card stat-orders">

            <div class="stat-icon">
                <i class="fa-solid fa-receipt"></i>
            </div>

            <div class="stat-label">
                Total Orders
            </div>

            <div class="stat-value">
                {{ number_format($totalOrders ?? 0) }}
            </div>

            <div class="stat-meta">
                All customer orders
            </div>

        </div>

    </div>


    {{-- Pending Orders --}}

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card stat-pending">

            <div class="stat-icon">
                <i class="fa-solid fa-clock"></i>
            </div>

            <div class="stat-label">
                Pending Orders
            </div>

            <div class="stat-value">
                {{ number_format($pendingOrders ?? 0) }}
            </div>

            <div class="stat-meta">
                Require attention
            </div>

        </div>

    </div>


    {{-- Products --}}

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card stat-products">

            <div class="stat-icon">
                <i class="fa-solid fa-box"></i>
            </div>

            <div class="stat-label">
                Products
            </div>

            <div class="stat-value">
                {{ number_format($totalProducts ?? 0) }}
            </div>

            <div class="stat-meta">
                Active store products
            </div>

        </div>

    </div>


    {{-- Categories --}}

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card stat-category">

            <div class="stat-icon">
                <i class="fa-solid fa-layer-group"></i>
            </div>

            <div class="stat-label">
                Categories
            </div>

            <div class="stat-value">
                {{ number_format($totalCategories ?? 0) }}
            </div>

            <div class="stat-meta">
                Product categories
            </div>

        </div>

    </div>


    {{-- Stock --}}

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card stat-stock">

            <div class="stat-icon">
                <i class="fa-solid fa-cubes-stacked"></i>
            </div>

            <div class="stat-label">
                Total Stock
            </div>

            <div class="stat-value">
                {{ number_format($totalStock ?? 0) }}
            </div>

            <div class="stat-meta">
                Units currently available
            </div>

        </div>

    </div>


    {{-- Cart Items --}}

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card stat-cart">

            <div class="stat-icon">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>

            <div class="stat-label">
                Cart Items
            </div>

            <div class="stat-value">
                {{ number_format($cartItems ?? 0) }}
            </div>

            <div class="stat-meta">
                Current session cart
            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     SALES + ORDER STATUS
========================================================= --}}

<div class="row g-3 mb-4">


    {{-- Monthly Sales --}}

    <div class="col-12 col-xl-8">

        <div class="dashboard-card">

            <div class="dashboard-card-header">

                <div>

                    <h3 class="dashboard-card-title">
                        Monthly Sales
                    </h3>

                    <p class="dashboard-card-subtitle">
                        Sales performance for {{ now()->year }}
                    </p>

                </div>

            </div>

            <div class="dashboard-card-body">

                <div class="sales-chart-wrapper">

                    <canvas id="monthlySalesChart"></canvas>

                </div>

            </div>

        </div>

    </div>


    {{-- Order Status --}}

    <div class="col-12 col-xl-4">

        <div class="dashboard-card">

            <div class="dashboard-card-header">

                <div>

                    <h3 class="dashboard-card-title">
                        Order Status
                    </h3>

                    <p class="dashboard-card-subtitle">
                        Current order distribution
                    </p>

                </div>

            </div>

            <div class="dashboard-card-body">


                @php

                    $statusTotal = max(
                        1,
                        array_sum($orderStatusCounts ?? [])
                    );

                @endphp


                @foreach([
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'processing' => 'Processing',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ] as $statusKey => $statusLabel)

                    @php

                        $statusCount =
                            $orderStatusCounts[$statusKey]
                            ?? 0;

                        $statusPercentage =
                            ($statusCount / $statusTotal) * 100;

                    @endphp


                    <div class="status-row">

                        <div class="status-info">

                            <span class="status-name">
                                {{ $statusLabel }}
                            </span>

                            <span class="status-count">
                                {{ $statusCount }}
                            </span>

                        </div>

                        <div class="status-progress">

                            <div
                                class="status-progress-bar status-{{ $statusKey }}"
                                style="width: {{ $statusPercentage }}%"
                            ></div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     LOW STOCK + QUICK ACTIONS
========================================================= --}}

<div class="row g-3 mb-4">


    {{-- Low Stock --}}

    <div class="col-12 col-xl-6">

        <div class="dashboard-card">

            <div class="dashboard-card-header">

                <div>

                    <h3 class="dashboard-card-title">
                        Low Stock
                    </h3>

                    <p class="dashboard-card-subtitle">
                        Products with 5 or fewer items
                    </p>

                </div>

                @if(Route::has('admin.products.index'))

                    <a
                        href="{{ route('admin.products.index') }}"
                        class="view-all"
                    >
                        Manage Products
                    </a>

                @endif

            </div>


            <div class="dashboard-card-body">


                @forelse($lowStockProducts ?? [] as $product)

                    <div class="low-stock-item">

                        <div class="product-mini">

                            @php

                                $imageUrl = null;

                                if (!empty($product->image)) {

                                    $imageUrl = asset(
                                        'uploads/products/' . $product->image
                                    );

                                }

                            @endphp


                            <img
                                src="{{ $imageUrl ?? asset('images/placeholder.jpg') }}"
                                alt="{{ $product->name }}"
                                class="product-mini-image"
                                onerror="this.onerror=null;this.src='{{ asset('images/placeholder.jpg') }}';"
                            >


                            <div class="min-w-0">

                                <div class="product-mini-name">

                                    {{ $product->name }}

                                </div>

                                <div class="product-mini-category">

                                    {{ $product->category?->name ?? 'Uncategorized' }}

                                </div>

                            </div>

                        </div>


                        <span class="stock-badge">

                            {{ (int) $product->stock }} left

                        </span>

                    </div>

                @empty

                    <div class="empty-state">

                        <i class="fa-solid fa-circle-check d-block"></i>

                        <p>
                            All products are well stocked.
                        </p>

                    </div>

                @endforelse


            </div>

        </div>

    </div>


    {{-- Quick Actions --}}

    <div class="col-12 col-xl-6">

        <div class="dashboard-card">

            <div class="dashboard-card-header">

                <div>

                    <h3 class="dashboard-card-title">
                        Quick Actions
                    </h3>

                    <p class="dashboard-card-subtitle">
                        Frequently used store management tools
                    </p>

                </div>

            </div>


            <div class="dashboard-card-body">

                <div class="row g-3">


                    @if(Route::has('admin.orders.index'))

                        <div class="col-12 col-sm-6">

                            <a
                                href="{{ route('admin.orders.index') }}"
                                class="quick-action"
                            >

                                <div class="quick-action-icon">

                                    <i class="fa-solid fa-receipt"></i>

                                </div>

                                <div>

                                    <div class="quick-action-title">
                                        Manage Orders
                                    </div>

                                    <div class="quick-action-text">
                                        View and manage orders
                                    </div>

                                </div>

                            </a>

                        </div>

                    @endif


                    @if(Route::has('admin.products.index'))

                        <div class="col-12 col-sm-6">

                            <a
                                href="{{ route('admin.products.index') }}"
                                class="quick-action"
                            >

                                <div class="quick-action-icon">

                                    <i class="fa-solid fa-box"></i>

                                </div>

                                <div>

                                    <div class="quick-action-title">
                                        Manage Products
                                    </div>

                                    <div class="quick-action-text">
                                        Add and edit products
                                    </div>

                                </div>

                            </a>

                        </div>

                    @endif


                    @if(Route::has('admin.categories.index'))

                        <div class="col-12 col-sm-6">

                            <a
                                href="{{ route('admin.categories.index') }}"
                                class="quick-action"
                            >

                                <div class="quick-action-icon">

                                    <i class="fa-solid fa-layer-group"></i>

                                </div>

                                <div>

                                    <div class="quick-action-title">
                                        Categories
                                    </div>

                                    <div class="quick-action-text">
                                        Organize your catalog
                                    </div>

                                </div>

                            </a>

                        </div>

                    @endif


                    @if(Route::has('admin.customers.index'))

                        <div class="col-12 col-sm-6">

                            <a
                                href="{{ route('admin.customers.index') }}"
                                class="quick-action"
                            >

                                <div class="quick-action-icon">

                                    <i class="fa-solid fa-users"></i>

                                </div>

                                <div>

                                    <div class="quick-action-title">
                                        Customers
                                    </div>

                                    <div class="quick-action-text">
                                        View customer information
                                    </div>

                                </div>

                            </a>

                        </div>

                    @endif


                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     RECENT ORDERS
========================================================= --}}

<div class="dashboard-card mb-4">

    <div class="dashboard-card-header">

        <div>

            <h3 class="dashboard-card-title">
                Recent Orders
            </h3>

            <p class="dashboard-card-subtitle">
                Latest customer orders
            </p>

        </div>

        @if(Route::has('admin.orders.index'))

            <a
                href="{{ route('admin.orders.index') }}"
                class="view-all"
            >
                View All
            </a>

        @endif

    </div>


    <div class="table-responsive">

        <table class="dashboard-table">

            <thead>

                <tr>

                    <th>
                        Order
                    </th>

                    <th>
                        Customer
                    </th>

                    <th>
                        Phone
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

                    <th class="text-end">
                        Action
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse($recentOrders ?? [] as $order)

                    <tr>

                        <td>

                            <span class="order-id">

                                #{{ $order->id }}

                            </span>

                        </td>


                        <td>

                            <span class="customer-name">

                                {{ $order->customer_name ?? 'Guest Customer' }}

                            </span>

                        </td>


                        <td>

                            {{ $order->phone ?? '—' }}

                        </td>


                        <td>

                            <span class="amount">

                                ৳{{ number_format(
                                    $order->total_amount
                                    ?? $order->total
                                    ?? 0,
                                    2
                                ) }}

                            </span>

                        </td>


                        <td>

                            {{ $order->payment_method ?? '—' }}

                        </td>


                        <td>

                            @php

                                $orderStatus =
                                    strtolower(
                                        $order->status ?? 'pending'
                                    );

                            @endphp


                            <span
                                class="order-status status-badge-{{ $orderStatus }}"
                            >

                                {{ ucfirst($orderStatus) }}

                            </span>

                        </td>


                        <td class="text-end">

                            @if(Route::has('admin.orders.show'))

                                <a
                                    href="{{ route('admin.orders.show', $order) }}"
                                    class="btn btn-sm btn-light"
                                    title="View Order"
                                >

                                    <i class="fa-solid fa-eye"></i>

                                </a>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="text-center py-5"
                        >

                            <div class="empty-state">

                                <i class="fa-solid fa-receipt d-block"></i>

                                <p>
                                    No orders found.
                                </p>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>


{{-- =========================================================
     RECENT PRODUCTS + TOP PRODUCTS
========================================================= --}}

<div class="row g-3 mb-4">


    {{-- Recent Products --}}

    <div class="col-12 col-xl-7">

        <div class="dashboard-card">

            <div class="dashboard-card-header">

                <div>

                    <h3 class="dashboard-card-title">
                        Recent Products
                    </h3>

                    <p class="dashboard-card-subtitle">
                        Latest products added to your store
                    </p>

                </div>

                @if(Route::has('admin.products.index'))

                    <a
                        href="{{ route('admin.products.index') }}"
                        class="view-all"
                    >
                        View All
                    </a>

                @endif

            </div>


            <div class="table-responsive">

                <table class="dashboard-table">

                    <thead>

                        <tr>

                            <th>
                                Product
                            </th>

                            <th>
                                Category
                            </th>

                            <th>
                                Price
                            </th>

                            <th>
                                Stock
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($recentProducts ?? [] as $product)

                            <tr>

                                <td>

                                    <div class="table-product">

                                        @php

                                            $productImage = null;

                                            if (!empty($product->image)) {

                                                $productImage = asset(
                                                    'uploads/products/' . $product->image
                                                );

                                            }

                                        @endphp


                                        <img
                                            src="{{ $productImage ?? asset('images/placeholder.jpg') }}"
                                            alt="{{ $product->name }}"
                                            class="table-product-image"
                                            onerror="this.onerror=null;this.src='{{ asset('images/placeholder.jpg') }}';"
                                        >


                                        <span class="table-product-name">

                                            {{ $product->name }}

                                        </span>

                                    </div>

                                </td>


                                <td>

                                    <span class="category-text">

                                        {{ $product->category?->name ?? 'Uncategorized' }}

                                    </span>

                                </td>


                                <td>

                                    <span class="amount">

                                        ৳{{ number_format(
                                            $product->price ?? 0,
                                            2
                                        ) }}

                                    </span>

                                </td>


                                <td>

                                    {{ (int) ($product->stock ?? 0) }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="4"
                                    class="text-center py-5"
                                >

                                    <div class="empty-state">

                                        <i class="fa-solid fa-box-open d-block"></i>

                                        <p>
                                            No products found.
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- Top Products --}}

    <div class="col-12 col-xl-5">

        <div class="dashboard-card">

            <div class="dashboard-card-header">

                <div>

                    <h3 class="dashboard-card-title">
                        Top Products
                    </h3>

                    <p class="dashboard-card-subtitle">
                        Based on units sold
                    </p>

                </div>

            </div>


            <div class="dashboard-card-body">


                @forelse($topProducts ?? [] as $index => $product)

                    <div class="top-product">

                        <div class="top-product-rank">

                            {{ $index + 1 }}

                        </div>


                        <div class="top-product-info">

                            <div class="top-product-name">

                                {{ $product->name }}

                            </div>

                            <div class="top-product-sales">

                                {{ number_format(
                                    $product->sold_quantity ?? 0
                                ) }}
                                units sold

                            </div>

                        </div>


                        <div class="amount">

                            ৳{{ number_format(
                                $product->price ?? 0,
                                2
                            ) }}

                        </div>

                    </div>

                @empty

                    <div class="empty-state">

                        <i class="fa-solid fa-chart-simple d-block"></i>

                        <p>
                            No sales data available yet.
                        </p>

                    </div>

                @endforelse


            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     PAYMENT OVERVIEW
========================================================= --}}

<div class="dashboard-card mb-4">

    <div class="dashboard-card-header">

        <div>

            <h3 class="dashboard-card-title">
                Payment Overview
            </h3>

            <p class="dashboard-card-subtitle">
                Current payment status across orders
            </p>

        </div>

    </div>


    <div class="dashboard-card-body">

        <div class="row g-3">


            {{-- Pending --}}

            <div class="col-6 col-md-3">

                <div class="p-3 rounded-3 bg-light">

                    <div class="text-muted small mb-1">
                        Pending
                    </div>

                    <div class="fw-bold fs-5">

                        {{ $paymentStatusCounts['pending'] ?? 0 }}

                    </div>

                </div>

            </div>


            {{-- Paid --}}

            <div class="col-6 col-md-3">

                <div class="p-3 rounded-3 bg-light">

                    <div class="text-muted small mb-1">
                        Paid
                    </div>

                    <div class="fw-bold fs-5">

                        {{ $paymentStatusCounts['paid'] ?? 0 }}

                    </div>

                </div>

            </div>


            {{-- Failed --}}

            <div class="col-6 col-md-3">

                <div class="p-3 rounded-3 bg-light">

                    <div class="text-muted small mb-1">
                        Failed
                    </div>

                    <div class="fw-bold fs-5">

                        {{ $paymentStatusCounts['failed'] ?? 0 }}

                    </div>

                </div>

            </div>


            {{-- Refunded --}}

            <div class="col-6 col-md-3">

                <div class="p-3 rounded-3 bg-light">

                    <div class="text-muted small mb-1">
                        Refunded
                    </div>

                    <div class="fw-bold fs-5">

                        {{ $paymentStatusCounts['refunded'] ?? 0 }}

                    </div>

                </div>

            </div>


        </div>

    </div>

</div>


{{-- =========================================================
     CHART.JS
========================================================= --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const canvas =
        document.getElementById('monthlySalesChart');

    if (!canvas) {
        return;
    }


    const monthlySales =
        @json(array_values($monthlySales ?? array_fill(1, 12, 0)));


    const monthLabels = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec'
    ];


    new Chart(canvas, {

        type: 'line',

        data: {

            labels: monthLabels,

            datasets: [

                {

                    label: 'Sales',

                    data: monthlySales,

                    fill: true,

                    tension: 0.35,

                    borderWidth: 2,

                    pointRadius: 3,

                    pointHoverRadius: 5

                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            interaction: {

                intersect: false,

                mode: 'index'

            },

            plugins: {

                legend: {

                    display: false

                },

                tooltip: {

                    callbacks: {

                        label: function (context) {

                            return ' ৳' +
                                Number(
                                    context.raw || 0
                                ).toLocaleString(
                                    'en-BD',
                                    {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }
                                );

                        }

                    }

                }

            },

            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {

                        callback: function (value) {

                            return '৳' +
                                Number(value).toLocaleString(
                                    'en-BD'
                                );

                        },

                        font: {

                            size: 10

                        }

                    },

                    grid: {

                        drawBorder: false

                    }

                },

                x: {

                    grid: {

                        display: false

                    },

                    ticks: {

                        font: {

                            size: 10

                        }

                    }

                }

            }

        }

    });

});

</script>

@endsection