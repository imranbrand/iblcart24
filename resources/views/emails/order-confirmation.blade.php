<!DOCTYPE html>
<html>
<body style="font-family:Arial,sans-serif;background:#f4f6f8;padding:20px;color:#222;">
<div style="max-width:600px;margin:auto;background:#fff;border-radius:12px;padding:28px;">

    <h2 style="margin-top:0;color:#0d6efd;">Thank you for your order!</h2>
    <p>Hi {{ $order->customer_name }}, we have received your order <strong>#{{ $order->id }}</strong>.</p>

    <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;margin:16px 0;">
        <tr style="background:#f1f3f5;text-align:left;">
            <th>Product</th><th>Qty</th><th style="text-align:right;">Subtotal</th>
        </tr>
        @foreach($order->items as $item)
            <tr style="border-bottom:1px solid #eee;">
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td style="text-align:right;">৳{{ number_format($item->subtotal, 2) }}</td>
            </tr>
        @endforeach
    </table>

    <p style="text-align:right;line-height:1.7;">
        Subtotal: ৳{{ number_format($order->subtotal, 2) }}<br>
        Delivery: ৳{{ number_format($order->delivery_charge, 2) }}<br>
        @if((float) $order->discount_amount > 0)
            Discount ({{ $order->coupon_code }}): -৳{{ number_format($order->discount_amount, 2) }}<br>
        @endif
        <strong style="font-size:18px;">Total: ৳{{ number_format($order->total, 2) }}</strong>
    </p>

    <hr style="border:none;border-top:1px solid #eee;">

    <p>
        <strong>Payment:</strong> {{ $order->payment_method }} ({{ $order->payment_status }})<br>
        <strong>Deliver to:</strong> {{ $order->address }}<br>
        <strong>Phone:</strong> {{ $order->phone }}
    </p>

    <p style="text-align:center;margin-top:24px;">
        <a href="{{ route('orders.show', $order) }}"
           style="background:#0d6efd;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;">
            View Order
        </a>
    </p>

    <p style="color:#888;font-size:12px;text-align:center;">IBL CART 24</p>
</div>
</body>
</html>
