@extends('layouts.admin')

@section('title', 'Customer Details')

@section('content')

<div class="container-fluid py-4">

    {{-- ============================================================
         PAGE HEADER
    ============================================================= --}}

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a
                    href="{{ route('admin.customers.index') }}"
                    class="text-decoration-none text-secondary"
                >
                    <i class="fas fa-users me-1"></i>
                    Customers
                </a>

                <i class="fas fa-chevron-right text-muted small"></i>

                <span class="text-muted">
                    Customer Details
                </span>
            </div>

            <h2 class="fw-bold mb-1">
                <i class="fas fa-user-circle text-primary me-2"></i>
                Customer Details
            </h2>

            <p class="text-muted mb-0">
                View complete customer information, order history and statistics.
            </p>
        </div>

        <div class="d-flex gap-2">

            <a
                href="{{ route('admin.customers.index') }}"
                class="btn btn-outline-secondary"
            >
                <i class="fas fa-arrow-left me-1"></i>
                Back
            </a>

            <a
                href="{{ route('admin.customers.edit', ['user' => $user->id]) }}"
                class="btn btn-primary"
            >
                <i class="fas fa-edit me-1"></i>
                Edit Customer
            </a>

        </div>

    </div>


    {{-- ============================================================
         ALERTS
    ============================================================= --}}

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-2"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="fas fa-exclamation-circle me-2"></i>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>
    @endif


    {{-- ============================================================
         CUSTOMER PROFILE + STATISTICS
    ============================================================= --}}

    <div class="row g-4 mb-4">

        {{-- Customer Profile --}}
        <div class="col-xl-4 col-lg-5">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-white border-0 pt-4 px-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-user me-2 text-primary"></i>
                            Customer Profile
                        </h5>

                        <span class="badge bg-success-subtle text-success">
                            Active
                        </span>

                    </div>

                </div>

                <div class="card-body px-4">

                    {{-- Avatar --}}
                    <div class="text-center mb-4">

                        <div
                            class="rounded-circle bg-primary bg-opacity-10
                                   d-inline-flex align-items-center justify-content-center"
                            style="width:90px;height:90px;"
                        >
                            <span
                                class="fw-bold text-primary"
                                style="font-size:34px;"
                            >
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </span>
                        </div>

                        <h4 class="fw-bold mt-3 mb-1">
                            {{ $user->name ?? 'N/A' }}
                        </h4>

                        <div class="text-muted">
                            Customer #{{ $user->id }}
                        </div>

                    </div>


                    {{-- Information --}}
                    <div class="border-top pt-3">

                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">
                                <i class="fas fa-user me-2"></i>
                                Name
                            </span>

                            <strong>
                                {{ $user->name ?? 'N/A' }}
                            </strong>
                        </div>


                        <div class="d-flex justify-content-between py-2">

                            <span class="text-muted">
                                <i class="fas fa-envelope me-2"></i>
                                Email
                            </span>

                            <span class="text-end">
                                {{ $user->email ?? 'N/A' }}
                            </span>

                        </div>


                        <div class="d-flex justify-content-between py-2">

                            <span class="text-muted">
                                <i class="fas fa-phone me-2"></i>
                                Phone
                            </span>

                            <span>
                                {{ $user->phone ?? 'Not provided' }}
                            </span>

                        </div>


                        <div class="d-flex justify-content-between py-2">

                            <span class="text-muted">
                                <i class="fas fa-calendar me-2"></i>
                                Registered
                            </span>

                            <span>
                                {{ optional($user->created_at)->format('d M Y') }}
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Statistics --}}
        <div class="col-xl-8 col-lg-7">

            <div class="row g-3">

                {{-- Total Orders --}}
                <div class="col-md-6 col-xl-6">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center">

                                <div>
                                    <div class="text-muted small">
                                        Total Orders
                                    </div>

                                    <div class="fs-3 fw-bold">
                                        {{ number_format($totalOrders ?? 0) }}
                                    </div>
                                </div>

                                <div
                                    class="rounded-circle bg-primary bg-opacity-10
                                           text-primary d-flex align-items-center justify-content-center"
                                    style="width:50px;height:50px;"
                                >
                                    <i class="fas fa-shopping-bag"></i>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Completed --}}
                <div class="col-md-6 col-xl-6">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center">

                                <div>
                                    <div class="text-muted small">
                                        Completed Orders
                                    </div>

                                    <div class="fs-3 fw-bold text-success">
                                        {{ number_format($completedOrders ?? 0) }}
                                    </div>
                                </div>

                                <div
                                    class="rounded-circle bg-success bg-opacity-10
                                           text-success d-flex align-items-center justify-content-center"
                                    style="width:50px;height:50px;"
                                >
                                    <i class="fas fa-check-circle"></i>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Pending --}}
                <div class="col-md-6 col-xl-6">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center">

                                <div>
                                    <div class="text-muted small">
                                        Active / Pending Orders
                                    </div>

                                    <div class="fs-3 fw-bold text-warning">
                                        {{ number_format($pendingOrders ?? 0) }}
                                    </div>
                                </div>

                                <div
                                    class="rounded-circle bg-warning bg-opacity-10
                                           text-warning d-flex align-items-center justify-content-center"
                                    style="width:50px;height:50px;"
                                >
                                    <i class="fas fa-clock"></i>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Cancelled --}}
                <div class="col-md-6 col-xl-6">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center">

                                <div>
                                    <div class="text-muted small">
                                        Cancelled Orders
                                    </div>

                                    <div class="fs-3 fw-bold text-danger">
                                        {{ number_format($cancelledOrders ?? 0) }}
                                    </div>
                                </div>

                                <div
                                    class="rounded-circle bg-danger bg-opacity-10
                                           text-danger d-flex align-items-center justify-content-center"
                                    style="width:50px;height:50px;"
                                >
                                    <i class="fas fa-times-circle"></i>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Total Spent --}}
                <div class="col-md-6">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <div class="text-muted small mb-1">
                                Total Spent
                            </div>

                            <div class="fs-4 fw-bold text-primary">
                                ৳{{ number_format((float) ($totalSpent ?? 0), 2) }}
                            </div>

                        </div>

                    </div>

                </div>


                {{-- Paid --}}
                <div class="col-md-6">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <div class="text-muted small mb-1">
                                Paid Amount
                            </div>

                            <div class="fs-4 fw-bold text-success">
                                ৳{{ number_format((float) ($paidAmount ?? 0), 2) }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
         LATEST ORDER
    ============================================================= --}}

    @if($latestOrder)

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-0 pt-4 px-4">

                <h5 class="fw-bold mb-0">
                    <i class="fas fa-star text-warning me-2"></i>
                    Latest Order
                </h5>

            </div>

            <div class="card-body px-4">

                <div class="row g-3 align-items-center">

                    <div class="col-md-3">

                        <div class="text-muted small">
                            Order ID
                        </div>

                        <strong>
                            #{{ $latestOrder->id }}
                        </strong>

                    </div>


                    <div class="col-md-3">

                        <div class="text-muted small">
                            Amount
                        </div>

                        <strong>
                            ৳{{ number_format((float) ($latestOrder->total_amount ?? 0), 2) }}
                        </strong>

                    </div>


                    <div class="col-md-3">

                        <div class="text-muted small">
                            Status
                        </div>

                        @php
                            $orderStatus = strtolower(
                                (string) ($latestOrder->status ?? 'pending')
                            );

                            $statusClass = match ($orderStatus) {
                                'delivered', 'completed' => 'success',
                                'cancelled' => 'danger',
                                'processing', 'confirmed', 'shipped' => 'warning',
                                default => 'secondary',
                            };
                        @endphp

                        <span class="badge bg-{{ $statusClass }}">
                            {{ ucfirst($orderStatus) }}
                        </span>

                    </div>


                    <div class="col-md-3">

                        <div class="text-muted small">
                            Date
                        </div>

                        <strong>
                            {{ optional($latestOrder->created_at)->format('d M Y, h:i A') }}
                        </strong>

                    </div>

                </div>

            </div>

        </div>

    @endif


    {{-- ============================================================
         ORDER HISTORY
    ============================================================= --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 pt-4 px-4">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="fw-bold mb-1">
                        <i class="fas fa-shopping-cart text-primary me-2"></i>
                        Order History
                    </h5>

                    <div class="text-muted small">
                        All orders placed by this customer
                    </div>
                </div>

                <span class="badge bg-primary">
                    {{ number_format($totalOrders ?? 0) }} Orders
                </span>

            </div>

        </div>


        <div class="card-body p-0">

            @if($orders->count())

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th class="px-4">
                                    Order
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Amount
                                </th>

                                <th>
                                    Payment
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-end px-4">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($orders as $order)

                                @php

                                    $status = strtolower(
                                        (string) ($order->status ?? 'pending')
                                    );

                                    $statusClass = match ($status) {
                                        'delivered', 'completed' => 'success',
                                        'cancelled' => 'danger',
                                        'processing', 'confirmed', 'shipped' => 'warning',
                                        default => 'secondary',
                                    };

                                    $paymentStatus = strtolower(
                                        (string) ($order->payment_status ?? 'pending')
                                    );

                                    $paymentClass = match ($paymentStatus) {
                                        'paid' => 'success',
                                        'failed' => 'danger',
                                        'refunded' => 'info',
                                        default => 'warning',
                                    };

                                @endphp

                                <tr>

                                    <td class="px-4">

                                        <div class="fw-bold">
                                            #{{ $order->id }}
                                        </div>

                                        @if(!empty($order->payment_method))

                                            <div class="small text-muted">
                                                {{ $order->payment_method }}
                                            </div>

                                        @endif

                                    </td>


                                    <td>

                                        <div>
                                            {{ optional($order->created_at)->format('d M Y') }}
                                        </div>

                                        <small class="text-muted">
                                            {{ optional($order->created_at)->format('h:i A') }}
                                        </small>

                                    </td>


                                    <td>

                                        <strong>
                                            ৳{{ number_format((float) ($order->total_amount ?? 0), 2) }}
                                        </strong>

                                    </td>


                                    <td>

                                        <span class="badge bg-{{ $paymentClass }}">
                                            {{ ucfirst($paymentStatus) }}
                                        </span>

                                    </td>


                                    <td>

                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ ucfirst($status) }}
                                        </span>

                                    </td>


                                    <td class="text-end px-4">

                                        <a
                                            href="{{ route('admin.orders.show', $order) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            title="View Order"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="text-center py-5">

                    <div
                        class="rounded-circle bg-light
                               d-inline-flex align-items-center justify-content-center mb-3"
                        style="width:80px;height:80px;"
                    >
                        <i class="fas fa-shopping-bag fa-2x text-muted"></i>
                    </div>

                    <h5 class="fw-bold">
                        No Orders Found
                    </h5>

                    <p class="text-muted mb-0">
                        This customer has not placed any orders yet.
                    </p>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection