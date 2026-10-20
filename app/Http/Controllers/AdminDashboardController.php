<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC COUNTS
        |--------------------------------------------------------------------------
        */

        $totalProducts = Product::count();

        $totalCategories = Category::count();

        $totalOrders = Order::count();


        /*
        |--------------------------------------------------------------------------
        | TOTAL STOCK
        |--------------------------------------------------------------------------
        */

        $totalStock = (int) Product::sum('stock');


        /*
        |--------------------------------------------------------------------------
        | CART ITEMS
        |--------------------------------------------------------------------------
        */

        $cart = session()->get('cart', []);

        $cartItems = collect($cart)->sum(function ($item) {
            return (int) (
                $item['quantity']
                ?? $item['qty']
                ?? 0
            );
        });


        /*
        |--------------------------------------------------------------------------
        | SALES
        |--------------------------------------------------------------------------
        |
        | Only non-cancelled orders are included in sales.
        |
        */

        $totalSales = (float) Order::query()
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'cancelled');
            })
            ->sum(DB::raw(
                'COALESCE(total_amount, total, 0)'
            ));


        /*
        |--------------------------------------------------------------------------
        | TODAY'S SALES
        |--------------------------------------------------------------------------
        */

        $todaySales = (float) Order::query()
            ->whereDate('created_at', now()->toDateString())
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'cancelled');
            })
            ->sum(DB::raw(
                'COALESCE(total_amount, total, 0)'
            ));


        /*
        |--------------------------------------------------------------------------
        | PENDING ORDERS
        |--------------------------------------------------------------------------
        */

        $pendingOrders = Order::query()
            ->where('status', 'pending')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | ORDER STATUS COUNTS
        |--------------------------------------------------------------------------
        */

        $orderStatusCounts = [
            'pending' => Order::where('status', 'pending')->count(),

            'confirmed' => Order::where('status', 'confirmed')->count(),

            'processing' => Order::where('status', 'processing')->count(),

            'shipped' => Order::where('status', 'shipped')->count(),

            'delivered' => Order::where('status', 'delivered')->count(),

            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];


        /*
        |--------------------------------------------------------------------------
        | MONTHLY SALES
        |--------------------------------------------------------------------------
        */

        $monthlySales = [];

        for ($month = 1; $month <= 12; $month++) {

            $monthlySales[$month] = (float) Order::query()
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', $month)
                ->where(function ($query) {
                    $query->whereNull('status')
                        ->orWhere('status', '!=', 'cancelled');
                })
                ->sum(DB::raw(
                    'COALESCE(total_amount, total, 0)'
                ));
        }


        /*
        |--------------------------------------------------------------------------
        | LOW STOCK PRODUCTS
        |--------------------------------------------------------------------------
        |
        | Products with stock <= 5
        |
        */

        $lowStockProducts = Product::query()
            ->with('category')
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->orderBy('name', 'asc')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | RECENT ORDERS
        |--------------------------------------------------------------------------
        */

        $recentOrders = Order::query()
            ->latest()
            ->take(10)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | RECENT PRODUCTS
        |--------------------------------------------------------------------------
        */

        $recentProducts = Product::query()
            ->with('category')
            ->latest()
            ->take(8)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | TOP PRODUCTS
        |--------------------------------------------------------------------------
        |
        | Calculates sold quantity from order_items.
        |
        */

        $topProducts = Product::query()
            ->select(
                'products.id',
                'products.name',
                'products.price',
                'products.stock',
                'products.image',
                'products.category_id'
            )
            ->with('category')
            ->leftJoin(
                'order_items',
                'products.id',
                '=',
                'order_items.product_id'
            )
            ->selectRaw(
                'COALESCE(SUM(order_items.quantity), 0) as sold_quantity'
            )
            ->groupBy(
                'products.id',
                'products.name',
                'products.price',
                'products.stock',
                'products.image',
                'products.category_id'
            )
            ->orderByDesc('sold_quantity')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PAYMENT STATUS
        |--------------------------------------------------------------------------
        */

        $paymentStatusCounts = [
            'pending' => Order::where('payment_status', 'Pending')->count(),

            'paid' => Order::where('payment_status', 'Paid')->count(),

            'failed' => Order::where('payment_status', 'Failed')->count(),

            'refunded' => Order::where('payment_status', 'Refunded')->count(),
        ];


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER COUNT
        |--------------------------------------------------------------------------
        */

        $totalCustomers = Order::query()
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');


        /*
        |--------------------------------------------------------------------------
        | DASHBOARD VIEW
        |--------------------------------------------------------------------------
        */

        return view('admin.dashboard.index', compact(

            'totalProducts',

            'totalCategories',

            'totalOrders',

            'totalStock',

            'cartItems',

            'totalSales',

            'todaySales',

            'pendingOrders',

            'orderStatusCounts',

            'monthlySales',

            'lowStockProducts',

            'recentOrders',

            'recentProducts',

            'topProducts',

            'paymentStatusCounts',

            'totalCustomers'

        ));
    }
}