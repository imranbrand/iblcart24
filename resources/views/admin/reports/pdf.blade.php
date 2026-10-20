<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>
        IBL CART 24 - Business Report
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #111827;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .logo {
            font-size: 25px;
            font-weight: bold;
            color: #111827;
        }

        .subtitle {
            color: #6b7280;
            margin-top: 5px;
            font-size: 11px;
        }

        .date {
            margin-top: 10px;
            font-size: 11px;
            font-weight: bold;
        }

        .summary {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .summary td {
            width: 25%;
            padding: 4px;
            vertical-align: top;
        }

        .card {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: center;
        }

        .card-title {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .card-value {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 18px;
            margin-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 6px;
        }

        table.orders {
            width: 100%;
            border-collapse: collapse;
        }

        table.orders th {
            background: #111827;
            color: white;
            padding: 7px 5px;
            text-align: left;
            font-size: 8px;
        }

        table.orders td {
            border-bottom: 1px solid #e5e7eb;
            padding: 7px 5px;
            font-size: 8px;
            vertical-align: top;
        }

        .status {
            font-weight: bold;
            text-transform: capitalize;
        }

        .total {
            font-weight: bold;
            text-align: right;
        }

        .money {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #6b7280;
            font-size: 8px;
        }

        .small {
            font-size: 8px;
            color: #6b7280;
        }
    </style>

</head>

<body>

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="header">

        <div class="logo">
            IBL CART 24
        </div>

        <div class="subtitle">
            E-Commerce Business Report
        </div>

        <div class="date">

            Report Period:

            {{ \Carbon\Carbon::parse($from)->format('d M Y') }}

            -

            {{ \Carbon\Carbon::parse($to)->format('d M Y') }}

        </div>

    </div>


    {{-- =========================================================
         MAIN SUMMARY
    ========================================================== --}}

    <table class="summary">

        <tr>

            <td>

                <div class="card">

                    <div class="card-title">
                        Total Sales
                    </div>

                    <div class="card-value">
                        Tk {{ number_format($totalSales ?? 0, 2) }}
                    </div>

                </div>

            </td>


            <td>

                <div class="card">

                    <div class="card-title">
                        Total Orders
                    </div>

                    <div class="card-value">
                        {{ $totalOrders ?? 0 }}
                    </div>

                </div>

            </td>


            <td>

                <div class="card">

                    <div class="card-title">
                        Active Orders
                    </div>

                    <div class="card-value">
                        {{ $activeOrders ?? 0 }}
                    </div>

                </div>

            </td>


            <td>

                <div class="card">

                    <div class="card-title">
                        Average Order
                    </div>

                    <div class="card-value">
                        Tk {{ number_format($averageOrderValue ?? 0, 2) }}
                    </div>

                </div>

            </td>

        </tr>

    </table>


    {{-- =========================================================
         ORDER STATUS
    ========================================================== --}}

    <div class="section-title">
        Order Status Summary
    </div>

    <table class="summary">

        <tr>

            <td>

                <div class="card">

                    <div class="card-title">
                        Pending
                    </div>

                    <div class="card-value">
                        {{ $pendingOrders ?? 0 }}
                    </div>

                </div>

            </td>


            <td>

                <div class="card">

                    <div class="card-title">
                        Confirmed
                    </div>

                    <div class="card-value">
                        {{ $confirmedOrders ?? 0 }}
                    </div>

                </div>

            </td>


            <td>

                <div class="card">

                    <div class="card-title">
                        Processing
                    </div>

                    <div class="card-value">
                        {{ $processingOrders ?? 0 }}
                    </div>

                </div>

            </td>


            <td>

                <div class="card">

                    <div class="card-title">
                        Shipped
                    </div>

                    <div class="card-value">
                        {{ $shippedOrders ?? 0 }}
                    </div>

                </div>

            </td>

        </tr>


        <tr>

            <td>

                <div class="card">

                    <div class="card-title">
                        Delivered
                    </div>

                    <div class="card-value">
                        {{ $deliveredOrders ?? 0 }}
                    </div>

                </div>

            </td>


            <td>

                <div class="card">

                    <div class="card-title">
                        Cancelled
                    </div>

                    <div class="card-value">
                        {{ $cancelledOrders ?? 0 }}
                    </div>

                </div>

            </td>


            <td>

                <div class="card">

                    <div class="card-title">
                        Paid Orders
                    </div>

                    <div class="card-value">
                        {{ $paidOrders ?? 0 }}
                    </div>

                </div>

            </td>


            <td>

                <div class="card">

                    <div class="card-title">
                        Pending Payments
                    </div>

                    <div class="card-value">
                        {{ $pendingPayments ?? 0 }}
                    </div>

                </div>

            </td>

        </tr>

    </table>


    {{-- =========================================================
         ORDERS REPORT
    ========================================================== --}}

    <div class="section-title">
        Orders Report
    </div>


    <table class="orders">

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

            </tr>

        </thead>


        <tbody>

            @forelse($orders as $order)

                @php

                    $customerName =
                        $order->customer_name
                        ?? optional($order->user)->name
                        ?? 'Guest';

                    $customerEmail =
                        optional($order->user)->email
                        ?? '';

                    $paymentMethod =
                        $order->payment_method
                        ?? '-';

                    $paymentStatus =
                        $order->payment_status
                        ?? 'Pending';

                    $orderStatus =
                        $order->status
                        ?? 'Pending';

                    $totalAmount =
                        $order->total_amount
                        ?? $order->total
                        ?? 0;

                @endphp


                <tr>

                    {{-- Order ID --}}

                    <td>

                        <strong>
                            #{{ $order->id }}
                        </strong>

                    </td>


                    {{-- Customer --}}

                    <td>

                        <strong>
                            {{ $customerName }}
                        </strong>

                        @if($customerEmail)

                            <br>

                            <span class="small">
                                {{ $customerEmail }}
                            </span>

                        @endif


                        @if($order->phone)

                            <br>

                            <span class="small">
                                {{ $order->phone }}
                            </span>

                        @endif

                    </td>


                    {{-- Payment --}}

                    <td>

                        {{ ucfirst(
                            str_replace(
                                '_',
                                ' ',
                                $paymentMethod
                            )
                        ) }}

                        <br>

                        <span class="small">

                            {{ ucfirst(
                                strtolower(
                                    $paymentStatus
                                )
                            ) }}

                        </span>

                    </td>


                    {{-- Order Status --}}

                    <td class="status">

                        {{ ucfirst(
                            strtolower(
                                $orderStatus
                            )
                        ) }}

                    </td>


                    {{-- Total --}}

                    <td class="total">

                        Tk {{ number_format(
                            (float) $totalAmount,
                            2
                        ) }}

                    </td>


                    {{-- Date --}}

                    <td>

                        @if($order->created_at)

                            {{ $order->created_at->format(
                                'd M Y'
                            ) }}

                            <br>

                            <span class="small">

                                {{ $order->created_at->format(
                                    'h:i A'
                                ) }}

                            </span>

                        @else

                            -

                        @endif

                    </td>

                </tr>


            @empty

                <tr>

                    <td
                        colspan="6"
                        class="center"
                        style="padding:20px;"
                    >

                        No orders found for this period.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- =========================================================
         FOOTER
    ========================================================== --}}

    <div class="footer">

        IBL CART 24 &copy; {{ date('Y') }}

        <br>

        Shop Smart. Shop Premium.

        <br>

        Generated on
        {{ now()->format('d M Y, h:i A') }}

    </div>

</body>

</html>