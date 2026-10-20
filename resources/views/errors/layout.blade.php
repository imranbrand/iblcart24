<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('code') - {{ config('app.name', 'IBL CART 24') }}</title>
    <style>
        :root { --bg:#f6f7fb; --card:#fff; --ink:#111827; --muted:#6b7280; --brand:#4f46e5; --brand2:#7c3aed; }
        @media (prefers-color-scheme: dark) {
            :root { --bg:#0b1020; --card:#131a2e; --ink:#f3f4f6; --muted:#9ca3af; }
        }
        * { box-sizing: border-box; }
        body { margin:0; min-height:100vh; display:grid; place-items:center; padding:24px;
               background:var(--bg); color:var(--ink);
               font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Bengali", sans-serif; }
        .card { max-width:520px; width:100%; background:var(--card); border-radius:20px; padding:44px 32px;
                text-align:center; box-shadow:0 10px 40px rgba(0,0,0,.08); }
        .code { font-size:88px; font-weight:800; line-height:1; letter-spacing:-2px;
                background:linear-gradient(135deg,var(--brand),var(--brand2)); -webkit-background-clip:text;
                background-clip:text; color:transparent; }
        h1 { font-size:22px; margin:14px 0 8px; }
        p { color:var(--muted); line-height:1.6; margin:0 0 26px; }
        .actions { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; }
        a.btn { text-decoration:none; padding:12px 22px; border-radius:12px; font-weight:600; font-size:15px; }
        a.primary { background:linear-gradient(135deg,var(--brand),var(--brand2)); color:#fff; }
        a.ghost { border:1px solid rgba(127,127,127,.35); color:var(--ink); }
        .brand { margin-top:28px; font-size:13px; color:var(--muted); }
    </style>
</head>
<body>
    <main class="card">
        <div class="code">@yield('code')</div>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <div class="actions">
            <a class="btn primary" href="{{ url('/') }}">Back to shop</a>
            <a class="btn ghost" href="javascript:history.back()">Go back</a>
        </div>
        <div class="brand">{{ config('app.name', 'IBL CART 24') }}</div>
    </main>
</body>
</html>
