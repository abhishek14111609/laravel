@php
    $isAuthenticated = auth()->check();
    $authUser = auth()->user();

    $isUser = $isAuthenticated && $authUser->isUser();
@endphp

<header class="relative z-30 pt-5 sm:pt-7">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="soft-surface rounded-[30px] px-4 py-3 sm:px-6">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-soft-brand text-white">
                        <x-application-logo class="h-5 w-5 fill-current" />
                    </span>
                    <span class="tracking-tight text-lg font-semibold text-soft-ink">Eventory</span>
                </a>

                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-soft-muted">
                    <a href="{{ route('home') }}"
                        class="soft-nav-link {{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a>
                    <a href="{{ route('events.index') }}"
                        class="soft-nav-link {{ request()->routeIs('events.*') ? 'is-active' : '' }}">Events</a>
                </nav>

                <div class="hidden md:flex items-center gap-2">
                    @auth
                        @if ($isUser)
                            <a href="{{ route('user.dashboard') }}" class="soft-pill-link">My Tickets</a>
                            <a href="{{ route('wishlist.index') }}" class="soft-pill-link">Wishlist</a>
                        @endif
                        <a href="{{ route('profile.edit') }}" class="soft-pill-link">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="soft-pill-link">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="soft-pill-link">Sign In</a>
                        <a href="{{ route('register') }}" class="soft-pill-link">Register</a>
                    @endauth
                </div>

                <details class="relative md:hidden">
                    <summary
                        class="list-none inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-2xl border border-white/80 bg-white text-soft-ink shadow-soft-sm">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </summary>
                    <div
                        class="absolute right-0 mt-2 w-56 rounded-2xl border border-white/90 bg-[#fffdfa] p-2 shadow-soft-lg">
                        <a href="{{ route('home') }}" class="soft-mobile-link">Home</a>
                        <a href="{{ route('events.index') }}" class="soft-mobile-link">Events</a>
                        @auth
                            @if ($isUser)
                                <a href="{{ route('user.dashboard') }}" class="soft-mobile-link">My Tickets</a>
                                <a href="{{ route('wishlist.index') }}" class="soft-mobile-link">Wishlist</a>
                            @endif
                            <a href="{{ route('profile.edit') }}" class="soft-mobile-link">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="soft-mobile-link w-full text-left">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="soft-mobile-link">Sign In</a>
                            <a href="{{ route('register') }}" class="soft-mobile-link">Register</a>
                        @endauth
                    </div>
                </details>
            </div>
        </div>
    </div>
</header>
