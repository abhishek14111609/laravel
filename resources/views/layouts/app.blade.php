@props(['showNavigation' => true])

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
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Sora:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endif

    <style>
        :root {
            --bg-main: #f5f3ef;
            --ink: #1d1b18;
            --brand: #c86b43;
        }

        body {
            font-family: 'Outfit', sans-serif;
            color: var(--ink);
            background: radial-gradient(circle at 14% 18%, #ddd6ce 0 18%, transparent 18%),
                radial-gradient(circle at 88% 9%, #e5dfd7 0 24%, transparent 24%),
                var(--bg-main);
            min-height: 100vh;
        }

        .brand-font {
            font-family: 'Sora', sans-serif;
        }

        .text-soft-ink {
            color: var(--ink);
        }

        .text-soft-muted {
            color: #6f675d;
        }

        .bg-soft-brand {
            background: #d28853;
        }

        .soft-surface {
            border: 1px solid rgba(255, 255, 255, 0.95);
            background: color-mix(in srgb, #fffdfa 90%, transparent);
            box-shadow: 0 10px 30px rgba(49, 31, 20, 0.08);
            backdrop-filter: blur(7px);
        }

        .soft-nav-link {
            transition: color .2s ease;
        }

        .soft-nav-link:hover,
        .soft-nav-link.is-active {
            color: var(--brand);
        }

        .soft-pill-link {
            border-radius: 999px;
            border: 1px solid #e8d8c4;
            padding: .55rem .95rem;
            color: var(--ink);
            font-size: .82rem;
            font-weight: 600;
            background: #fff9f1;
            transition: border-color .2s ease, background .2s ease;
        }

        .soft-pill-link:hover {
            border-color: #ddb68b;
            background: #fff4e6;
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

        .shadow-soft-sm {
            box-shadow: 0 6px 16px rgba(59, 38, 24, 0.1);
        }

        .shadow-soft-lg {
            box-shadow: 0 15px 28px rgba(59, 38, 24, 0.18);
        }
    </style>
</head>

<body class="antialiased">
    <div class="min-h-screen">
        @if ($showNavigation)
            @if (auth()->user()?->isUser())
                <x-site.navbar />
            @else
                @include('layouts.navigation')
            @endif
        @endif

        <!-- Page Heading -->
        @isset($header)
            <header class="max-w-7xl mx-auto pt-5 px-4 sm:px-6 lg:px-8">
                <div
                    class="rounded-2xl bg-white/85 backdrop-blur border border-white/80 shadow-[0_10px_35px_rgba(60,35,20,0.08)] py-5 px-5 sm:px-7">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
</body>

</html>
