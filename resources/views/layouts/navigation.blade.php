@php
    $authUser = Auth::user();
    $isAdmin = $authUser?->isAdmin();
    $isStaff = $authUser?->isStaff();
    $isUser = $authUser?->isUser();
@endphp

<nav x-data="{ open: false }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5">
    <div class="rounded-2xl bg-white/85 backdrop-blur border border-white/80 shadow-[0_12px_40px_rgba(50,33,21,0.08)]">
        <div class="flex justify-between items-center h-16 px-4 sm:px-6">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                    <x-application-logo class="block h-8 w-auto fill-current text-[#c86b43]" />
                    <span
                        class="brand-font text-xs font-semibold uppercase tracking-[0.2em] text-slate-700">Eventory</span>
                </a>

                <div class="hidden sm:flex items-center gap-6 text-sm font-medium text-slate-700">
                    @if ($isAdmin)
                        <a href="{{ route('admin.dashboard') }}"
                            class="hover:text-[#c86b43] {{ request()->routeIs('admin.*') ? 'text-[#c86b43]' : '' }}">Admin</a>
                        <a href="{{ route('admin.events.index') }}"
                            class="hover:text-[#c86b43] {{ request()->routeIs('admin.events.*') ? 'text-[#c86b43]' : '' }}">Events</a>
                        <a href="{{ route('admin.bookings.index') }}"
                            class="hover:text-[#c86b43] {{ request()->routeIs('admin.bookings.*') ? 'text-[#c86b43]' : '' }}">Bookings</a>
                        <a href="{{ route('admin.reports.index') }}"
                            class="hover:text-[#c86b43] {{ request()->routeIs('admin.reports.*') ? 'text-[#c86b43]' : '' }}">Reports</a>
                    @elseif ($isStaff)
                        <a href="{{ route('staff.dashboard') }}"
                            class="hover:text-[#c86b43] {{ request()->routeIs('staff.*') ? 'text-[#c86b43]' : '' }}">Staff</a>
                    @else
                        <a href="{{ route('home') }}"
                            class="hover:text-[#c86b43] {{ request()->routeIs('home') ? 'text-[#c86b43]' : '' }}">Home</a>
                        <a href="{{ route('events.index') }}"
                            class="hover:text-[#c86b43] {{ request()->routeIs('events.*') ? 'text-[#c86b43]' : '' }}">Events</a>
                        <a href="{{ route('user.dashboard') }}"
                            class="hover:text-[#c86b43] {{ request()->routeIs('user.*') ? 'text-[#c86b43]' : '' }}">My
                            Tickets</a>
                        <a href="{{ route('wishlist.index') }}"
                            class="hover:text-[#c86b43] {{ request()->routeIs('wishlist.*') ? 'text-[#c86b43]' : '' }}">Wishlist</a>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex items-center gap-3">
                @auth
                    <a href="{{ route('profile.edit') }}"
                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-[#c86b43]">{{ $authUser?->name }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            class="rounded-lg bg-[#c86b43] px-3 py-2 text-xs font-semibold text-white hover:bg-[#a95430]">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-[#c86b43]">Login</a>
                    <a href="{{ route('register') }}"
                        class="rounded-lg bg-[#c86b43] px-3 py-2 text-xs font-semibold text-white hover:bg-[#a95430]">Register</a>
                @endauth
            </div>

            <button @click="open = ! open"
                class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-slate-500 hover:text-slate-700 hover:bg-slate-100">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden border-t border-slate-200 px-4 py-3">
            <div class="space-y-2 text-sm font-medium text-slate-700">
                @if ($isAdmin)
                    <a href="{{ route('admin.dashboard') }}"
                        class="block rounded-md px-3 py-2 hover:bg-slate-100">Admin</a>
                    <a href="{{ route('admin.events.index') }}"
                        class="block rounded-md px-3 py-2 hover:bg-slate-100">Events</a>
                    <a href="{{ route('admin.bookings.index') }}"
                        class="block rounded-md px-3 py-2 hover:bg-slate-100">Bookings</a>
                    <a href="{{ route('admin.reports.index') }}"
                        class="block rounded-md px-3 py-2 hover:bg-slate-100">Reports</a>
                @elseif ($isStaff)
                    <a href="{{ route('staff.dashboard') }}"
                        class="block rounded-md px-3 py-2 hover:bg-slate-100">Staff</a>
                @else
                    <a href="{{ route('home') }}" class="block rounded-md px-3 py-2 hover:bg-slate-100">Home</a>
                    <a href="{{ route('events.index') }}"
                        class="block rounded-md px-3 py-2 hover:bg-slate-100">Events</a>
                    <a href="{{ route('user.dashboard') }}" class="block rounded-md px-3 py-2 hover:bg-slate-100">My
                        Tickets</a>
                    <a href="{{ route('wishlist.index') }}"
                        class="block rounded-md px-3 py-2 hover:bg-slate-100">Wishlist</a>
                @endif
                @auth
                    <a href="{{ route('profile.edit') }}" class="block rounded-md px-3 py-2 hover:bg-slate-100">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full rounded-md bg-[#c86b43] px-3 py-2 text-left text-white">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block rounded-md px-3 py-2 hover:bg-slate-100">Login</a>
                    <a href="{{ route('register') }}" class="block rounded-md px-3 py-2 hover:bg-slate-100">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
