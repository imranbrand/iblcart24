<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | DATE RANGE
        |--------------------------------------------------------------------------
        */

        $range = $request->get('range', 'month');

        switch ($range) {

            case 'today':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;

            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;

            case 'year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;

            default:
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                $range = 'month';
                break;
        }


        /*
        |--------------------------------------------------------------------------
        | SALES
        |--------------------------------------------------------------------------
        */

        $salesQuery = Order::whereBetween(
            'created_at',
            [$startDate, $endDate]
        )
        ->where('status', '!=', 'cancelled');

        $totalSales = (clone $salesQuery)
            ->sum('total_amount');

        $totalOrders = (clone $salesQuery)
            ->count();


        /*
        |--------------------------------------------------------------------------
        | AVERAGE ORDER VALUE
        |--------------------------------------------------------------------------
        */

        $averageOrderValue = $totalOrders > 0
            ? $totalSales / $totalOrders
            : 0;


        /*
        |--------------------------------------------------------------------------
        | ORDER STATUS
        |--------------------------------------------------------------------------
        */

        $pendingOrders = Order::whereBetween(
                'created_at',
                [$startDate, $endDate]
            )
            ->where('status', 'pending')
            ->count();

        $confirmedOrders = Order::whereBetween(
                'created_at',
                [$startDate, $endDate]
            )
            ->where('status', 'confirmed')
            ->count();

        $processingOrders = Order::whereBetween(
                'created_at',
                [$startDate, $endDate]
            )
            ->where('status', 'processing')
            ->count();

        $shippedOrders = Order::whereBetween(
                'created_at',
                [$startDate, $endDate]
            )
            ->where('status', 'shipped')
            ->count();

        $deliveredOrders = Order::whereBetween(
                'created_at',
                [$startDate, $endDate]
            )
            ->where('status', 'delivered')
            ->count();

        $cancelledOrders = Order::whereBetween(
                'created_at',
                [$startDate, $endDate]
            )
            ->where('status', 'cancelled')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | PAYMENT STATUS
        |--------------------------------------------------------------------------
        */

        $paidOrders = Order::whereBetween(
                'created_at',
                [$startDate, $endDate]
            )
            ->where('payment_status', 'paid')
            ->count();

        $pendingPayments = Order::whereBetween(
                'created_at',
                [$startDate, $endDate]
            )
            ->where('payment_status', 'pending')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | DAILY SALES
        |--------------------------------------------------------------------------
        */

        $dailySales = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween(
                'created_at',
                [$startDate, $endDate]
            )
            ->where('status', '!=', 'cancelled')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | MONTHLY SALES
        |--------------------------------------------------------------------------
        */

        $monthlySales = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereYear('created_at', now()->year)
            ->where('status', '!=', 'cancelled')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | BEST SELLING PRODUCTS
        |--------------------------------------------------------------------------
        */

        $bestSellingProducts = OrderItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(quantity * price) as revenue')
            )
            ->whereHas('order', function ($query) use ($startDate, $endDate) {

                $query->whereBetween(
                    'created_at',
                    [$startDate, $endDate]
                )
                ->where('status', '!=', 'cancelled');

            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->take(10)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | TOP CUSTOMERS
        |--------------------------------------------------------------------------
        */

        $topCustomers = User::select(
                'users.id',
                'users.name',
                'users.email'
            )
            ->join(
                'orders',
                'users.id',
                '=',
                'orders.user_id'
            )
            ->whereBetween(
                'orders.created_at',
                [$startDate, $endDate]
            )
            ->where(
                'orders.status',
                '!=',
                'cancelled'
            )
            ->groupBy(
                'users.id',
                'users.name',
                'users.email'
            )
            ->selectRaw(
                'COUNT(orders.id) as total_orders'
            )
            ->selectRaw(
                'SUM(orders.total_amount) as total_spent'
            )
            ->orderByDesc('total_spent')
            ->take(10)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PAYMENT METHOD
        |--------------------------------------------------------------------------
        */

        $paymentMethods = Order::select(
                'payment_method',
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween(
                'created_at',
                [$startDate, $endDate]
            )
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.analytics.index',
            compact(
                'range',
                'startDate',
                'endDate',

                'totalSales',
                'totalOrders',
                'averageOrderValue',

                'pendingOrders',
                'confirmedOrders',
                'processingOrders',
                'shippedOrders',
                'deliveredOrders',
                'cancelledOrders',

                'paidOrders',
                'pendingPayments',

                'dailySales',
                'monthlySales',

                'bestSellingProducts',
                'topCustomers',

                'paymentMethods'
            )
        );
    }
}

