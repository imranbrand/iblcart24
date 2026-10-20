<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Invoice {{ $number }}</title>
<style>
    @page { margin: 32px 36px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
    h1 { font-size: 22px; margin: 0; color: #111827; }
    .muted { color: #6b7280; }
    .right { text-align: right; }
    .box { border: 1px solid #e5e7eb; padding: 10px 12px; }
    table { width: 100%; border-collapse: collapse; }
    .items th { background: #111827; color: #ffffff; padding: 8px; text-align: left; font-size: 10px; }
    .items td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
    .totals td { padding: 4px 8px; }
    .grand td { font-size: 14px; font-weight: bold; border-top: 2px solid #111827; padding-top: 8px; }
    .stamp { display: inline-block; border: 2px solid #059669; color: #059669; padding: 3px 12px; font-weight: bold; font-size: 14px; }
    .stamp.unpaid { border-color: #d97706; color: #d97706; }
    .footer { margin-top: 30px; text-align: center; color: #6b7280; font-size: 10px; }
</style>
</head>
<body>

@php
    $paid = strtolower((string) $order->payment_status) === 'paid';
    $money = fn ($v) => 'Tk ' . number_format((float) $v, 2);
    $subtotal = (float) ($order->subtotal ?? 0);
    $delivery = (float) ($order->delivery_charge ?? 0);
    $discount = (float) ($order->discount_amount ?? 0);
    $grand = (float) ($order->total ?? $order->total_amount ?? 0);
@endphp

<table>
    <tr>
        <td style="width:60%; vertical-align: top;">
            <h1>{{ config('shop.name') }}</h1>
            <div class="muted" style="margin-top:4px;">
                @if(config('shop.address')){{ config('shop.address') }}<br>@endif
                @if(config('shop.phone'))Phone: {{ config('shop.phone') }}<br>@endif
                @if(config('shop.email')){{ config('shop.email') }}@endif
            </div>
        </td>
        <td class="right" style="vertical-align: top;">
            <div style="font-size:18px; font-weight:bold;">INVOICE</div>
            <div class="muted">No: INV-{{ $number }}</div>
            <div class="muted">Order: #{{ $order->id }}</div>
            <div class="muted">Date: {{ optional($order->created_at)->format('d M Y') }}</div>
            <div style="margin-top:8px;">
                <span class="stamp {{ $paid ? '' : 'unpaid' }}">{{ $paid ? 'PAID' : 'UNPAID' }}</span>
            </div>
        </td>
    </tr>
</table>

<table style="margin-top:18px;">
    <tr>
        <td style="width:50%; vertical-align: top; padding-right:8px;">
            <div class="box">
                <div class="muted" style="font-size:9px; text-transform:uppercase;">Bill / Ship to</div>
                <div style="font-weight:bold; margin-top:3px;">{{ $order->customer_name }}</div>
                <div>{{ $order->phone }}</div>
                <div>{{ $order->address }}</div>
            </div>
        </td>
        <td style="width:50%; vertical-align: top; padding-left:8px;">
            <div class="box">
                <div class="muted" style="font-size:9px; text-transform:uppercase;">Payment</div>
                <div style="margin-top:3px;">Method: {{ $order->payment_method }}</div>
                <div>Status: {{ ucfirst((string) ($order->payment_status ?? 'pending')) }}</div>
                @if($order->payment_transaction_id)
                    <div>Transaction ID: {{ $order->payment_transaction_id }}</div>
                @endif
                <div>Order status: {{ ucfirst((string) $order->status) }}</div>
            </div>
        </td>
    </tr>
</table>

<table class="items" style="margin-top:18px;">
    <thead>
        <tr>
            <th style="width:5%;">#</th>
            <th>Item</th>
            <th style="width:12%;" class="right">Qty</th>
            <th style="width:18%;" class="right">Price</th>
            <th style="width:18%;" class="right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->product_name ?: 'Product #' . $item->product_id }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">{{ $money($item->price) }}</td>
                <td class="right">{{ $money($item->subtotal) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table style="margin-top:12px;">
    <tr>
        <td style="width:55%;"></td>
        <td>
            <table class="totals">
                <tr><td>Subtotal</td><td class="right">{{ $money($subtotal) }}</td></tr>
                <tr><td>Delivery</td><td class="right">{{ $money($delivery) }}</td></tr>
                @if($discount > 0)
                    <tr><td>Discount{{ $order->coupon_code ? ' (' . $order->coupon_code . ')' : '' }}</td><td class="right">- {{ $money($discount) }}</td></tr>
                @endif
                <tr class="grand"><td>Total</td><td class="right">{{ $money($grand) }}</td></tr>
            </table>
        </td>
    </tr>
</table>

@if($order->note)
    <div class="box" style="margin-top:14px;">
        <span class="muted">Note:</span> {{ $order->note }}
    </div>
@endif

<div class="footer">
    Thank you for shopping with {{ config('shop.name') }}.<br>
    This is a computer-generated invoice.
</div>

</body>
</html>
