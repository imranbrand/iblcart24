{{-- Courier (Steadfast) card. Included by admin/orders/show.blade.php --}}
@php
    $steadfastReady = app(\App\Services\SteadfastService::class)->enabled();
@endphp

<div class="action-card mt-4 mb-4 no-print">

    <div class="action-body p-4">

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <h5 class="fw-bold mb-0">
                <i class="fa-solid fa-truck-fast me-2"></i>
                Courier
            </h5>

            @if($order->courier_status)
                <span class="badge bg-info text-dark">
                    {{ ucwords(str_replace('_', ' ', $order->courier_status)) }}
                </span>
            @endif
        </div>

        @if($order->courier_consignment_id)

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="text-muted small">Courier</div>
                    <div class="fw-semibold">{{ $order->courier_name ?: 'Steadfast' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Consignment ID</div>
                    <div class="fw-semibold"><code>{{ $order->courier_consignment_id }}</code></div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Tracking code</div>
                    <div class="fw-semibold"><code>{{ $order->courier_tracking_code ?: '-' }}</code></div>
                </div>
            </div>

            @if($order->courier_sent_at)
                <p class="text-muted small mb-3">
                    Sent on {{ $order->courier_sent_at->format('d M Y, h:i A') }}
                </p>
            @endif

            @if($steadfastReady)
                <form method="POST" action="{{ route('admin.orders.courier.refresh', $order) }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="fa-solid fa-rotate me-1"></i>
                        Refresh courier status
                    </button>
                </form>
            @endif

        @elseif($order->isCancelled())

            <p class="text-muted mb-0 small">A cancelled order cannot be sent to a courier.</p>

        @elseif(! $steadfastReady)

            <p class="text-muted mb-0 small">
                Steadfast is not connected yet. Add <code>STEADFAST_ENABLED=true</code>,
                <code>STEADFAST_API_KEY</code> and <code>STEADFAST_SECRET_KEY</code> to your
                <code>.env</code> file to turn this on.
            </p>

        @else

            <p class="text-muted small mb-3">
                Creates a Steadfast parcel with the customer's name, phone and address.
                @if(strtolower((string) $order->payment_status) === 'paid')
                    This order is already paid, so the courier will collect Tk 0.
                @else
                    The courier will collect Tk {{ number_format((float) ($order->total ?: $order->total_amount), 0) }}.
                @endif
            </p>

            <form
                method="POST"
                action="{{ route('admin.orders.courier.send', $order) }}"
                onsubmit="return confirm('Send Order #{{ $order->id }} to Steadfast now?');"
            >
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane me-1"></i>
                    Send to Steadfast
                </button>
            </form>

        @endif

    </div>

</div>
