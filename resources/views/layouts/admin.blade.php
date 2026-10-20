<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Admin Panel') - IBL CART 24
    </title>

    {{-- SEO --}}
    @hasSection('seo')
        @yield('seo')
    @else
        <meta name="description" content="IBL CART 24 - Shop smart. Quality products at great prices in Bangladesh.">
        <meta property="og:site_name" content="IBL CART 24">
        <meta property="og:title" content="@yield('title', 'IBL CART 24')">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Bootstrap --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- Font Awesome --}}
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

    {{-- Google Font --}}
    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="{{ asset('css/pages/layouts-admin.css') }}?v=b3">

    @stack('styles')

</head>


<body>

    {{-- ============================================================
         ADMIN SIDEBAR
    ============================================================= --}}

    <aside
        id="adminSidebar"
        class="admin-sidebar"
    >

        {{-- BRAND --}}
        <div class="admin-brand">

            <div class="admin-brand-icon">

                <i class="fa-solid fa-cart-shopping"></i>

            </div>

            <div class="admin-brand-text">

                <h1 class="admin-brand-title">
                    IBL CART 24
                </h1>

                <div class="admin-brand-subtitle">
                    ADMIN PANEL
                </div>

            </div>

        </div>


        {{-- SIDEBAR CONTENT --}}
        <div class="sidebar-content">


            {{-- ====================================================
                 MAIN
            ===================================================== --}}

            <div class="sidebar-section">

                <div class="sidebar-section-title">
                    Main
                </div>


                {{-- Dashboard --}}

                @if(Route::has('admin.dashboard'))

                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="sidebar-link
                            {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    >

                        <i class="fa-solid fa-gauge-high"></i>

                        <span>
                            Dashboard
                        </span>

                    </a>

                @endif


                {{-- Products --}}

                @if(Route::has('admin.products.index'))

                    <a
                        href="{{ route('admin.products.index') }}"
                        class="sidebar-link
                            {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
                    >

                        <i class="fa-solid fa-box"></i>

                        <span>
                            Products
                        </span>

                    </a>

                @endif


                {{-- Categories --}}

                @if(Route::has('admin.categories.index'))

                    <a
                        href="{{ route('admin.categories.index') }}"
                        class="sidebar-link
                            {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                    >

                        <i class="fa-solid fa-layer-group"></i>

                        <span>
                            Categories
                        </span>

                    </a>

                @endif


                {{-- Orders --}}

                @if(Route::has('admin.orders.index'))

                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="sidebar-link
                            {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                    >

                        <i class="fa-solid fa-receipt"></i>

                        <span>
                            Orders
                        </span>

                    </a>

                @endif


                {{-- Customers --}}

                @if(Route::has('admin.customers.index'))

                    <a
                        href="{{ route('admin.customers.index') }}"
                        class="sidebar-link
                            {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                    >

                        <i class="fa-solid fa-users"></i>

                        <span>
                            Customers
                        </span>

                    </a>

                @endif


                {{-- Coupons --}}

                @if(Route::has('admin.coupons.index'))

                    <a
                        href="{{ route('admin.coupons.index') }}"
                        class="sidebar-link
                            {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}"
                    >

                        <i class="fa-solid fa-ticket"></i>

                        <span>
                            Coupons
                        </span>

                    </a>

                @endif

                {{-- Blocked phones (fake-order protection) --}}

                @if(Route::has('admin.blocked-phones.index'))

                    <a
                        href="{{ route('admin.blocked-phones.index') }}"
                        class="sidebar-link
                            {{ request()->routeIs('admin.blocked-phones.*') ? 'active' : '' }}"
                    >

                        <i class="fa-solid fa-ban"></i>

                        <span>
                            Blocked Phones
                        </span>

                    </a>

                @endif


                {{-- Reviews --}}

                @if(Route::has('admin.reviews.index'))

                    <a
                        href="{{ route('admin.reviews.index') }}"
                        class="sidebar-link
                            {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}"
                    >

                        <i class="fa-solid fa-star"></i>

                        <span>
                            Reviews
                        </span>

                    </a>

                @endif


                {{-- Settings --}}

                @if(Route::has('admin.settings.edit'))

                    <a
                        href="{{ route('admin.settings.edit') }}"
                        class="sidebar-link
                            {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                    >

                        <i class="fa-solid fa-gear"></i>

                        <span>
                            Settings
                        </span>

                    </a>

                @endif


                {{-- Analytics --}}

                @if(Route::has('admin.analytics'))

                    <a
                        href="{{ route('admin.analytics') }}"
                        class="sidebar-link
                            {{ request()->routeIs('admin.analytics') ? 'active' : '' }}"
                    >

                        <i class="fa-solid fa-chart-pie"></i>

                        <span>
                            Analytics
                        </span>

                    </a>

                @endif


                {{-- Reports --}}

                @if(Route::has('admin.reports'))

                    <a
                        href="{{ route('admin.reports') }}"
                        class="sidebar-link
                            {{ request()->routeIs('admin.reports') ? 'active' : '' }}"
                    >

                        <i class="fa-solid fa-file-invoice"></i>

                        <span>
                            Reports
                        </span>

                    </a>

                @endif

            </div>


            {{-- DIVIDER --}}

            <div class="sidebar-divider"></div>


            {{-- ====================================================
                 STORE
            ===================================================== --}}

            <div class="sidebar-section">

                <div class="sidebar-section-title">
                    Store
                </div>


                {{-- View Store --}}

                @if(Route::has('home'))

                    <a
                        href="{{ route('home') }}"
                        target="_blank"
                        rel="noopener"
                        class="sidebar-link"
                    >

                        <i class="fa-solid fa-store"></i>

                        <span>
                            View Store
                        </span>

                        <i
                            class="fa-solid fa-arrow-up-right-from-square ms-auto"
                            style="font-size:10px;"
                        ></i>

                    </a>

                @endif


                {{-- Cart --}}

                @if(Route::has('cart.index'))

                    <a
                        href="{{ route('cart.index') }}"
                        class="sidebar-link
                            {{ request()->routeIs('cart.*') ? 'active' : '' }}"
                    >

                        <i class="fa-solid fa-cart-shopping"></i>

                        <span>
                            Cart
                        </span>

                    </a>

                @endif


                {{-- Wishlist --}}

                @if(Route::has('wishlist.index'))

                    <a
                        href="{{ route('wishlist.index') }}"
                        class="sidebar-link
                            {{ request()->routeIs('wishlist.*') ? 'active' : '' }}"
                    >

                        <i class="fa-solid fa-heart"></i>

                        <span>
                            Wishlist
                        </span>

                    </a>

                @endif

            </div>


            {{-- DIVIDER --}}

            <div class="sidebar-divider"></div>


            {{-- ====================================================
                 ACCOUNT
            ===================================================== --}}

            <div class="sidebar-section">

                <div class="sidebar-section-title">
                    Account
                </div>


                {{-- Profile --}}

                @if(Route::has('profile.edit'))

                    <a
                        href="{{ route('profile.edit') }}"
                        class="sidebar-link
                            {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                    >

                        <i class="fa-solid fa-user"></i>

                        <span>
                            Profile
                        </span>

                    </a>

                @endif


                {{-- Logout --}}

                @if(Route::has('logout'))

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                        class="m-0"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="sidebar-link border-0 w-100 text-start"
                            style="background:transparent;"
                        >

                            <i class="fa-solid fa-right-from-bracket"></i>

                            <span>
                                Logout
                            </span>

                        </button>

                    </form>

                @endif

            </div>

        </div>

    </aside>


    {{-- MOBILE OVERLAY --}}

    <div
        id="adminOverlay"
        class="admin-overlay"
    ></div>


    {{-- ============================================================
         MAIN
    ============================================================= --}}

    <main class="admin-main">


        {{-- ========================================================
             TOPBAR
        ========================================================= --}}

        <header class="admin-topbar">

            <div class="topbar-left">

                {{-- Mobile Menu --}}

                <button
                    type="button"
                    id="adminMenuButton"
                    class="topbar-menu-button"
                    aria-label="Open menu"
                >

                    <i class="fa-solid fa-bars"></i>

                </button>


                <div>

                    <h2 class="topbar-title">
                        @yield('page-title', 'Admin Panel')
                    </h2>

                    <p class="topbar-subtitle">
                        Manage your IBL CART 24 store
                    </p>

                </div>

            </div>


            {{-- USER --}}

            <div class="admin-user-area">

                <div class="admin-user-info">

                    <p class="admin-user-name">

                        {{ auth()->user()->name ?? 'Administrator' }}

                    </p>

                    <div class="admin-user-role">
                        Administrator
                    </div>

                </div>


                @php

                    $adminName = auth()->user()->name ?? 'Administrator';

                    $adminInitial = strtoupper(
                        substr(trim($adminName), 0, 1)
                    );

                @endphp


                <div class="admin-avatar">

                    {{ $adminInitial }}

                </div>

            </div>

        </header>


        {{-- ========================================================
             PAGE CONTENT
        ========================================================= --}}

        <section class="admin-content">


            {{-- SESSION SUCCESS MESSAGE --}}

            @if(session('success'))

                <div
                    class="alert alert-success admin-alert alert-dismissible fade show"
                    role="alert"
                >

                    <i class="fa-solid fa-circle-check me-2"></i>

                    {{ session('success') }}

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"
                    ></button>

                </div>

            @endif


            {{-- SESSION ERROR MESSAGE --}}

            @if(session('error'))

                <div
                    class="alert alert-danger admin-alert alert-dismissible fade show"
                    role="alert"
                >

                    <i class="fa-solid fa-circle-exclamation me-2"></i>

                    {{ session('error') }}

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"
                    ></button>

                </div>

            @endif


            {{-- VALIDATION ERRORS --}}

            @if($errors->any())

                <div
                    class="alert alert-danger admin-alert alert-dismissible fade show"
                    role="alert"
                >

                    <div class="fw-bold mb-2">

                        <i class="fa-solid fa-triangle-exclamation me-2"></i>

                        Please fix the following errors:

                    </div>


                    <ul class="mb-0 ps-4">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"
                    ></button>

                </div>

            @endif


            {{-- PAGE CONTENT --}}

            @yield('content')


        </section>


        {{-- ========================================================
             FOOTER
        ========================================================= --}}

        <footer class="admin-footer">

            © {{ date('Y') }}

            <strong>
                IBL CART 24
            </strong>

            — Admin Panel

        </footer>


    </main>


    {{-- Bootstrap JS --}}

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
    ></script>


    {{-- ============================================================
         MOBILE SIDEBAR SCRIPT
    ============================================================= --}}

    <script src="{{ asset('js/pages/layouts-admin.js') }}?v=b3"></script>


    @stack('scripts')

</body>

</html>