@extends('layouts.storefront')

@section('title', 'My Orders - IBL CART 24')

@section('content')

<style>
    .orders-page {
        min-height: calc(100vh - 100px);
        padding: 42px 0 80px;
        background:
            radial-gradient(circle at top right, rgba(79,70,229,.07), transparent 30%),
            #f8fafc;
    }

    .orders-hero {
        border-radius: 24px;
        padding: 30px;
        color: #fff;
        background: linear-gradient(135deg, #111827 0%, #312e81 100%);
        box-shadow: 0 18px 45px rgba(15,23,42,.14);
        margin-bottom: 24px;
    }

    .orders-hero h1 {
        font-size: 30px;
        font-weight: 850;
        margin: 0 0 7px;
    }

    .orders-hero p {
        margin: 0;
        color: rgba(255,255,255,.75);
    }

    .summary-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 20px;
        height: 100%;
        box-shadow: 0 8px 28px rgba(15,23,42,.05);
    }

    .summary-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    .summary-value {
        color: #111827;
        font-size: 25px;
        font-weight: 850;
        margin-top: 5px;
    }

    .orders-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(15,23,42,.05);
    }

    .orders-card-header {
        padding: 20px 22px;
        border-bottom: 1px solid #eef2f7;
    }

    .orders-card-header h2 {
        margin: 0;
        font-size: 19px;
        font-weight: 850;
        color: #111827;
    }

    .order-row {
        padding: 22px;
        border-bottom: 1px solid #eef2f7;
        transition: background .2s ease;
    }

    .order-row:last-child {
        border-bottom: 0;
    }

    .order-row:hover {
        background: #fafbff;
    }

    .order-id {
        font-size: 16px;
        font-weight: 850;
        color: #111827;
    }

    .order-date {
        color: #64748b;
        font-size: 12px;
        margin-top: 4px;
    }

    .order-total {
        font-size: 18px;
        font-weight: 850;
        color: #111827;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 11px;
        font-size: 11px;
        font-weight: 800;
        text-transform: capitalize;
    }

    .status-pending {
        background: #fff7ed;
        color: #c2410c;
    }

    .status-confirmed,
    .status-processing {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .status-shipped {
        background: #eef2ff;
        color: #4338ca;
    }

    .status-delivered {
        background: #ecfdf5;
        color: #047857;
    }

    .status-cancelled {
        background: #fef2f2;
        color: #b91c1c;
    }

    .payment-paid {
        color: #047857;
        font-weight: 800;
    }

    .payment-pending {
        color: #c2410c;
        font-weight: 800;
    }

    .payment-failed,
    .payment-refunded {
        color: #b91c1c;
        font-weight: 800;
    }

    .btn-view-order {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border-radius: 10px;
        padding: 10px 14px;
        background: #111827;
        color: #fff;
        text-decoration: none;
        font-size: 12px;
        font-weight: 800;
    }

    .btn-view-order:hover {
        background: #000;
        color: #fff;
    }

    .empty-orders {
        padding: 70px 25px;
        text-align: center;
    }

    .empty-icon {
        width: 82px;
        height: 82px;
        margin: 0 auto 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #64748b;
        font-size: 30px;
    }

    .empty-orders h3 {
        font-size: 21px;
        font-weight: 850;
        color: #111827;
    }

    .empty-orders p {
        color: #64748b;
        max-width: 480px;
        margin: 0 auto 22px;
    }

    .btn-shop {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 10px;
        padding: 11px 18px;
        background: #111827;
        color: #fff;
        text-decoration: none;
        font-weight: 800;
    }

    .btn-shop:hover {
        background: #000;
        color: #fff;
    }

    @media (max-width: 767.98px) {
        .orders-page {
            padding-top: 25px;
        }

        .orders-hero {
            padding: 24px;
        }

        .orders-hero h1 {
            font-size: 25px;
        }

        .order-row {
            padding: 18px;
        }
    }
</style>

<div class="orders-page">
    <div class="container">

        <div class="orders-hero">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h1>My Orders</h1>
                    <p>Track your purchases, payment status and order history in one place.</p>
                </div>

                <a href="{{ route('products.index') }}" class="btn btn-light fw-bold px-3">
                    <i class="fa-solid fa-bag-shopping me-1"></i>
                    Continue Shopping
                </a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-lg">
                <div class="summary-card">
                    <div class="summary-label">Total Orders</div>
                    <div class="summary-value">{{ $totalOrders }}</div>
                </div>
            </div>

            <div class="col-6 col-lg">
                <div class="summary-card">
                    <div class="summary-label">Pending</div>
                    <div class="summary-value">{{ $pendingOrders }}</div>
                </div>
            </div>

            <div class="col-6 col-lg">
                <div class="summary-card">
                    <div class="summary-label">Processing</div>
                    <div class="summary-value">{{ $processingOrders }}</div>
                </div>
            </div>

            <div class="col-6 col-lg">
                <div class="summary-card">
                    <div class="summary-label">Delivered</div>
                    <div class="summary-value">{{ $deliveredOrders }}</div>
                </div>
            </div>

            <div class="col-12 col-lg">
                <div class="summary-card">
                    <div class="summary-label">Total Spent</div>
                    <div class="summary-value">৳{{ number_format((float) $totalSpent, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="orders-card">

            <div class="orders-card-header">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                    <h2>Order History</h2>

                    <span class="text-muted small">
                        {{ $orders->total() }} order(s)
                    </span>
                </div>
            </div>

            @forelse($orders as $order)

                @php
                    $status = strtolower((string) ($order->status ?? 'pending'));
                    $paymentStatus = strtolower((string) ($order->payment_status ?? 'pending'));
                    $total = (float) ($order->total_amount ?? $order->total ?? 0);
                    $itemCount = $order->items->sum('quantity');
                @endphp

                <div class="order-row">

                    <div class="row align-items-center g-3">

                        <div class="col-12 col-md-3">
                            <div class="order-id">
                                Order #{{ $order->id }}
                            </div>

                            <div class="order-date">
                                <i class="fa-regular fa-calendar me-1"></i>
                                {{ optional($order->created_at)->format('d M Y, h:i A') }}
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <div class="text-muted small mb-1">Items</div>
                            <div class="fw-bold">
                                {{ $itemCount }}
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <div class="text-muted small mb-1">Total</div>
                            <div class="order-total">
                                ৳{{ number_format($total, 2) }}
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <div class="text-muted small mb-1">Order Status</div>
                            <span class="status-badge status-{{ $status }}">
                                <i class="fa-solid fa-circle" style="font-size:6px;"></i>
                                {{ ucfirst($status) }}
                            </span>
                        </div>

                        <div class="col-6 col-md-2">
                            <div class="text-muted small mb-1">Payment</div>
                            <div class="
                                @if($paymentStatus === 'paid') payment-paid
                                @elseif($paymentStatus === 'pending') payment-pending
                                @else payment-{{ $paymentStatus }}
                                @endif
                            ">
                                {{ ucfirst($paymentStatus) }}
                            </div>
                        </div>

                        <div class="col-12 col-md-1 text-md-end">
                            <a
                                href="{{ route('orders.show', $order) }}"
                                class="btn-view-order w-100"
                                title="View order"
                            >
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>

                    </div>

                </div>

            @empty

                <div class="empty-orders">
                    <div class="empty-icon">
                        <i class="fa-solid fa-receipt"></i>
                    </div>

                    <h3>No orders yet</h3>

                    <p>
                        You have not placed an order yet. Explore our products
                        and find something you love.
                    </p>

                    <a href="{{ route('products.index') }}" class="btn-shop">
                        <i class="fa-solid fa-bag-shopping"></i>
                        Start Shopping
                    </a>
                </div>

            @endforelse

            @if($orders->hasPages())
                <div class="p-4 border-top">
                    {{ $orders->links() }}
                </div>
            @endif

        </div>

    </div>
</div>

@endsection