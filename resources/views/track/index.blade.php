@extends('layouts.storefront')

@section('title', 'Track Order')

@section('content')
@php
    $steps = ['pending' => 'Order placed', 'confirmed' => 'Confirmed', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered'];
    $current = strtolower((string) ($order->status ?? 'pending'));
    $keys = array_keys($steps);
    $currentIndex = array_search($current === 'completed' ? 'delivered' : $current, $keys, true);
@endphp
<div class="container py-5" style="max-width: 760px;">
    <div class="text-center mb-4">
        <h1 class="fw-bold mb-1">Track your order</h1>
        <p class="text-muted mb-0">Enter your order number and the phone number you used at checkout.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('track.order.lookup') }}" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Order number</label>
                    <input type="number" name="order_id" min="1" value="{{ old('order_id', $order->id ?? '') }}" class="form-control form-control-lg @error('order_id') is-invalid @enderror" placeholder="e.g. 105" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Phone number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control form-control-lg @error('phone') is-invalid @enderror" placeholder="01XXXXXXXXX" required>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-dark btn-lg w-100 rounded-3" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Track</button>
                </div>
                @error('order_id')<div class="col-12"><div class="text-danger small">{{ $message }}</div></div>@enderror
                @error('phone')<div class="col-12"><div class="text-danger small">{{ $message }}</div></div>@enderror
            </form>
        </div>
    </div>

    @if($order)
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between flex-wrap gap-2 mb-4">
                    <div>
                        <div class="text-muted small">Order</div>
                        <div class="h4 fw-bold mb-0">#{{ $order->id }}</div>
                    </div>
                    <div class="text-md-end">
                        <div class="text-muted small">Total</div>
                        <div class="h4 fw-bold mb-0">৳{{ number_format((float) ($order->total_amount ?? $order->total ?? 0), 2) }}</div>
                    </div>
                </div>

                @if($current === 'cancelled')
                    <div class="alert alert-danger mb-4"><i class="fa-solid fa-ban me-2"></i>This order was cancelled.</div>
                @else
                    <div class="d-flex justify-content-between mb-4" style="gap:6px;">
                        @foreach($steps as $key => $label)
                            @php $done = $currentIndex !== false && array_search($key, $keys, true) <= $currentIndex; @endphp
                            <div class="text-center flex-fill">
                                <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle"
                                     style="width:38px;height:38px;background:{{ $done ? 'linear-gradient(135deg,#4f46e5,#7c3aed)' : '#e5e7eb' }};color:{{ $done ? '#fff' : '#9ca3af' }};">
                                    <i class="fa-solid {{ $done ? 'fa-check' : 'fa-circle' }}" style="font-size:{{ $done ? '14px' : '6px' }}"></i>
                                </div>
                                <div class="small {{ $done ? 'fw-semibold' : 'text-muted' }}">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="row g-3 small mb-3">
                    <div class="col-sm-6"><span class="text-muted">Placed:</span> {{ optional($order->created_at)->format('d M Y, h:i A') }}</div>
                    <div class="col-sm-6"><span class="text-muted">Payment:</span> {{ $order->payment_method ?? 'N/A' }} ({{ $order->payment_status ?? 'Pending' }})</div>
                    <div class="col-12"><span class="text-muted">Deliver to:</span> {{ $order->customer_name }}, {{ $order->address }}</div>
                </div>

                <hr>
                <div class="fw-semibold mb-2">Items</div>
                @foreach($order->items as $item)
                    <div class="d-flex justify-content-between py-1">
                        <span>{{ $item->product_name }} <span class="text-muted">× {{ $item->quantity }}</span></span>
                        <span>৳{{ number_format((float) $item->price * (int) $item->quantity, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection