<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\SslCommerzController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\AbandonedCartController;
use App\Http\Controllers\AdminBlockedPhoneController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TrackOrderController;
use App\Http\Controllers\OrderCancelController;


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Root
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('home');
})->name('root');


/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/home', [
    ProductController::class,
    'index',
])->name('home');


/*
|--------------------------------------------------------------------------
| Post-login landing (Breeze compatibility)
|--------------------------------------------------------------------------
| Laravel Breeze controllers redirect to route('dashboard') after
| register / verify / confirm-password. Send admins to the admin panel
| and customers to the storefront.
*/

Route::get('/dashboard', function () {
    return auth()->user()?->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('home');
})->middleware('auth')->name('dashboard');


/*
|--------------------------------------------------------------------------
| PUBLIC CATEGORY ROUTES
|--------------------------------------------------------------------------
|
| CategoryController@index/@show and their views already existed but
| were only reachable under the admin-only prefix. These public
| routes are what welcome/home/profile pages actually link to.
*/

Route::get('/categories', [
    CategoryController::class,
    'index',
])->name('categories.index');

Route::get('/categories/{category}', [
    CategoryController::class,
    'show',
])->name('categories.show');


/*
|--------------------------------------------------------------------------
| PUBLIC PRODUCT ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/products', [
    ProductController::class,
    'index',
])->name('products.index');

Route::get('/products/{product}', [
    ProductController::class,
    'show',
])->name('products.show');


/*
|--------------------------------------------------------------------------
| PUBLIC CART ROUTES (Guests + Logged-in Customers)
|--------------------------------------------------------------------------
|
| The cart is session-based, so it already works without an account.
| Guests can add to cart / buy now; login is only required at
| checkout (see the auth group below), which is standard ecommerce
| practice and avoids losing guests who don't want to sign up first.
|
*/

Route::get('/cart', [
    CartController::class,
    'index',
])->name('cart.index');

Route::post('/cart/add/{product}', [
    CartController::class,
    'store',
])->name('cart.store');

Route::post('/cart/{product}', [
    CartController::class,
    'store',
])->name('cart.add');

Route::patch('/cart/{product}', [
    CartController::class,
    'update',
])->name('cart.update');

Route::delete('/cart/{product}', [
    CartController::class,
    'destroy',
])->name('cart.destroy');

Route::delete('/cart', [
    CartController::class,
    'clear',
])->name('cart.clear');

Route::post('/buy-now/{product}', [
    CartController::class,
    'buyNow',
])->name('buy.now');

Route::get('/buy-now/{product}', [
    CartController::class,
    'buyNow',
])->name('buy.now.get');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED CUSTOMER ROUTES
|--------------------------------------------------------------------------
|
| These routes require a logged-in customer.
|
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [
        ProfileController::class,
        'edit',
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update',
    ])->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy',
    ])->name('profile.destroy');

    Route::put('/password', [
        ProfileController::class,
        'updatePassword',
    ])->name('password.update');


    /*
    |--------------------------------------------------------------------------
    | CHECKOUT
    |--------------------------------------------------------------------------
    */

    Route::get('/checkout', [
        CheckoutController::class,
        'index',
    ])->name('checkout.index');

    Route::post('/checkout', [
        CheckoutController::class,
        'store',
    ])->middleware('throttle:20,1')->name('checkout.store');


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER ORDERS
    |--------------------------------------------------------------------------
    */

    Route::get('/orders', [
        OrderController::class,
        'index',
    ])->name('orders.index');

    Route::get('/orders/{order}', [
        OrderController::class,
        'show',
    ])->name('orders.show');


    /*
    |--------------------------------------------------------------------------
    | WISHLIST
    |--------------------------------------------------------------------------
    */

    Route::get('/wishlist', [
        WishlistController::class,
        'index',
    ])->name('wishlist.index');

    Route::post('/wishlist/add/{product}', [
        WishlistController::class,
        'store',
    ])->name('wishlist.store');

    Route::post('/wishlist/{product}', [
        WishlistController::class,
        'add',
    ])->name('wishlist.add');

    Route::post('/wishlist/{product}/cart', [
        WishlistController::class,
        'cart',
    ])->name('wishlist.cart');

    Route::delete('/wishlist/{product}', [
        WishlistController::class,
        'destroy',
    ])->name('wishlist.destroy');

    Route::delete('/wishlist/remove/{product}', [
        WishlistController::class,
        'remove',
    ])->name('wishlist.remove');
});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
|
| Every admin route requires:
|
| 1. Authentication
| 2. Admin middleware
|
*/

Route::middleware([
    'auth',
    'admin',
])
->prefix('admin')
->group(function () {


    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [
        AdminDashboardController::class,
        'index',
    ])->name('admin.dashboard');


    /*
    |--------------------------------------------------------------------------
    | ADMIN PRODUCTS
    |--------------------------------------------------------------------------
    */

    Route::get('/products', [
        ProductController::class,
        'index',
    ])->name('admin.products.index');

    Route::get('/products/create', [
        ProductController::class,
        'create',
    ])->name('admin.products.create');

    Route::post('/products', [
        ProductController::class,
        'store',
    ])->name('admin.products.store');

    Route::get('/products/{product}/edit', [
        ProductController::class,
        'edit',
    ])->name('admin.products.edit');

    Route::put('/products/{product}', [
        ProductController::class,
        'update',
    ])->name('admin.products.update');

    Route::patch('/products/{product}', [
        ProductController::class,
        'update',
    ])->name('admin.products.patch');

    Route::delete('/products/{product}', [
        ProductController::class,
        'destroy',
    ])->name('admin.products.destroy');


    /*
    |--------------------------------------------------------------------------
    | ADMIN CATEGORIES
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | All category route names consistently use:
    |
    | admin.categories.*
    |
    */

    Route::get('/categories', [
        CategoryController::class,
        'index',
    ])->name('admin.categories.index');

    Route::get('/categories/create', [
        CategoryController::class,
        'create',
    ])->name('admin.categories.create');

    Route::post('/categories', [
        CategoryController::class,
        'store',
    ])->name('admin.categories.store');

    Route::get('/categories/{category}', [
        CategoryController::class,
        'show',
    ])->name('admin.categories.show');

    Route::get('/categories/{category}/edit', [
        CategoryController::class,
        'edit',
    ])->name('admin.categories.edit');

    Route::put('/categories/{category}', [
        CategoryController::class,
        'update',
    ])->name('admin.categories.update');

    Route::patch('/categories/{category}', [
        CategoryController::class,
        'update',
    ])->name('admin.categories.patch');

    Route::delete('/categories/{category}', [
        CategoryController::class,
        'destroy',
    ])->name('admin.categories.destroy');


    /*
    |--------------------------------------------------------------------------
    | ADMIN ORDERS
    |--------------------------------------------------------------------------
    */

    Route::get('/orders', [
        AdminOrderController::class,
        'index',
    ])->name('admin.orders.index');

    Route::get('/orders/{order}', [
        AdminOrderController::class,
        'show',
    ])->name('admin.orders.show');

    Route::put('/orders/{order}/status', [
        AdminOrderController::class,
        'updateStatus',
    ])->name('admin.orders.status.update');

    Route::patch('/orders/{order}/status', [
        AdminOrderController::class,
        'updateStatus',
    ])->name('admin.orders.status.patch');

    Route::patch('/orders/{order}/payment-status', [
        AdminOrderController::class,
        'updatePaymentStatus',
    ])->name('admin.orders.payment.status.update');

    Route::put('/orders/{order}/payment-status', [
        AdminOrderController::class,
        'updatePaymentStatus',
    ])->name('admin.orders.payment.status.put');

    Route::delete('/orders/{order}', [
        AdminOrderController::class,
        'destroy',
    ])->name('admin.orders.destroy');


    /*
    |--------------------------------------------------------------------------
    | ADMIN CUSTOMERS
    |--------------------------------------------------------------------------
    */

    Route::get('/customers', [
        CustomerController::class,
        'index',
    ])->name('admin.customers.index');

    Route::get('/customers/{user}', [
        CustomerController::class,
        'show',
    ])->name('admin.customers.show');

    Route::get('/customers/{user}/edit', [
        CustomerController::class,
        'edit',
    ])->name('admin.customers.edit');

    Route::put('/customers/{user}', [
        CustomerController::class,
        'update',
    ])->name('admin.customers.update');

    Route::patch('/customers/{user}', [
        CustomerController::class,
        'update',
    ])->name('admin.customers.patch');


    /*
    |--------------------------------------------------------------------------
    | ADMIN ANALYTICS
    |--------------------------------------------------------------------------
    */

    Route::get('/analytics-view', [
        AnalyticsController::class,
        'index',
    ])->name('admin.analytics');


    /*
    |--------------------------------------------------------------------------
    | ADMIN REPORTS
    |--------------------------------------------------------------------------
    */

    Route::get('/reports-view', [
        ReportController::class,
        'index',
    ])->name('admin.reports');

    Route::get('/reports/excel', [
        ReportExportController::class,
        'excel',
    ])->name('admin.reports.excel');

    Route::get('/reports/pdf', [
        ReportExportController::class,
        'pdf',
    ])->name('admin.reports.pdf');

    Route::get('/reports/csv', [
        ReportExportController::class,
        'csv',
    ])->name('admin.reports.csv');
});


/*
|--------------------------------------------------------------------------
| SSL COMMERZ PAYMENT ROUTES
|--------------------------------------------------------------------------
|
| These routes are outside the auth middleware because
| SSLCommerz needs to communicate with the application.
|
*/

Route::match(['get', 'post'], '/payment/sslcommerz/initiate/{order}', [
    SslCommerzController::class,
    'initiate',
])->middleware('auth')->name('payment.sslcommerz.initiate');

Route::match(['get', 'post'], '/payment/sslcommerz/success', [
    SslCommerzController::class,
    'success',
])->name('payment.sslcommerz.success');

Route::match(['get', 'post'], '/payment/sslcommerz/fail', [
    SslCommerzController::class,
    'fail',
])->name('payment.sslcommerz.fail');

Route::match(['get', 'post'], '/payment/sslcommerz/cancel', [
    SslCommerzController::class,
    'cancel',
])->name('payment.sslcommerz.cancel');

Route::post('/payment/sslcommerz/ipn', [
    SslCommerzController::class,
    'ipn',
])->name('payment.sslcommerz.ipn');


/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| SEO: SITEMAP
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [SitemapController::class, 'index'])
    ->name('sitemap');


/*
|--------------------------------------------------------------------------
| COUPONS (customer: apply / remove) - works for guests too
|--------------------------------------------------------------------------
*/

Route::post('/coupon/apply', [CouponController::class, 'apply'])
    ->middleware('throttle:10,1')
    ->name('coupon.apply');

Route::delete('/coupon', [CouponController::class, 'remove'])
    ->name('coupon.remove');


/*
|--------------------------------------------------------------------------
| PRODUCT REVIEWS (logged-in customers)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('reviews.store');

    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])
        ->name('reviews.destroy');
});


/*
|--------------------------------------------------------------------------
| ADMIN: COUPON MANAGEMENT
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/coupons', [CouponController::class, 'index'])->name('admin.coupons.index');
        Route::get('/coupons/create', [CouponController::class, 'create'])->name('admin.coupons.create');
        Route::post('/coupons', [CouponController::class, 'store'])->name('admin.coupons.store');
        Route::get('/coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('admin.coupons.edit');
        Route::put('/coupons/{coupon}', [CouponController::class, 'update'])->name('admin.coupons.update');
        Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy'])->name('admin.coupons.destroy');

        // Fake-order protection
        Route::get('/blocked-phones', [AdminBlockedPhoneController::class, 'index'])->name('admin.blocked-phones.index');
        Route::post('/blocked-phones', [AdminBlockedPhoneController::class, 'store'])->name('admin.blocked-phones.store');
        Route::delete('/blocked-phones/{blockedPhone}', [AdminBlockedPhoneController::class, 'destroy'])->name('admin.blocked-phones.destroy');
    });

/*
|--------------------------------------------------------------------------
| Product image fallback
|--------------------------------------------------------------------------
| Real files in public/uploads/products are served directly by the web
| server. This route only runs when the file is MISSING (for example
| demo/seeded products), so pages show a clean branded placeholder
| instead of a broken-image icon.
*/

Route::get('/uploads/products/{file}', function () {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600">'
        . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
        . '<stop offset="0" stop-color="#eef2ff"/><stop offset="1" stop-color="#f5f3ff"/></linearGradient></defs>'
        . '<rect width="600" height="600" fill="url(#g)"/>'
        . '<g fill="none" stroke="#a5b4fc" stroke-width="10" stroke-linecap="round" stroke-linejoin="round">'
        . '<rect x="190" y="215" width="220" height="180" rx="22"/>'
        . '<circle cx="255" cy="275" r="20"/><path d="M200 380l70-70 50 50 35-35 55 55"/></g>'
        . '<text x="300" y="470" text-anchor="middle" font-family="Arial,sans-serif" font-size="30" font-weight="700" fill="#818cf8">IBL CART 24</text>'
        . '<text x="300" y="505" text-anchor="middle" font-family="Arial,sans-serif" font-size="20" fill="#a5b4fc">Image coming soon</text>'
        . '</svg>';

    return response($svg, 200, [
        'Content-Type'  => 'image/svg+xml',
        'Cache-Control' => 'public, max-age=3600',
    ]);
})->where('file', '.*')->name('product.image.fallback');


/*
|--------------------------------------------------------------------------
| ORDER TRACKING (public) + CUSTOMER CANCEL
|--------------------------------------------------------------------------
*/

Route::get('/track-order', [TrackOrderController::class, 'index'])
    ->name('track.order');

Route::post('/track-order', [TrackOrderController::class, 'lookup'])
    ->middleware('throttle:10,1')
    ->name('track.order.lookup');

Route::post('/orders/{order}/cancel', OrderCancelController::class)
    ->middleware('auth')
    ->name('orders.cancel');


/*
|--------------------------------------------------------------------------
| INVOICE (PDF) + STATIC PAGES
|--------------------------------------------------------------------------
*/

Route::get('/orders/{order}/invoice', [\App\Http\Controllers\InvoiceController::class, 'download'])
    ->middleware('auth')
    ->name('orders.invoice');

Route::get('/about', [\App\Http\Controllers\PageController::class, 'show'])
    ->defaults('slug', 'about')->name('pages.about');

Route::get('/contact', [\App\Http\Controllers\PageController::class, 'show'])
    ->defaults('slug', 'contact')->name('pages.contact');

Route::get('/privacy-policy', [\App\Http\Controllers\PageController::class, 'show'])
    ->defaults('slug', 'privacy')->name('pages.privacy');

Route::get('/terms-and-conditions', [\App\Http\Controllers\PageController::class, 'show'])
    ->defaults('slug', 'terms')->name('pages.terms');

Route::get('/return-policy', [\App\Http\Controllers\PageController::class, 'show'])
    ->defaults('slug', 'returns')->name('pages.returns');

Route::get('/shipping-info', [\App\Http\Controllers\PageController::class, 'show'])
    ->defaults('slug', 'shipping')->name('pages.shipping');


/*
|--------------------------------------------------------------------------
| ADMIN: SETTINGS, REVIEW MODERATION, PAYMENT VERIFY
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/settings', [\App\Http\Controllers\AdminSettingsController::class, 'edit'])
            ->name('admin.settings.edit');

        Route::put('/settings', [\App\Http\Controllers\AdminSettingsController::class, 'update'])
            ->name('admin.settings.update');

        Route::get('/reviews', [\App\Http\Controllers\AdminReviewController::class, 'index'])
            ->name('admin.reviews.index');

        Route::patch('/reviews/{review}/toggle', [\App\Http\Controllers\AdminReviewController::class, 'toggle'])
            ->name('admin.reviews.toggle');

        Route::delete('/reviews/{review}', [\App\Http\Controllers\AdminReviewController::class, 'destroy'])
            ->name('admin.reviews.destroy');

        Route::patch('/orders/{order}/verify-payment', \App\Http\Controllers\AdminVerifyPaymentController::class)
            ->name('admin.orders.payment.verify');

        Route::post('/orders/{order}/courier', [\App\Http\Controllers\AdminCourierController::class, 'send'])
            ->name('admin.orders.courier.send');

        Route::post('/orders/{order}/courier/refresh', [\App\Http\Controllers\AdminCourierController::class, 'refresh'])
            ->name('admin.orders.courier.refresh');
    });


/*
|--------------------------------------------------------------------------
| REORDER + FAQ
|--------------------------------------------------------------------------
*/

Route::post('/orders/{order}/reorder', \App\Http\Controllers\OrderReorderController::class)
    ->middleware('auth')
    ->name('orders.reorder');

Route::get('/faq', [\App\Http\Controllers\PageController::class, 'show'])
    ->defaults('slug', 'faq')->name('pages.faq');

/*
|--------------------------------------------------------------------------
| ABANDONED CART (save phone at checkout + link from the reminder SMS)
|--------------------------------------------------------------------------
*/

Route::post('/checkout/capture', [AbandonedCartController::class, 'capture'])
    ->middleware('throttle:30,1')
    ->name('checkout.capture');

Route::get('/cart/recover/{token}', [AbandonedCartController::class, 'restore'])
    ->middleware('throttle:20,1')
    ->name('cart.recover');
