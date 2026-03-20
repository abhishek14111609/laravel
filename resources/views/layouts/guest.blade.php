<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        :root {
            --bg-main: #f8f3eb;
            --bg-soft: #efe7dc;
            --surface: #ffffff;
            --ink: #28231e;
            --muted: #7b7268;
            --brand: #e88c53;
            --brand-dark: #d9783a;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--ink);
            background: radial-gradient(circle at 12% 12%, #eadfcd 0 14%, transparent 14%),
                radial-gradient(circle at 92% 16%, #efe4d5 0 19%, transparent 19%),
                radial-gradient(circle at 8% 84%, #e7dbc9 0 12%, transparent 12%),
                radial-gradient(circle at 86% 88%, #f0e5d8 0 18%, transparent 18%),
                var(--bg-main);
            min-height: 100vh;
        }

        .display-font {
            font-family: 'Poppins', sans-serif;
        }

        .soft-organic {
            position: fixed;
            z-index: 1;
            pointer-events: none;
            border-radius: 999px;
            filter: blur(0.2px);
            opacity: 0.55;
        }

        .soft-organic.one {
            top: -120px;
            right: -80px;
            width: 340px;
            height: 340px;
            background: #f2dcc4;
        }

        .soft-organic.two {
            bottom: -120px;
            left: -110px;
            width: 320px;
            height: 320px;
            background: #f4e6d4;
        }

        .soft-organic.three {
            top: 42%;
            left: 46%;
            width: 170px;
            height: 170px;
            background: #f0e3d4;
            opacity: 0.35;
        }

        .soft-surface {
            border: 1px solid rgba(255, 255, 255, 0.95);
            background: color-mix(in srgb, #fffdfa 90%, transparent);
            box-shadow: 0 10px 30px rgba(49, 31, 20, 0.08);
            backdrop-filter: blur(7px);
        }

        .soft-card {
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.94);
            background: #fffdfa;
            box-shadow: 0 10px 24px rgba(59, 38, 24, 0.09);
            transition: transform .35s ease, box-shadow .35s ease;
        }

        .soft-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 36px rgba(59, 38, 24, 0.14);
        }

        .soft-btn {
            background: var(--brand);
            color: #fff;
            border-radius: 999px;
            padding: .78rem 1.1rem;
            font-weight: 600;
            box-shadow: 0 9px 16px rgba(232, 140, 83, .27);
            transition: transform .22s ease, background .22s ease, box-shadow .22s ease;
            white-space: nowrap;
        }

        .soft-btn:hover {
            background: var(--brand-dark);
            box-shadow: 0 12px 22px rgba(217, 120, 58, .32);
            transform: translateY(-2px);
        }

        .soft-btn-sm {
            background: #f7ede4;
            color: var(--ink);
            border-radius: 999px;
            padding: .5rem .95rem;
            font-size: .75rem;
            font-weight: 600;
            transition: background .2s ease, transform .2s ease;
        }

        .soft-btn-sm:hover {
            background: #f0dcc8;
            transform: translateY(-1px);
        }

        .soft-search-field {
            border-radius: 999px;
            border: 1px solid #f0dfcb;
            background: #fff;
            display: flex;
            align-items: center;
            gap: .55rem;
            padding: .72rem .95rem;
        }

        .soft-search-icon {
            color: #b79f87;
            flex-shrink: 0;
        }

        .soft-search-input {
            width: 100%;
            border: 0;
            padding: 0;
            font-size: .9rem;
            color: var(--ink);
            background: transparent;
        }

        .soft-search-input:focus {
            outline: none;
            box-shadow: none;
        }

        .soft-filter-input {
            width: 100%;
            border-radius: 999px;
            border: 1px solid #f0dfcb;
            background: #fff;
            color: var(--ink);
            font-size: .9rem;
            padding: .78rem .95rem;
        }

        .soft-filter-input:focus {
            outline: none;
            border-color: #efcda9;
            box-shadow: 0 0 0 2px rgba(232, 140, 83, .18);
        }

        .soft-nav-link {
            transition: color .2s ease;
        }

        .soft-nav-link:hover,
        .soft-nav-link.is-active {
            color: var(--brand-dark);
        }

        .soft-pill-link {
            border-radius: 999px;
            border: 1px solid #f1dfca;
            padding: .55rem .9rem;
            color: var(--ink);
            font-size: .82rem;
            font-weight: 500;
            transition: border-color .2s ease, background .2s ease;
        }

        .soft-pill-link:hover {
            border-color: #efcda9;
            background: #fff8f1;
        }

        .soft-mobile-link {
            display: block;
            border-radius: 14px;
            padding: .55rem .7rem;
            color: var(--ink);
            font-size: .88rem;
            transition: background .2s ease;
        }

        .soft-mobile-link:hover {
            background: #faf3eb;
        }

        .soft-footer-link {
            transition: color .2s ease;
        }

        .soft-footer-link:hover {
            color: var(--brand-dark);
        }

        .soft-social {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #f0dfcb;
            color: var(--ink);
            background: #fff;
            transition: transform .2s ease, background .2s ease;
        }

        .soft-social:hover {
            background: #f8ebdd;
            transform: translateY(-1px);
        }

        .fade-up {
            animation: fadeUp .7s ease both;
        }

        .fade-up.delay-1 {
            animation-delay: .08s;
        }

        .fade-up.delay-2 {
            animation-delay: .16s;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .text-soft-ink {
            color: var(--ink);
        }

        .text-soft-muted {
            color: var(--muted);
        }

        .bg-soft-brand {
            background: var(--brand);
        }

        .shadow-soft-sm {
            box-shadow: 0 6px 16px rgba(59, 38, 24, 0.1);
        }

        .shadow-soft-lg {
            box-shadow: 0 15px 28px rgba(59, 38, 24, 0.18);
        }

        .pagination-soft nav {
            display: flex;
            justify-content: center;
        }

        .pagination-soft .relative.inline-flex.items-center {
            border-radius: 999px;
            border: 1px solid #f0dfcb;
            background: #fffdfa;
            box-shadow: 0 8px 18px rgba(59, 38, 24, .08);
            overflow: hidden;
        }

        .pagination-soft .relative.inline-flex.items-center a,
        .pagination-soft .relative.inline-flex.items-center span {
            border: 0;
            color: var(--ink);
            background: transparent;
        }

        .pagination-soft .relative.inline-flex.items-center a:hover {
            background: #f8efe3;
        }
    </style>
</head>

@php
    $isAuthPage = request()->routeIs('login', 'register', 'password.*', 'verification.*');
@endphp

<body class="antialiased">
    <span class="soft-organic one"></span>
    <span class="soft-organic two"></span>
    <span class="soft-organic three hidden lg:block"></span>

    @if ($isAuthPage)
        <div class="relative z-20">
            <x-site.navbar />
        </div>

        <div class="relative z-20 min-h-[calc(100vh-7rem)] flex flex-col sm:justify-center items-center px-4 py-10">
            <a href="{{ route('home') }}" class="mb-6 inline-flex items-center gap-3 fade-up">
                <x-application-logo class="w-10 h-10 fill-current text-[color:var(--brand)]" />
                <span class="display-font text-sm font-semibold uppercase tracking-[0.2em]">Eventory</span>
            </a>
            <div class="w-full sm:max-w-md px-6 py-6 rounded-[28px] soft-surface fade-up delay-1">
                {{ $slot }}
            </div>
        </div>
    @else
        <div class="relative z-20">
            <x-site.navbar />
        </div>

        <main class="relative z-20">
            {{ $slot }}
        </main>

        <div class="relative z-20">
            <x-site.footer />
        </div>
    @endif
</body>

</html>
