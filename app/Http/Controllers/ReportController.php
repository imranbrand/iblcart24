<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display admin sales reports.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | DATE RANGE
        |--------------------------------------------------------------------------
        */

        $from = $request->input(
            'from',
            now()->startOfMonth()->format('Y-m-d')
        );

        $to = $request->input(
            'to',
            now()->format('Y-m-d')
        );

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE DATE RANGE
        |--------------------------------------------------------------------------
        */

        try {
            $fromDate = \Carbon\Carbon::parse($from)->startOfDay();
            $toDate = \Carbon\Carbon::parse($to)->endOfDay();

            if ($fromDate->greaterThan($toDate)) {
                [$fromDate, $toDate] = [$toDate, $fromDate];

                $from = $fromDate->format('Y-m-d');
                $to = $toDate->format('Y-m-d');
            }
        } catch (\Throwable $e) {
            $fromDate = now()->startOfMonth()->startOfDay();
            $toDate = now()->endOfDay();

            $from = $fromDate->format('Y-m-d');
            $to = $toDate->format('Y-m-d');
        }

        /*
        |--------------------------------------------------------------------------
        | BASE QUERY
        |--------------------------------------------------------------------------
        */

        $baseQuery = Order::query()
            ->whereBetween('created_at', [
                $fromDate,
                $toDate,
            ]);

        /*
        |--------------------------------------------------------------------------
        | REPORT ORDERS COLLECTION
        |--------------------------------------------------------------------------
        |
        | This collection is used for report calculations.
        |
        */

        $reportOrders = (clone $baseQuery)
            ->with([
                'user',
                'items',
            ])
            ->latest('created_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | PAGINATED ORDERS
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | $orders is deliberately a paginator because the Blade file
        | uses methods such as:
        |
        |     $orders->total()
        |     $orders->currentPage()
        |     $orders->lastPage()
        |     $orders->links()
        |
        */

        $orders = (clone $baseQuery)
            ->with('user')
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | TOTAL ORDERS
        |--------------------------------------------------------------------------
        */

        $totalOrders = (clone $baseQuery)->count();

        /*
        |--------------------------------------------------------------------------
        | ORDER STATUS COUNTS
        |--------------------------------------------------------------------------
        */

        $pendingOrders = (clone $baseQuery)
            ->where('status', 'pending')
            ->count();

        $confirmedOrders = (clone $baseQuery)
            ->where('status', 'confirmed')
            ->count();

        $processingOrders = (clone $baseQuery)
            ->where('status', 'processing')
            ->count();

        $shippedOrders = (clone $baseQuery)
            ->where('status', 'shipped')
            ->count();

        $deliveredOrders = (clone $baseQuery)
            ->where('status', 'delivered')
            ->count();

        $cancelledOrders = (clone $baseQuery)
            ->where('status', 'cancelled')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | PAYMENT STATUS COUNTS
        |--------------------------------------------------------------------------
        */

        $paidOrders = (clone $baseQuery)
            ->where('payment_status', 'paid')
            ->count();

        $paymentPendingOrders = (clone $baseQuery)
            ->where('payment_status', 'pending')
            ->count();

        $failedOrders = (clone $baseQuery)
            ->where('payment_status', 'failed')
            ->count();

        $refundedOrders = (clone $baseQuery)
            ->where('payment_status', 'refunded')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | SALES
        |--------------------------------------------------------------------------
        */

        $totalSales = (clone $baseQuery)
            ->where('payment_status', 'paid')
            ->sum(
                DB::raw(
                    'COALESCE(total_amount, total, 0)'
                )
            );

        $grossSales = (clone $baseQuery)
            ->sum(
                DB::raw(
                    'COALESCE(total_amount, total, 0)'
                )
            );

        /*
        |--------------------------------------------------------------------------
        | ORDER IDS
        |--------------------------------------------------------------------------
        */

        $orderIds = (clone $baseQuery)
            ->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | TOTAL ITEMS SOLD
        |--------------------------------------------------------------------------
        */

        $totalItemsSold = 0;

        if ($orderIds->isNotEmpty()) {
            $totalItemsSold = OrderItem::query()
                ->whereIn('order_id', $orderIds)
                ->sum('quantity');
        }

        /*
        |--------------------------------------------------------------------------
        | TOP PRODUCTS
        |--------------------------------------------------------------------------
        */

        $topProducts = collect();

        if ($orderIds->isNotEmpty()) {
            $topProducts = OrderItem::query()
                ->whereIn('order_id', $orderIds)
                ->select(
                    'product_id',
                    'product_name',
                    DB::raw(
                        'SUM(quantity) as total_quantity'
                    ),
                    DB::raw(
                        'SUM(subtotal) as total_revenue'
                    )
                )
                ->groupBy(
                    'product_id',
                    'product_name'
                )
                ->orderByDesc('total_quantity')
                ->limit(10)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | TOP CUSTOMERS
        |--------------------------------------------------------------------------
        */

        $topCustomers = (clone $baseQuery)
            ->select(
                'user_id',
                'customer_name',
                'phone',
                DB::raw(
                    'COUNT(*) as total_orders'
                ),
                DB::raw(
                    'SUM(COALESCE(total_amount, total, 0)) as total_spent'
                )
            )
            ->groupBy(
                'user_id',
                'customer_name',
                'phone'
            )
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | DAILY SALES
        |--------------------------------------------------------------------------
        */

        $dailySales = (clone $baseQuery)
            ->where('payment_status', 'paid')
            ->select(
                DB::raw(
                    'DATE(created_at) as sale_date'
                ),
                DB::raw(
                    'COUNT(*) as total_orders'
                ),
                DB::raw(
                    'SUM(COALESCE(total_amount, total, 0)) as total_sales'
                )
            )
            ->groupBy(
                DB::raw('DATE(created_at)')
            )
            ->orderBy('sale_date')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | CONFIRMED ORDERS IN RANGE
        |--------------------------------------------------------------------------
        */

        $confirmedOrdersInRange = (clone $baseQuery)
            ->where('status', 'confirmed')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | AVERAGE ORDER VALUE
        |--------------------------------------------------------------------------
        */

        $averageOrderValue = $totalOrders > 0
            ? $grossSales / $totalOrders
            : 0;

        /*
        |--------------------------------------------------------------------------
        | AVERAGE PAID ORDER VALUE
        |--------------------------------------------------------------------------
        */

        $averagePaidOrderValue = $paidOrders > 0
            ? $totalSales / $paidOrders
            : 0;

        /*
        |--------------------------------------------------------------------------
        | PERCENTAGES
        |--------------------------------------------------------------------------
        */

        $deliveredPercentage = $totalOrders > 0
            ? ($deliveredOrders / $totalOrders) * 100
            : 0;

        $cancelledPercentage = $totalOrders > 0
            ? ($cancelledOrders / $totalOrders) * 100
            : 0;

        $paidPercentage = $totalOrders > 0
            ? ($paidOrders / $totalOrders) * 100
            : 0;

        /*
        |--------------------------------------------------------------------------
        | RECENT ORDERS
        |--------------------------------------------------------------------------
        */

        $recentOrders = (clone $baseQuery)
            ->with('user')
            ->latest('created_at')
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER COUNT
        |--------------------------------------------------------------------------
        */

        $customerCount = (clone $baseQuery)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        /*
        |--------------------------------------------------------------------------
        | PAYMENT METHOD SUMMARY
        |--------------------------------------------------------------------------
        */

        $paymentMethods = (clone $baseQuery)
            ->select(
                'payment_method',
                DB::raw(
                    'COUNT(*) as total_orders'
                ),
                DB::raw(
                    'SUM(COALESCE(total_amount, total, 0)) as total_amount'
                )
            )
            ->groupBy('payment_method')
            ->orderByDesc('total_orders')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RETURN REPORT VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.reports.index',
            compact(
                'orders',
                'reportOrders',
                'recentOrders',

                'from',
                'to',

                'totalOrders',

                'pendingOrders',
                'confirmedOrders',
                'processingOrders',
                'shippedOrders',
                'deliveredOrders',
                'cancelledOrders',

                'paidOrders',
                'paymentPendingOrders',
                'failedOrders',
                'refundedOrders',

                'totalSales',
                'grossSales',

                'totalItemsSold',

                'topProducts',
                'topCustomers',
                'dailySales',

                'confirmedOrdersInRange',

                'averageOrderValue',
                'averagePaidOrderValue',

                'deliveredPercentage',
                'cancelledPercentage',
                'paidPercentage',

                'customerCount',
                'paymentMethods'
            )
        );
    }
}