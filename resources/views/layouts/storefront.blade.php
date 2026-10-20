<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IBL CART 24') - IBL CART 24</title>
    @hasSection('seo') @yield('seo') @else
        <meta name="description" content="IBL CART 24 - Shop smart. Shop premium. Quality products at great prices in Bangladesh.">
        <meta property="og:site_name" content="IBL CART 24">
        <meta property="og:title" content="@yield('title', 'IBL CART 24')">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        @if(config('shop.og_image'))
            <meta property="og:image" content="{{ str_starts_with(config('shop.og_image'), 'http') ? config('shop.og_image') : asset(config('shop.og_image')) }}">
            <meta name="twitter:card" content="summary_large_image">
        @endif
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="theme-color" content="#4f46e5">
    @include('partials.tracking')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--store-primary:#111827;--store-accent:#4f46e5;--store-accent2:#7c3aed;--store-bg:#f6f7fb;--store-border:#e5e7eb}
        *{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:var(--store-bg);color:#111827;font-family:'Inter','Noto Sans Bengali',Arial,Helvetica,sans-serif}a{text-decoration:none}
        .store-navbar{position:sticky;top:0;z-index:1030;background:rgba(255,255,255,.97);border-bottom:1px solid var(--store-border);backdrop-filter:blur(14px)}
        .store-brand{font-size:21px;font-weight:800;letter-spacing:-.4px;color:#111827}.store-brand span{color:var(--store-accent)}
        .store-nav-link{color:#374151!important;font-weight:600;border-radius:10px;padding:9px 12px!important;transition:.2s ease}.store-nav-link:hover,.store-nav-link.active{color:var(--store-accent)!important;background:#eef2ff}
        .store-main{min-height:calc(100vh - 72px)}
        .store-footer{margin-top:60px;background:#111827;color:#d1d5db;padding:42px 0 22px}.store-footer h5{color:#fff;font-weight:800}.store-footer a{color:#d1d5db}.store-footer a:hover{color:#fff}.store-footer-bottom{margin-top:28px;padding-top:18px;border-top:1px solid rgba(255,255,255,.1);font-size:13px;color:#9ca3af}
        @media(max-width:991.98px){.store-navbar .navbar-nav{padding:12px 0}}
        .store-brand span{background:linear-gradient(135deg,var(--store-accent),var(--store-accent2));-webkit-background-clip:text;background-clip:text;color:transparent}
        .store-search{max-width:280px}.store-search .form-control{border-radius:999px 0 0 999px;border-color:var(--store-border);font-size:14px}.store-search .btn{border-radius:0 999px 999px 0;background:var(--store-primary);color:#fff}
        .cart-badge{position:relative}.cart-badge .badge{position:absolute;top:0;right:-2px;font-size:10px;background:linear-gradient(135deg,var(--store-accent),var(--store-accent2))}
        .ibl-hero{background:linear-gradient(135deg,#312e81 0%,#4f46e5 45%,#7c3aed 100%);color:#fff;border-radius:24px;padding:44px 40px;box-shadow:0 20px 50px rgba(79,70,229,.25)}
        .ibl-hero-tag{display:inline-block;background:rgba(255,255,255,.16);border-radius:999px;padding:6px 14px;font-size:13px;font-weight:600;margin-bottom:16px}
        .ibl-hero-title{font-size:clamp(28px,4vw,46px);font-weight:800;line-height:1.1;letter-spacing:-1px;margin-bottom:14px}
        .ibl-hero-text{color:rgba(255,255,255,.85);max-width:520px;margin-bottom:22px}
        .ibl-perk{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);border-radius:16px;padding:16px;height:100%;display:flex;flex-direction:column;gap:2px}
        .ibl-perk i{font-size:20px;margin-bottom:6px}.ibl-perk b{font-size:14px}.ibl-perk span{font-size:12px;color:rgba(255,255,255,.75)}
        .ibl-chips{display:flex;gap:8px;overflow-x:auto;padding-bottom:4px}.ibl-chip{white-space:nowrap;background:#fff;border:1px solid var(--store-border);border-radius:999px;padding:8px 18px;color:#374151;font-weight:600;font-size:14px;transition:.2s}.ibl-chip:hover{background:var(--store-accent);border-color:var(--store-accent);color:#fff}
        .pay-badges span{display:inline-block;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);border-radius:8px;padding:4px 10px;font-size:12px;margin:0 6px 6px 0;color:#e5e7eb}
        .to-top{position:fixed;right:18px;bottom:18px;width:44px;height:44px;border-radius:50%;border:0;background:linear-gradient(135deg,var(--store-accent),var(--store-accent2));color:#fff;box-shadow:0 8px 20px rgba(79,70,229,.35);display:none;z-index:1040}
        .to-top.show{display:block}
        @media(max-width:575.98px){.ibl-hero{padding:28px 20px;border-radius:18px}.store-search{max-width:100%;margin:8px 0}}
        @media print{.store-navbar,.store-footer,.to-top{display:none!important}}
    </style>
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/ibl-ui.css') }}?v=ui2">
    <link rel="stylesheet" href="{{ asset('css/ibl-polish.css') }}?v=1">
</head>
<body>
<a href="#main" class="skip-link">Skip to content</a>
@if(config('shop.announcement_enabled') && filled(config('shop.announcement_text')))
<div class="announce-bar" role="note">{{ config('shop.announcement_text') }}</div>
@endif
<nav class="navbar navbar-expand-lg store-navbar">
    <div class="container">
        <a class="navbar-brand store-brand" href="{{ route('home') }}">IBL <span>CART</span> 24</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#storeNavbar" aria-controls="storeNavbar" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="storeNavbar">
            <form action="{{ route('products.index') }}" method="GET" class="input-group store-search ms-lg-4 me-lg-auto"><input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search products..." aria-label="Search products"><button class="btn" type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button></form>
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link store-nav-link {{ request()->routeIs('home*') ? 'active' : '' }}" href="{{ route('home') }}"><i class="fa-solid fa-house me-1"></i>Home</a></li>
                <li class="nav-item"><a class="nav-link store-nav-link {{ request()->routeIs('products*') ? 'active' : '' }}" href="{{ route('products.index') }}"><i class="fa-solid fa-bag-shopping me-1"></i>Products</a></li>
                <li class="nav-item"><a class="nav-link store-nav-link {{ request()->routeIs('categories*') ? 'active' : '' }}" href="{{ route('categories.index') }}"><i class="fa-solid fa-layer-group me-1"></i>Categories</a></li>
                <li class="nav-item"><a class="nav-link store-nav-link {{ request()->routeIs('track*') ? 'active' : '' }}" href="{{ route('track.order') }}"><i class="fa-solid fa-location-dot me-1"></i>Track</a></li>
                <li class="nav-item"><a class="nav-link store-nav-link cart-badge" href="{{ route('cart.index') }}"><i class="fa-solid fa-cart-shopping me-1"></i>Cart @php $__cartQty = collect(session('cart', []))->sum('quantity'); @endphp @if($__cartQty > 0)<span class="badge rounded-pill">{{ $__cartQty }}</span>@endif</a></li>
                @auth
                    <li class="nav-item"><a class="nav-link store-nav-link" href="{{ route('wishlist.index') }}"><i class="fa-regular fa-heart me-1"></i>Wishlist</a></li>
                    <li class="nav-item"><a class="nav-link store-nav-link" href="{{ route('orders.index') }}"><i class="fa-solid fa-box-open me-1"></i>Orders</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link store-nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-regular fa-user me-1"></i>{{ auth()->user()->name }}</a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fa-regular fa-user me-2"></i>Profile</a></li>
                            @if(auth()->user()->isAdmin())
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item fw-semibold" href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-gauge-high me-2"></i>Admin Dashboard</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</button></form></li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link store-nav-link" href="{{ route('login') }}"><i class="fa-solid fa-right-to-bracket me-1"></i>Login</a></li>
                    @if(Route::has('register'))<li class="nav-item"><a class="btn btn-dark rounded-3 px-3 ms-lg-1" href="{{ route('register') }}">Register</a></li>@endif
                @endauth
            </ul>
        </div>
    </div>
</nav>
<main class="store-main" id="main">
    @if(session('success'))<div class="container pt-3"><div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert"><i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>@endif
    @if(session('error'))<div class="container pt-3"><div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert"><i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>@endif
    @yield('content')
</main>
<section class="trust-strip" aria-label="Why shop with us">
    <div class="container"><div class="row g-0">
        <div class="col-6 col-lg-3"><div class="trust-item"><span class="trust-ico"><i class="fa-solid fa-truck-fast"></i></span><div><strong>Fast delivery</strong><small>Dhaka {{ config('shop.delivery.inside_days') }} days &bull; Outside {{ config('shop.delivery.outside_days') }} days</small></div></div></div>
        <div class="col-6 col-lg-3"><div class="trust-item"><span class="trust-ico"><i class="fa-solid fa-hand-holding-dollar"></i></span><div><strong>Cash on Delivery</strong><small>Pay when you receive</small></div></div></div>
        <div class="col-6 col-lg-3"><div class="trust-item"><span class="trust-ico"><i class="fa-solid fa-rotate-left"></i></span><div><strong>{{ (int) config('shop.return_days') }}-day returns</strong><small>Easy return policy</small></div></div></div>
        <div class="col-6 col-lg-3"><div class="trust-item"><span class="trust-ico"><i class="fa-solid fa-shield-halved"></i></span><div><strong>Secure checkout</strong><small>bKash &bull; Nagad &bull; Card</small></div></div></div>
    </div></div>
</section>
<footer class="store-footer">
    <div class="container"><div class="row g-4">
        <div class="col-md-5"><h5>IBL CART 24</h5><p class="mb-0">Shop smart. Shop premium. Quality products, convenient ordering and reliable delivery.</p>
        @if(filled(config('shop.phone')) || filled(config('shop.email')) || filled(config('shop.address')))
        <div class="footer-contact">
            @if(filled(config('shop.phone')))<span><i class="fa-solid fa-phone"></i> {{ config('shop.phone') }}</span>@endif
            @if(filled(config('shop.email')))<span><i class="fa-regular fa-envelope"></i> {{ config('shop.email') }}</span>@endif
            @if(filled(config('shop.address')))<span><i class="fa-solid fa-location-dot"></i> {{ config('shop.address') }}</span>@endif
        </div>
        @endif</div>
        <div class="col-6 col-md-3"><h5 class="h6">Quick Links</h5><div class="d-flex flex-column gap-2"><a href="{{ route('home') }}">Home</a><a href="{{ route('products.index') }}">Products</a><a href="{{ route('categories.index') }}">Categories</a><a href="{{ route('cart.index') }}">Cart</a></div></div>
        <div class="col-6 col-md-4"><h5 class="h6">Customer Care</h5><div class="d-flex flex-column gap-2">@auth<a href="{{ route('orders.index') }}">My Orders</a><a href="{{ route('profile.edit') }}">My Profile</a>@else<a href="{{ route('login') }}">Login</a>@endauth<a href="{{ route('cart.index') }}">Shopping Cart</a><a href="{{ route('track.order') }}">Track Your Order</a><a href="{{ route('pages.shipping') }}">Shipping Info</a><a href="{{ route('pages.returns') }}">Return Policy</a><a href="{{ route('pages.contact') }}">Contact Us</a></div><div class="pay-badges mt-3"><span>Cash on Delivery</span><span>bKash</span><span>Nagad</span><span>Card / Online</span></div></div>
    </div><div class="store-footer-bottom text-center"><div class="mb-2 d-flex flex-wrap justify-content-center gap-3"><a href="{{ route('pages.about') }}">About Us</a><a href="{{ route('pages.faq') }}">FAQ</a><a href="{{ route('pages.privacy') }}">Privacy Policy</a><a href="{{ route('pages.terms') }}">Terms &amp; Conditions</a></div>© {{ date('Y') }} IBL CART 24. All rights reserved.</div></div>
</footer>
@if(\App\Models\Setting::whatsappNumber() !== '')
<a href="https://wa.me/{{ \App\Models\Setting::whatsappNumber() }}?text={{ rawurlencode('Hello ' . config('shop.name') . ', I need some help.') }}" target="_blank" rel="noopener" aria-label="Chat on WhatsApp" class="wa-float"><i class="fa-brands fa-whatsapp"></i></a>
@endif
<nav class="mobile-tabbar" aria-label="Quick navigation">
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}"><i class="fa-solid fa-house"></i>Home</a>
    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*', 'categories.*') ? 'active' : '' }}"><i class="fa-solid fa-bag-shopping"></i>Shop</a>
    <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'active' : '' }}"><i class="fa-solid fa-cart-shopping"></i>Cart @if(($__cartQty ?? 0) > 0)<span class="tab-badge">{{ $__cartQty }}</span>@endif</a>
    @auth
        <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*', 'profile.*') ? 'active' : '' }}"><i class="fa-regular fa-user"></i>Account</a>
    @else
        <a href="{{ route('login') }}" class="{{ request()->routeIs('login', 'register') ? 'active' : '' }}"><i class="fa-regular fa-user"></i>Login</a>
    @endauth
</nav>
<button type="button" class="to-top" id="toTop" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        var b = document.getElementById('toTop');
        window.addEventListener('scroll', function () { b.classList.toggle('show', window.scrollY > 500); });
        b.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
    })();
</script>
<script>
    (function () {
        // Prevent double submits: show a spinner on the clicked button, re-enable if user comes back (bfcache)
        document.addEventListener('submit', function (e) {
            var f = e.target;
            if (!(f instanceof HTMLFormElement) || f.hasAttribute('data-no-lock') || e.defaultPrevented) return;
            var btn = e.submitter || f.querySelector('button[type="submit"], input[type="submit"]');
            if (!btn || btn.tagName !== 'BUTTON') return;
            if (f.dataset.locked === '1') { e.preventDefault(); return; }
            f.dataset.locked = '1';
            setTimeout(function () { btn.classList.add('is-loading'); btn.setAttribute('aria-busy', 'true'); }, 0);
            setTimeout(function () { f.dataset.locked = ''; btn.classList.remove('is-loading'); btn.removeAttribute('aria-busy'); }, 8000);
        });
        window.addEventListener('pageshow', function (ev) {
            if (!ev.persisted) return;
            document.querySelectorAll('form[data-locked="1"]').forEach(function (f) { f.dataset.locked = ''; });
            document.querySelectorAll('.btn.is-loading').forEach(function (b) { b.classList.remove('is-loading'); });
        });
        // Success flash messages fade out by themselves (errors stay)
        document.querySelectorAll('.alert-success.alert-dismissible').forEach(function (a) {
            setTimeout(function () {
                if (window.bootstrap && a.isConnected) { try { bootstrap.Alert.getOrCreateInstance(a).close(); } catch (err) {} }
            }, 6000);
        });
    })();
</script>
@stack('scripts')
</body>
</html>