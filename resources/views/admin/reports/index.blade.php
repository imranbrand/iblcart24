@extends('layouts.admin')

@section('title', 'Reports & Analytics')

@section('page-title', 'Reports & Analytics')

@section('content')

<link rel="stylesheet" href="{{ asset('css/pages/admin-reports-index.css') }}?v=b3">


<div class="container-fluid py-2">


    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="report-header">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>

                <h2>
                    <i class="fa-solid fa-chart-pie me-2"></i>
                    Reports & Analytics
                </h2>

                <p>
                    Complete sales, orders, customers and payment report
                </p>

                <div class="report-period">

                    <i class="fa-solid fa-calendar-days"></i>

                    {{ \Carbon\Carbon::parse($from)->format('d M Y') }}

                    <span>→</span>

                    {{ \Carbon\Carbon::parse($to)->format('d M Y') }}

                </div>

            </div>


            <div class="no-print">

                <a
                    href="{{ route('admin.analytics') }}"
                    class="btn btn-light"
                >
                    <i class="fa-solid fa-chart-line me-2"></i>
                    Analytics
                </a>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- DATE FILTER + EXPORT --}}
    {{-- ========================================================= --}}

    <div class="card filter-card no-print">

        <div class="card-body">

            <form
                action="{{ route('admin.reports') }}"
                method="GET"
            >

                <div class="row g-3 align-items-end">

                    {{-- FROM --}}
                    <div class="col-12 col-md-3">

                        <label class="form-label">
                            <i class="fa-solid fa-calendar-days me-1"></i>
                            From Date
                        </label>

                        <input
                            type="date"
                            name="from"
                            value="{{ $from }}"
                            class="form-control"
                            required
                        >

                    </div>


                    {{-- TO --}}
                    <div class="col-12 col-md-3">

                        <label class="form-label">
                            <i class="fa-solid fa-calendar-check me-1"></i>
                            To Date
                        </label>

                        <input
                            type="date"
                            name="to"
                            value="{{ $to }}"
                            class="form-control"
                            required
                        >

                    </div>


                    {{-- GENERATE --}}
                    <div class="col-12 col-md-2">

                        <button
                            type="submit"
                            class="btn btn-dark w-100"
                        >
                            <i class="fa-solid fa-filter me-2"></i>
                            Generate
                        </button>

                    </div>


                    {{-- EXPORT BUTTONS --}}
                    <div class="col-12 col-md-4">

                        <label class="form-label">
                            <i class="fa-solid fa-download me-1"></i>
                            Export Report
                        </label>

                        <div class="export-buttons">

                            {{-- EXCEL --}}
                            <a
                                href="{{ route('admin.reports.excel', [
                                    'from' => $from,
                                    'to' => $to
                                ]) }}"
                                class="btn btn-success"
                            >
                                <i class="fa-solid fa-file-excel me-1"></i>
                                Excel
                            </a>


                            {{-- CSV --}}
                            <a
                                href="{{ route('admin.reports.csv', [
                                    'from' => $from,
                                    'to' => $to
                                ]) }}"
                                class="btn btn-primary"
                            >
                                <i class="fa-solid fa-file-csv me-1"></i>
                                CSV
                            </a>


                            {{-- PDF --}}
                            <a
                                href="{{ route('admin.reports.pdf', [
                                    'from' => $from,
                                    'to' => $to
                                ]) }}"
                                class="btn btn-danger"
                            >
                                <i class="fa-solid fa-file-pdf me-1"></i>
                                PDF
                            </a>


                            {{-- PRINT --}}
                            <button
                                type="button"
                                onclick="window.print()"
                                class="btn btn-secondary"
                            >
                                <i class="fa-solid fa-print me-1"></i>
                                Print
                            </button>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SUMMARY CARDS --}}
    {{-- ========================================================= --}}

    <div class="row g-4 mb-4">


        {{-- TOTAL SALES --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card summary-card">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <div class="summary-label">
                                Total Sales
                            </div>

                            <div class="summary-value">
                                ৳{{ number_format($totalSales ?? 0, 2) }}
                            </div>

                        </div>

                        <div class="summary-icon icon-green">

                            <i class="fa-solid fa-wallet"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- TOTAL ORDERS --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card summary-card">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <div class="summary-label">
                                Total Orders
                            </div>

                            <div class="summary-value">
                                {{ $totalOrders ?? 0 }}
                            </div>

                        </div>

                        <div class="summary-icon icon-blue">

                            <i class="fa-solid fa-receipt"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- DELIVERED --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card summary-card">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <div class="summary-label">
                                Delivered Orders
                            </div>

                            <div class="summary-value">
                                {{ $deliveredOrders ?? 0 }}
                            </div>

                        </div>

                        <div class="summary-icon icon-green">

                            <i class="fa-solid fa-box-open"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- CANCELLED --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card summary-card">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <div class="summary-label">
                                Cancelled Orders
                            </div>

                            <div class="summary-value">
                                {{ $cancelledOrders ?? 0 }}
                            </div>

                        </div>

                        <div class="summary-icon icon-red">

                            <i class="fa-solid fa-ban"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ORDER STATUS --}}
    {{-- ========================================================= --}}

    <div class="card section-card">

        <div class="section-header">

            <h4>
                <i class="fa-solid fa-chart-simple text-primary me-2"></i>
                Order Status Summary
            </h4>

            <p>
                Order distribution for the selected period
            </p>

        </div>


        <div class="card-body p-4">

            <div class="row g-3">


                {{-- PENDING --}}
                <div class="col-6 col-md-4 col-xl-2">

                    <div class="mini-stat">

                        <div class="mini-stat-title">
                            Pending
                        </div>

                        <div class="mini-stat-value text-warning">
                            {{ $pendingOrders ?? 0 }}
                        </div>

                    </div>

                </div>


                {{-- PROCESSING --}}
                <div class="col-6 col-md-4 col-xl-2">

                    <div class="mini-stat">

                        <div class="mini-stat-title">
                            Processing
                        </div>

                        <div class="mini-stat-value">
                            {{ $processingOrders ?? 0 }}
                        </div>

                    </div>

                </div>


                {{-- SHIPPED --}}
                <div class="col-6 col-md-4 col-xl-2">

                    <div class="mini-stat">

                        <div class="mini-stat-title">
                            Shipped
                        </div>

                        <div class="mini-stat-value text-info">
                            {{ $shippedOrders ?? 0 }}
                        </div>

                    </div>

                </div>


                {{-- DELIVERED --}}
                <div class="col-6 col-md-4 col-xl-2">

                    <div class="mini-stat">

                        <div class="mini-stat-title">
                            Delivered
                        </div>

                        <div class="mini-stat-value text-success">
                            {{ $deliveredOrders ?? 0 }}
                        </div>

                    </div>

                </div>


                {{-- CANCELLED --}}
                <div class="col-6 col-md-4 col-xl-2">

                    <div class="mini-stat">

                        <div class="mini-stat-title">
                            Cancelled
                        </div>

                        <div class="mini-stat-value text-danger">
                            {{ $cancelledOrders ?? 0 }}
                        </div>

                    </div>

                </div>


                {{-- CONFIRMED --}}
                <div class="col-6 col-md-4 col-xl-2">

                    <div class="mini-stat">

                        <div class="mini-stat-title">
                            Confirmed
                        </div>

                        <div class="mini-stat-value text-primary">
                            {{ \App\Models\Order::whereBetween(
                                'created_at',
                                [
                                    $from . ' 00:00:00',
                                    $to . ' 23:59:59'
                                ]
                            )->where('status', 'confirmed')->count() }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- PAYMENT SUMMARY --}}
    {{-- ========================================================= --}}

    <div class="row g-4">


        {{-- PAYMENT STATUS --}}
        <div class="col-lg-6">

            <div class="card section-card h-100">

                <div class="section-header">

                    <h5>
                        <i class="fa-solid fa-credit-card text-success me-2"></i>
                        Payment Summary
                    </h5>

                    <p>
                        Payment status overview
                    </p>

                </div>


                <div class="card-body p-4">

                    <div class="row g-3">

                        <div class="col-6">

                            <div class="mini-stat">

                                <div class="mini-stat-title">
                                    Paid Orders
                                </div>

                                <div class="mini-stat-value text-success">
                                    {{ $paidOrders ?? 0 }}
                                </div>

                            </div>

                        </div>


                        <div class="col-6">

                            <div class="mini-stat">

                                <div class="mini-stat-title">
                                    Pending Payments
                                </div>

                                <div class="mini-stat-value text-warning">
                                    {{ $pendingPayments ?? 0 }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- PAYMENT METHODS --}}
        <div class="col-lg-6">

            <div class="card section-card h-100">

                <div class="section-header">

                    <h5>
                        <i class="fa-solid fa-money-check-dollar text-primary me-2"></i>
                        Payment Methods
                    </h5>

                    <p>
                        Payment method usage
                    </p>

                </div>


                <div class="card-body p-0">

                    @if(isset($paymentMethods) && $paymentMethods->count())

                        <div class="table-responsive">

                            <table class="table align-middle">

                                <thead>

                                    <tr>

                                        <th>
                                            Payment Method
                                        </th>

                                        <th class="text-end">
                                            Orders
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    @foreach($paymentMethods as $method)

                                        <tr>

                                            <td>

                                                <i class="fa-solid fa-wallet text-primary me-2"></i>

                                                <strong>
                                                    {{ ucfirst(
                                                        str_replace(
                                                            '_',
                                                            ' ',
                                                            $method->payment_method ?? 'Unknown'
                                                        )
                                                    ) }}
                                                </strong>

                                            </td>


                                            <td class="text-end">

                                                <span class="badge bg-primary rounded-pill px-3 py-2">

                                                    {{ $method->total }}

                                                </span>

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    @else

                        <div class="empty-state">

                            <i class="fa-solid fa-credit-card d-block"></i>

                            <div>
                                No payment data available.
                            </div>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- BEST SELLING PRODUCTS --}}
    {{-- ========================================================= --}}

    <div class="card section-card">

        <div class="section-header">

            <h4>
                <i class="fa-solid fa-trophy text-warning me-2"></i>
                Best Selling Products
            </h4>

            <p>
                Top products by quantity sold
            </p>

        </div>


        <div class="card-body p-0">

            @if(isset($bestSellingProducts) && $bestSellingProducts->count())

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>

                            <tr>

                                <th>
                                    #
                                </th>

                                <th>
                                    Product
                                </th>

                                <th>
                                    Quantity Sold
                                </th>

                                <th>
                                    Revenue
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($bestSellingProducts as $index => $item)

                                <tr>

                                    <td>

                                        <span class="rank-number">
                                            {{ $index + 1 }}
                                        </span>

                                    </td>


                                    <td>

                                        <div class="d-flex align-items-center">

                                            @if(
                                                $item->product &&
                                                $item->product->image
                                            )

                                                <img
                                                    src="{{ asset(
                                                        'uploads/products/' .
                                                        $item->product->image
                                                    ) }}"
                                                    class="product-thumb me-3"
                                                    alt="{{ $item->product->name }}"
                                                >

                                            @else

                                                <div class="product-thumb me-3 d-flex align-items-center justify-content-center">

                                                    <i class="fa-solid fa-box text-muted"></i>

                                                </div>

                                            @endif


                                            <div>

                                                <div class="customer-name">

                                                    {{ $item->product->name ?? 'Deleted Product' }}

                                                </div>

                                                <small class="text-muted">

                                                    Product ID:
                                                    #{{ $item->product_id }}

                                                </small>

                                            </div>

                                        </div>

                                    </td>


                                    <td>

                                        <span class="badge bg-primary rounded-pill px-3 py-2">

                                            {{ $item->total_quantity }}

                                            {{ $item->total_quantity == 1 ? 'item' : 'items' }}

                                        </span>

                                    </td>


                                    <td class="amount">

                                        ৳{{ number_format(
                                            $item->revenue ?? 0,
                                            2
                                        ) }}

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="empty-state">

                    <i class="fa-solid fa-box-open d-block"></i>

                    <strong>
                        No product sales found
                    </strong>

                    <div class="small mt-1">
                        There are no sales records for this date range.
                    </div>

                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- TOP CUSTOMERS --}}
    {{-- ========================================================= --}}

    <div class="card section-card">

        <div class="section-header">

            <h4>
                <i class="fa-solid fa-users text-primary me-2"></i>
                Top Customers
            </h4>

            <p>
                Customers ranked by total spending
            </p>

        </div>


        <div class="card-body p-0">

            @if(isset($topCustomers) && $topCustomers->count())

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>

                            <tr>

                                <th>
                                    #
                                </th>

                                <th>
                                    Customer
                                </th>

                                <th>
                                    Email
                                </th>

                                <th>
                                    Orders
                                </th>

                                <th>
                                    Total Spent
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($topCustomers as $index => $customer)

                                <tr>

                                    <td>

                                        <span class="rank-number">
                                            {{ $index + 1 }}
                                        </span>

                                    </td>


                                    <td>

                                        <div class="d-flex align-items-center">

                                            <div
                                                class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                                style="width:45px;height:45px;font-weight:800;"
                                            >
                                                {{ strtoupper(
                                                    mb_substr(
                                                        $customer->name ?? 'C',
                                                        0,
                                                        1
                                                    )
                                                ) }}
                                            </div>


                                            <div>

                                                <div class="customer-name">
                                                    {{ $customer->name ?? 'Customer' }}
                                                </div>

                                                <small class="text-muted">
                                                    Customer #{{ $customer->id }}
                                                </small>

                                            </div>

                                        </div>

                                    </td>


                                    <td>

                                        <span class="text-muted">
                                            {{ $customer->email ?? '-' }}
                                        </span>

                                    </td>


                                    <td>

                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-2">

                                            {{ $customer->total_orders }}

                                        </span>

                                    </td>


                                    <td class="amount">

                                        ৳{{ number_format(
                                            $customer->total_spent ?? 0,
                                            2
                                        ) }}

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="empty-state">

                    <i class="fa-solid fa-users-slash d-block"></i>

                    <strong>
                        No customer data found
                    </strong>

                    <div class="small mt-1">
                        No customer orders were found for this period.
                    </div>

                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ORDER STATUS DETAILS --}}
    {{-- ========================================================= --}}

    <div class="card section-card">

        <div class="section-header">

            <h4>
                <i class="fa-solid fa-list-check text-success me-2"></i>
                Detailed Order Status
            </h4>

            <p>
                Status-wise order breakdown
            </p>

        </div>


        <div class="card-body p-0">

            @if(isset($orderStatuses) && $orderStatuses->count())

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>

                            <tr>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Orders
                                </th>

                                <th>
                                    Percentage
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($orderStatuses as $status)

                                @php

                                    $statusName = strtolower(
                                        trim(
                                            $status->status ?? 'unknown'
                                        )
                                    );

                                    $statusClass =
                                        'status-' . $statusName;

                                    if (
                                        !in_array(
                                            $statusName,
                                            [
                                                'pending',
                                                'confirmed',
                                                'processing',
                                                'shipped',
                                                'delivered',
                                                'cancelled'
                                            ]
                                        )
                                    ) {
                                        $statusClass = 'status-default';
                                    }

                                    $percentage =
                                        ($totalOrders ?? 0) > 0
                                        ? (
                                            $status->total /
                                            $totalOrders
                                        ) * 100
                                        : 0;

                                @endphp


                                <tr>

                                    <td>

                                        <span class="status-badge {{ $statusClass }}">

                                            @if($statusName === 'pending')
                                                <i class="fa-solid fa-clock"></i>
                                            @elseif($statusName === 'confirmed')
                                                <i class="fa-solid fa-check"></i>
                                            @elseif($statusName === 'processing')
                                                <i class="fa-solid fa-gears"></i>
                                            @elseif($statusName === 'shipped')
                                                <i class="fa-solid fa-truck"></i>
                                            @elseif($statusName === 'delivered')
                                                <i class="fa-solid fa-box-open"></i>
                                            @elseif($statusName === 'cancelled')
                                                <i class="fa-solid fa-xmark"></i>
                                            @else
                                                <i class="fa-solid fa-circle"></i>
                                            @endif

                                            {{ ucfirst($statusName) }}

                                        </span>

                                    </td>


                                    <td>

                                        <strong>
                                            {{ $status->total }}
                                        </strong>

                                    </td>


                                    <td style="min-width:200px;">

                                        <div class="d-flex align-items-center gap-2">

                                            <div
                                                class="progress flex-grow-1"
                                                style="height:8px;"
                                            >

                                                <div
                                                    class="progress-bar"
                                                    role="progressbar"
                                                    style="width: {{ min($percentage, 100) }}%;"
                                                ></div>

                                            </div>

                                            <small class="fw-bold">

                                                {{ number_format(
                                                    $percentage,
                                                    1
                                                ) }}%

                                            </small>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="empty-state">

                    <i class="fa-solid fa-chart-column d-block"></i>

                    No order status data available.

                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ORDERS REPORT --}}
    {{-- ========================================================= --}}

    <div class="card section-card">

        <div class="section-header">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div>

                    <h4>
                        <i class="fa-solid fa-receipt text-danger me-2"></i>
                        Orders Report
                    </h4>

                    <p>
                        Complete order history for selected date range
                    </p>

                </div>


                <div>

                    <span class="badge bg-dark px-3 py-2">

                        {{ $orders->total() }}

                        {{ $orders->total() == 1 ? 'Order' : 'Orders' }}

                    </span>

                </div>

            </div>

        </div>


        <div class="card-body p-0">

            @if($orders->count())

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>

                            <tr>

                                <th>
                                    Order
                                </th>

                                <th>
                                    Customer
                                </th>

                                <th>
                                    Payment
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Total
                                </th>

                                <th>
                                    Date
                                </th>

                                <th class="text-center">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($orders as $order)

                                @php

                                    $status = strtolower(
                                        trim(
                                            $order->status ?? 'unknown'
                                        )
                                    );

                                    $statusClass =
                                        'status-' . $status;

                                    if (
                                        !in_array(
                                            $status,
                                            [
                                                'pending',
                                                'confirmed',
                                                'processing',
                                                'shipped',
                                                'delivered',
                                                'cancelled'
                                            ]
                                        )
                                    ) {
                                        $statusClass = 'status-default';
                                    }

                                    $paymentStatus = strtolower(
                                        trim(
                                            $order->payment_status
                                            ?? 'pending'
                                        )
                                    );

                                @endphp


                                <tr>

                                    {{-- ORDER --}}
                                    <td>

                                        <span class="order-id">

                                            #{{ $order->id }}

                                        </span>

                                    </td>


                                    {{-- CUSTOMER --}}
                                    <td>

                                        <div>

                                            <div class="customer-name">

                                                {{ $order->customer_name
                                                    ?? optional($order->user)->name
                                                    ?? 'Guest'
                                                }}

                                            </div>


                                            <small class="text-muted">

                                                {{ optional($order->user)->email
                                                    ?? 'No email'
                                                }}

                                            </small>

                                        </div>

                                    </td>


                                    {{-- PAYMENT --}}
                                    <td>

                                        <div class="mb-1">

                                            <strong>

                                                {{ ucfirst(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $order->payment_method
                                                        ?? 'N/A'
                                                    )
                                                ) }}

                                            </strong>

                                        </div>


                                        @if(
                                            in_array(
                                                $paymentStatus,
                                                [
                                                    'paid',
                                                    'completed',
                                                    'success',
                                                    'successful'
                                                ]
                                            )
                                        )

                                            <span class="payment-paid">

                                                <i class="fa-solid fa-circle-check me-1"></i>
                                                Paid

                                            </span>

                                        @elseif($paymentStatus === 'failed')

                                            <span class="payment-failed">

                                                <i class="fa-solid fa-circle-xmark me-1"></i>
                                                Failed

                                            </span>

                                        @else

                                            <span class="payment-pending">

                                                <i class="fa-solid fa-clock me-1"></i>
                                                Pending

                                            </span>

                                        @endif

                                    </td>


                                    {{-- STATUS --}}
                                    <td>

                                        <span class="status-badge {{ $statusClass }}">

                                            @if($status === 'pending')
                                                <i class="fa-solid fa-clock"></i>
                                            @elseif($status === 'confirmed')
                                                <i class="fa-solid fa-check"></i>
                                            @elseif($status === 'processing')
                                                <i class="fa-solid fa-gears"></i>
                                            @elseif($status === 'shipped')
                                                <i class="fa-solid fa-truck"></i>
                                            @elseif($status === 'delivered')
                                                <i class="fa-solid fa-box-open"></i>
                                            @elseif($status === 'cancelled')
                                                <i class="fa-solid fa-xmark"></i>
                                            @else
                                                <i class="fa-solid fa-circle"></i>
                                            @endif

                                            {{ ucfirst($status) }}

                                        </span>

                                    </td>


                                    {{-- TOTAL --}}
                                    <td>

                                        <span class="amount">

                                            ৳{{ number_format(
                                                (float) (
                                                    $order->total_amount
                                                    ?? 0
                                                ),
                                                2
                                            ) }}

                                        </span>

                                    </td>


                                    {{-- DATE --}}
                                    <td>

                                        <div>

                                            {{ $order->created_at
                                                ? $order->created_at->format('d M Y')
                                                : '-'
                                            }}

                                        </div>

                                        <small class="text-muted">

                                            {{ $order->created_at
                                                ? $order->created_at->format('h:i A')
                                                : ''
                                            }}

                                        </small>

                                    </td>


                                    {{-- ACTION --}}
                                    <td class="text-center">

                                        <a
                                            href="{{ route(
                                                'orders.show',
                                                $order->id
                                            ) }}"
                                            class="btn btn-sm btn-outline-primary"
                                        >

                                            <i class="fa-solid fa-eye me-1"></i>
                                            View

                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- PAGINATION --}}

                <div class="p-4 border-top">

                    {{ $orders->links() }}

                </div>

            @else

                <div class="empty-state">

                    <i class="fa-solid fa-receipt d-block"></i>

                    <strong>
                        No orders found
                    </strong>

                    <div class="small mt-1">
                        No orders were created during the selected date range.
                    </div>

                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- REPORT FOOTER --}}
    {{-- ========================================================= --}}

    <div class="text-center text-muted small py-3">

        <i class="fa-solid fa-shield-halved me-1"></i>

        IBL CART 24 — Business Reports

        <span class="mx-2">•</span>

        {{ now()->format('d M Y, h:i A') }}

    </div>


</div>


@endsection
