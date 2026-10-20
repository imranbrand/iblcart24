<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportExportController extends Controller
{
    /**
     * PDF EXPORT
     */
    public function pdf(Request $request)
    {
        $from = $request->input(
            'from',
            now()->startOfMonth()->format('Y-m-d')
        );

        $to = $request->input(
            'to',
            now()->format('Y-m-d')
        );

        $orders = Order::with('user')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->latest()
            ->get();

        $totalOrders = $orders->count();

        $totalSales = $orders
            ->where('status', '!=', 'cancelled')
            ->sum(function ($order) {
                return (float) (
                    $order->total_amount
                    ?? $order->total
                    ?? 0
                );
            });

        $deliveredOrders = $orders
            ->where('status', 'delivered')
            ->count();

        $cancelledOrders = $orders
            ->where('status', 'cancelled')
            ->count();

        $pendingOrders = $orders
            ->where('status', 'pending')
            ->count();

        $confirmedOrders = $orders
            ->where('status', 'confirmed')
            ->count();

        $processingOrders = $orders
            ->where('status', 'processing')
            ->count();

        $shippedOrders = $orders
            ->where('status', 'shipped')
            ->count();

        $paidOrders = $orders
            ->where('payment_status', 'paid')
            ->count();

        $pendingPayments = $orders
            ->where('payment_status', 'pending')
            ->count();

        $failedPayments = $orders
            ->where('payment_status', 'failed')
            ->count();

        $refundedPayments = $orders
            ->where('payment_status', 'refunded')
            ->count();

        $pdf = Pdf::loadView(
            'admin.reports.pdf',
            compact(
                'from',
                'to',
                'orders',
                'totalOrders',
                'totalSales',
                'deliveredOrders',
                'cancelledOrders',
                'pendingOrders',
                'confirmedOrders',
                'processingOrders',
                'shippedOrders',
                'paidOrders',
                'pendingPayments',
                'failedPayments',
                'refundedPayments'
            )
        );

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download(
            'IBL-CART-24-Report-' .
            $from .
            '-to-' .
            $to .
            '.pdf'
        );
    }

    /**
     * EXCEL EXPORT
     *
     * Creates an Excel-compatible HTML .xls file.
     * No external Excel package is required.
     */
    public function excel(Request $request)
    {
        $from = $request->input(
            'from',
            now()->startOfMonth()->format('Y-m-d')
        );

        $to = $request->input(
            'to',
            now()->format('Y-m-d')
        );

        $orders = Order::with('user')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->latest()
            ->get();

        $fileName =
            'IBL-CART-24-Report-' .
            $from .
            '-to-' .
            $to .
            '.xls';

        $html = $this->generateExcelHtml(
            $orders,
            $from,
            $to
        );

        return response(
            $html,
            Response::HTTP_OK,
            [
                'Content-Type' =>
                    'application/vnd.ms-excel; charset=UTF-8',

                'Content-Disposition' =>
                    'attachment; filename="' .
                    $fileName .
                    '"',

                'Cache-Control' =>
                    'max-age=0, no-cache, no-store, must-revalidate',

                'Pragma' => 'no-cache',

                'Expires' => '0',
            ]
        );
    }

    /**
     * CSV EXPORT
     */
    public function csv(Request $request)
    {
        $from = $request->input(
            'from',
            now()->startOfMonth()->format('Y-m-d')
        );

        $to = $request->input(
            'to',
            now()->format('Y-m-d')
        );

        $orders = Order::with('user')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->latest()
            ->get();

        $fileName =
            'IBL-CART-24-Report-' .
            $from .
            '-to-' .
            $to .
            '.csv';

        $callback = function () use ($orders, $from, $to) {

            $file = fopen(
                'php://output',
                'w'
            );

            /**
             * UTF-8 BOM
             *
             * Helps Microsoft Excel correctly
             * display Bangla text.
             */
            fwrite(
                $file,
                "\xEF\xBB\xBF"
            );

            /**
             * Report Information
             */
            fputcsv(
                $file,
                [
                    'IBL CART 24'
                ]
            );

            fputcsv(
                $file,
                [
                    'Sales Report'
                ]
            );

            fputcsv(
                $file,
                [
                    'From',
                    $from
                ]
            );

            fputcsv(
                $file,
                [
                    'To',
                    $to
                ]
            );

            fputcsv(
                $file,
                []
            );

            /**
             * Table Header
             */
            fputcsv(
                $file,
                [
                    'Order ID',
                    'Customer Name',
                    'Phone',
                    'Email',
                    'Payment Method',
                    'Payment Status',
                    'Order Status',
                    'Total Amount',
                    'Order Date'
                ]
            );

            /**
             * Orders
             */
            foreach ($orders as $order) {

                $customerName =
                    $order->customer_name
                    ?? optional($order->user)->name
                    ?? 'Guest';

                $phone =
                    $order->phone
                    ?? '-';

                $email =
                    optional($order->user)->email
                    ?? '-';

                $paymentMethod =
                    $order->payment_method
                    ?? '-';

                $paymentStatus =
                    ucfirst(
                        $order->payment_status
                        ?? 'pending'
                    );

                $orderStatus =
                    ucfirst(
                        $order->status
                        ?? 'pending'
                    );

                $amount =
                    (float) (
                        $order->total_amount
                        ?? $order->total
                        ?? 0
                    );

                $date =
                    $order->created_at
                    ? $order->created_at->format(
                        'Y-m-d H:i:s'
                    )
                    : '-';

                fputcsv(
                    $file,
                    [
                        $order->id,
                        $customerName,
                        $phone,
                        $email,
                        $paymentMethod,
                        $paymentStatus,
                        $orderStatus,
                        number_format(
                            $amount,
                            2,
                            '.',
                            ''
                        ),
                        $date
                    ]
                );
            }

            fclose($file);
        };

        return response()->stream(
            $callback,
            Response::HTTP_OK,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',

                'Content-Disposition' =>
                    'attachment; filename="' .
                    $fileName .
                    '"',

                'Cache-Control' =>
                    'max-age=0, no-cache, no-store, must-revalidate',

                'Pragma' => 'no-cache',

                'Expires' => '0',
            ]
        );
    }

    /**
     * EXCEL HTML GENERATOR
     */
    private function generateExcelHtml(
        $orders,
        $from,
        $to
    ) {
        $totalOrders =
            $orders->count();

        $totalSales =
            $orders
                ->where('status', '!=', 'cancelled')
                ->sum(function ($order) {
                    return (float) (
                        $order->total_amount
                        ?? $order->total
                        ?? 0
                    );
                });

        $deliveredOrders =
            $orders
                ->where('status', 'delivered')
                ->count();

        $cancelledOrders =
            $orders
                ->where('status', 'cancelled')
                ->count();

        $pendingOrders =
            $orders
                ->where('status', 'pending')
                ->count();

        $confirmedOrders =
            $orders
                ->where('status', 'confirmed')
                ->count();

        $processingOrders =
            $orders
                ->where('status', 'processing')
                ->count();

        $shippedOrders =
            $orders
                ->where('status', 'shipped')
                ->count();

        $paidOrders =
            $orders
                ->where('payment_status', 'paid')
                ->count();

        $pendingPayments =
            $orders
                ->where('payment_status', 'pending')
                ->count();

        $failedPayments =
            $orders
                ->where('payment_status', 'failed')
                ->count();

        $refundedPayments =
            $orders
                ->where('payment_status', 'refunded')
                ->count();

        /**
         * HTML START
         */
        $html = '<!DOCTYPE html>';

        $html .= '<html>';

        $html .= '<head>';

        $html .= '
            <meta charset="UTF-8">

            <style>

                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                }

                .title {
                    font-size: 22px;
                    font-weight: bold;
                }

                .subtitle {
                    font-size: 16px;
                    font-weight: bold;
                }

                .summary {
                    background: #f3f4f6;
                    font-weight: bold;
                }

                table {
                    border-collapse: collapse;
                    width: 100%;
                }

                th {
                    background: #111827;
                    color: white;
                    font-weight: bold;
                }

                th,
                td {
                    border: 1px solid #999;
                    padding: 7px;
                    text-align: left;
                }

                .amount {
                    text-align: right;
                }

            </style>
        ';

        $html .= '</head>';

        $html .= '<body>';

        /**
         * Header
         */
        $html .= '
            <table>

                <tr>
                    <td colspan="9" class="title">
                        IBL CART 24
                    </td>
                </tr>

                <tr>
                    <td colspan="9" class="subtitle">
                        Sales & Orders Report
                    </td>
                </tr>

                <tr>
                    <td colspan="9">
                        Report Period:
                        ' . e($from) . '
                        to
                        ' . e($to) . '
                    </td>
                </tr>

            </table>

            <br>
        ';

        /**
         * Summary
         */
        $html .= '
            <table>

                <tr class="summary">

                    <td>Total Orders</td>
                    <td>Total Sales</td>
                    <td>Delivered</td>
                    <td>Cancelled</td>
                    <td>Pending</td>
                    <td>Confirmed</td>
                    <td>Processing</td>
                    <td>Shipped</td>
                    <td>Paid</td>

                </tr>

                <tr>

                    <td>
                        ' . $totalOrders . '
                    </td>

                    <td>
                        Tk ' .
                        number_format(
                            $totalSales,
                            2
                        ) .
                    '</td>

                    <td>
                        ' . $deliveredOrders . '
                    </td>

                    <td>
                        ' . $cancelledOrders . '
                    </td>

                    <td>
                        ' . $pendingOrders . '
                    </td>

                    <td>
                        ' . $confirmedOrders . '
                    </td>

                    <td>
                        ' . $processingOrders . '
                    </td>

                    <td>
                        ' . $shippedOrders . '
                    </td>

                    <td>
                        ' . $paidOrders . '
                    </td>

                </tr>

            </table>

            <br>
        ';

        /**
         * Payment Summary
         */
        $html .= '
            <table>

                <tr class="summary">

                    <td>Pending Payments</td>
                    <td>Failed Payments</td>
                    <td>Refunded Payments</td>

                </tr>

                <tr>

                    <td>
                        ' . $pendingPayments . '
                    </td>

                    <td>
                        ' . $failedPayments . '
                    </td>

                    <td>
                        ' . $refundedPayments . '
                    </td>

                </tr>

            </table>

            <br>
        ';

        /**
         * Orders Table
         */
        $html .= '
            <table>

                <thead>

                    <tr>

                        <th>Order ID</th>

                        <th>Customer Name</th>

                        <th>Phone</th>

                        <th>Email</th>

                        <th>Payment Method</th>

                        <th>Payment Status</th>

                        <th>Order Status</th>

                        <th>Total Amount</th>

                        <th>Order Date</th>

                    </tr>

                </thead>

                <tbody>
        ';

        foreach ($orders as $order) {

            $customerName =
                $order->customer_name
                ?? optional($order->user)->name
                ?? 'Guest';

            $phone =
                $order->phone
                ?? '-';

            $email =
                optional($order->user)->email
                ?? '-';

            $paymentMethod =
                $order->payment_method
                ?? '-';

            $paymentStatus =
                ucfirst(
                    $order->payment_status
                    ?? 'pending'
                );

            $orderStatus =
                ucfirst(
                    $order->status
                    ?? 'pending'
                );

            $amount =
                number_format(
                    (float) (
                        $order->total_amount
                        ?? $order->total
                        ?? 0
                    ),
                    2
                );

            $date =
                $order->created_at
                ? $order->created_at->format(
                    'Y-m-d H:i:s'
                )
                : '-';

            $html .= '
                <tr>

                    <td>
                        ' . e($order->id) . '
                    </td>

                    <td>
                        ' . e($customerName) . '
                    </td>

                    <td>
                        ' . e($phone) . '
                    </td>

                    <td>
                        ' . e($email) . '
                    </td>

                    <td>
                        ' . e($paymentMethod) . '
                    </td>

                    <td>
                        ' . e($paymentStatus) . '
                    </td>

                    <td>
                        ' . e($orderStatus) . '
                    </td>

                    <td class="amount">
                        Tk ' . e($amount) . '
                    </td>

                    <td>
                        ' . e($date) . '
                    </td>

                </tr>
            ';
        }

        $html .= '
                </tbody>

            </table>
        ';

        $html .= '</body>';

        $html .= '</html>';

        return $html;
    }
}