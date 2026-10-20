<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex">
        <meta name="theme-color" content="#4f46e5">

        <title>{{ config('shop.name', 'IBL CART 24') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('css/ibl-auth.css') }}?v=a1">
    </head>
    <body class="auth-body">
        <div class="auth-wrap">
            <a href="{{ route('home') }}" class="auth-brand">IBL <span>CART</span> 24</a>
            <div class="auth-tag">Shop smart. Shop premium.</div>

            <div class="auth-card">
                {{ $slot }}
            </div>

            @if(request()->routeIs('login') && Route::has('register'))
                <div class="auth-alt">New to {{ config('shop.name', 'IBL CART 24') }}? <a href="{{ route('register') }}">Create an account</a></div>
            @endif

            <div class="auth-perks">
                <span><i class="fa-solid fa-lock"></i>Secure login</span>
                <span><i class="fa-solid fa-truck-fast"></i>Fast delivery</span>
                <span><i class="fa-solid fa-hand-holding-dollar"></i>Cash on Delivery</span>
            </div>
            <div class="auth-back"><a href="{{ route('home') }}"><i class="fa-solid fa-arrow-left me-1"></i> Back to shop</a></div>
        </div>
        <script>
            document.addEventListener('submit', function (e) {
                var f = e.target, b = f && f.querySelector ? f.querySelector('button[type="submit"]') : null;
                if (!b) return;
                if (f.dataset.locked === '1') { e.preventDefault(); return; }
                f.dataset.locked = '1';
                setTimeout(function () { b.disabled = true; b.style.opacity = '.8'; }, 0);
            });
            window.addEventListener('pageshow', function (ev) {
                if (ev.persisted) document.querySelectorAll('form').forEach(function (f) { f.dataset.locked = ''; var b = f.querySelector('button[type="submit"]'); if (b) { b.disabled = false; b.style.opacity = ''; } });
            });
        </script>
    </body>
</html>