@extends('layouts.admin')

@section('title', 'Analytics & Reports')

@section('page-title', 'Analytics & Reports')

@section('content')

<link rel="stylesheet" href="{{ asset('css/pages/admin-analytics-index.css') }}?v=b3">


<div class="analytics-page">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="analytics-header">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-4">

            <div>

                <h2>
                    <i class="fas fa-chart-line me-2"></i>
                    Analytics & Reports
                </h2>

                <p>
                    Monitor your store performance, sales and customers.
                </p>

            </div>


            <div class="range-buttons">

                <a
                    href="{{ route('admin.analytics', ['range' => 'today']) }}"
                    class="{{ $range === 'today' ? 'active' : '' }}"
                >
                    Today
                </a>

                <a
                    href="{{ route('admin.analytics', ['range' => 'week']) }}"
                    class="{{ $range === 'week' ? 'active' : '' }}"
                >
                    This Week
                </a>

                <a
                    href="{{ route('admin.analytics', ['range' => 'month']) }}"
                    class="{{ $range === 'month' ? 'active' : '' }}"
                >
                    This Month
                </a>

                <a
                    href="{{ route('admin.analytics', ['range' => 'year']) }}"
                    class="{{ $range === 'year' ? 'active' : '' }}"
                >
                    This Year
                </a>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- MAIN STATISTICS --}}
    {{-- ========================================================= --}}

    <div class="row g-4 mb-4">

        {{-- Total Sales --}}
        <div class="col-xl-4 col-md-6">

            <div class="stat-card">

                <div class="stat-icon">
                    <i class="fas fa-bangladeshi-taka-sign"></i>
                </div>

                <div class="stat-label">
                    Total Sales
                </div>

                <div class="stat-value">
                    ৳{{ number_format($totalSales ?? 0, 2) }}
                </div>

            </div>

        </div>


        {{-- Total Orders --}}
        <div class="col-xl-4 col-md-6">

            <div class="stat-card">

                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>

                <div class="stat-label">
                    Total Orders
                </div>

                <div class="stat-value">
                    {{ number_format($totalOrders ?? 0) }}
                </div>

            </div>

        </div>


        {{-- Average Order --}}
        <div class="col-xl-4 col-md-6">

            <div class="stat-card">

                <div class="stat-icon">
                    <i class="fas fa-receipt"></i>
                </div>

                <div class="stat-label">
                    Average Order Value
                </div>

                <div class="stat-value">
                    ৳{{ number_format($averageOrderValue ?? 0, 2) }}
                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ORDER STATUS --}}
    {{-- ========================================================= --}}

    <div class="section-card">

        <div class="section-header">

            <h5>
                <i class="fas fa-boxes-stacked me-2"></i>
                Order Status
            </h5>

        </div>


        <div class="section-body">

            <div class="row g-3">

                <div class="col-xl-2 col-md-4 col-6">

                    <div class="status-card">

                        <div class="status-title">
                            Pending
                        </div>

                        <div class="status-number">
                            {{ $pendingOrders ?? 0 }}
                        </div>

                    </div>

                </div>


                <div class="col-xl-2 col-md-4 col-6">

                    <div class="status-card">

                        <div class="status-title">
                            Confirmed
                        </div>

                        <div class="status-number">
                            {{ $confirmedOrders ?? 0 }}
                        </div>

                    </div>

                </div>


                <div class="col-xl-2 col-md-4 col-6">

                    <div class="status-card">

                        <div class="status-title">
                            Processing
                        </div>

                        <div class="status-number">
                            {{ $processingOrders ?? 0 }}
                        </div>

                    </div>

                </div>


                <div class="col-xl-2 col-md-4 col-6">

                    <div class="status-card">

                        <div class="status-title">
                            Shipped
                        </div>

                        <div class="status-number">
                            {{ $shippedOrders ?? 0 }}
                        </div>

                    </div>

                </div>


                <div class="col-xl-2 col-md-4 col-6">

                    <div class="status-card">

                        <div class="status-title">
                            Delivered
                        </div>

                        <div class="status-number">
                            {{ $deliveredOrders ?? 0 }}
                        </div>

                    </div>

                </div>


                <div class="col-xl-2 col-md-4 col-6">

                    <div class="status-card">

                        <div class="status-title">
                            Cancelled
                        </div>

                        <div class="status-number">
                            {{ $cancelledOrders ?? 0 }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SALES CHART --}}
    {{-- ========================================================= --}}

    <div class="section-card">

        <div class="section-header">

            <h5>
                <i class="fas fa-chart-area me-2"></i>
                Sales Overview
            </h5>

            <span class="text-muted small">
                {{ ucfirst($range) }} performance
            </span>

        </div>


        <div class="section-body">

            <div style="height: 350px;">

                <canvas id="salesChart"></canvas>

            </div>

        </div>

    </div>


    <div class="row g-4">


        {{-- ===================================================== --}}
        {{-- BEST SELLING PRODUCTS --}}
        {{-- ===================================================== --}}

        <div class="col-xl-7">

            <div class="section-card h-100">

                <div class="section-header">

                    <h5>
                        <i class="fas fa-trophy me-2"></i>
                        Best-Selling Products
                    </h5>

                </div>


                <div class="section-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover mb-0">

                            <thead>

                                <tr>

                                    <th class="ps-4">
                                        Product
                                    </th>

                                    <th>
                                        Sold
                                    </th>

                                    <th>
                                        Revenue
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($bestSellingProducts ?? [] as $item)

                                    <tr>

                                        <td class="ps-4">

                                            <div class="d-flex align-items-center gap-3">

                                                @if(
                                                    $item->product &&
                                                    $item->product->image
                                                )

                                                    <img
                                                        src="{{ asset('uploads/products/' . $item->product->image) }}"
                                                        class="product-thumb"
                                                        alt="{{ $item->product->name }}"
                                                    >

                                                @else

                                                    <div class="product-thumb d-flex align-items-center justify-content-center bg-light">

                                                        <i class="fas fa-image text-muted"></i>

                                                    </div>

                                                @endif


                                                <div>

                                                    <div class="fw-bold">

                                                        {{ $item->product->name ?? 'Deleted Product' }}

                                                    </div>

                                                    @if($item->product)

                                                        <small class="text-muted">

                                                            Product #{{ $item->product->id }}

                                                        </small>

                                                    @endif

                                                </div>

                                            </div>

                                        </td>


                                        <td>

                                            <span class="badge bg-light text-dark">

                                                {{ number_format($item->total_quantity ?? 0) }}

                                            </span>

                                        </td>


                                        <td>

                                            <strong>

                                                ৳{{ number_format($item->revenue ?? 0, 2) }}

                                            </strong>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="3">

                                            <div class="empty-state">

                                                <i class="fas fa-box-open d-block"></i>

                                                No sales data available.

                                            </div>

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- TOP CUSTOMERS --}}
        {{-- ===================================================== --}}

        <div class="col-xl-5">

            <div class="section-card h-100">

                <div class="section-header">

                    <h5>
                        <i class="fas fa-users me-2"></i>
                        Top Customers
                    </h5>

                </div>


                <div class="section-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover mb-0">

                            <thead>

                                <tr>

                                    <th class="ps-4">
                                        Customer
                                    </th>

                                    <th>
                                        Orders
                                    </th>

                                    <th>
                                        Spent
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($topCustomers ?? [] as $customer)

                                    <tr>

                                        <td class="ps-4">

                                            <div class="d-flex align-items-center gap-2">

                                                <div class="customer-avatar">

                                                    {{ strtoupper(substr($customer->name ?? 'U', 0, 1)) }}

                                                </div>


                                                <div>

                                                    <div class="fw-bold">

                                                        {{ $customer->name ?? 'Unknown' }}

                                                    </div>

                                                    <small class="text-muted">

                                                        {{ $customer->email ?? '' }}

                                                    </small>

                                                </div>

                                            </div>

                                        </td>


                                        <td>

                                            {{ number_format($customer->total_orders ?? 0) }}

                                        </td>


                                        <td>

                                            <strong>

                                                ৳{{ number_format($customer->total_spent ?? 0, 2) }}

                                            </strong>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="3">

                                            <div class="empty-state">

                                                <i class="fas fa-users d-block"></i>

                                                No customer data available.

                                            </div>

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- PAYMENT ANALYTICS --}}
    {{-- ========================================================= --}}

    <div class="row g-4 mt-1">


        <div class="col-xl-6">

            <div class="section-card">

                <div class="section-header">

                    <h5>

                        <i class="fas fa-credit-card me-2"></i>

                        Payment Overview

                    </h5>

                </div>


                <div class="section-body">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="status-card">

                                <div class="status-title">
                                    Paid Orders
                                </div>

                                <div class="status-number">
                                    {{ $paidOrders ?? 0 }}
                                </div>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="status-card">

                                <div class="status-title">
                                    Pending Payments
                                </div>

                                <div class="status-number">
                                    {{ $pendingPayments ?? 0 }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-xl-6">

            <div class="section-card">

                <div class="section-header">

                    <h5>

                        <i class="fas fa-wallet me-2"></i>

                        Payment Methods

                    </h5>

                </div>


                <div class="section-body">

                    @forelse($paymentMethods ?? [] as $payment)

                        <div class="payment-item">

                            <div class="payment-name">

                                <i class="fas fa-money-bill-wave me-2"></i>

                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'Unknown')) }}

                            </div>


                            <div class="payment-count">

                                {{ number_format($payment->total ?? 0) }}

                                <span class="text-muted fw-normal">
                                    orders
                                </span>

                            </div>

                        </div>

                    @empty

                        <div class="empty-state">

                            <i class="fas fa-wallet d-block"></i>

                            No payment data available.

                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>


</div>


{{-- ============================================================= --}}
{{-- CHART.JS --}}
{{-- ============================================================= --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const canvas = document.getElementById('salesChart');

    if (!canvas) {
        return;
    }


    const dailySales = @json($dailySales ?? []);

    const monthlySales = @json($monthlySales ?? []);

    let labels = [];
    let values = [];


    /*
    |--------------------------------------------------------------------------
    | TODAY / WEEK / MONTH
    |--------------------------------------------------------------------------
    */

    if (
        '{{ $range }}' === 'today' ||
        '{{ $range }}' === 'week' ||
        '{{ $range }}' === 'month'
    ) {

        labels = dailySales.map(function (item) {

            return item.date;

        });


        values = dailySales.map(function (item) {

            return Number(item.total || 0);

        });

    }


    /*
    |--------------------------------------------------------------------------
    | YEAR
    |--------------------------------------------------------------------------
    */

    if ('{{ $range }}' === 'year') {

        const monthNames = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];


        labels = monthlySales.map(function (item) {

            return monthNames[
                Number(item.month) - 1
            ] || item.month;

        });


        values = monthlySales.map(function (item) {

            return Number(item.total || 0);

        });

    }


    new Chart(canvas, {

        type: 'line',

        data: {

            labels: labels,

            datasets: [

                {

                    label: 'Sales (৳)',

                    data: values,

                    fill: true,

                    tension: 0.35,

                    borderWidth: 3,

                    pointRadius: 4,

                    pointHoverRadius: 6

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

                    display: true

                }

            },


            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {

                        callback: function (value) {

                            return '৳' +
                                Number(value).toLocaleString();

                        }

                    }

                }

            }

        }

    });

});

</script>

@endsection

