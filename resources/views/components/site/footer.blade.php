@php
    $profileUrl = auth()->check() ? route('profile.edit') : route('login');
@endphp

<footer class="relative z-20 mt-16 pb-8 sm:pb-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="soft-surface rounded-[30px] px-6 py-6 sm:px-8 sm:py-7">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-lg font-semibold tracking-tight text-soft-ink">Eventory</p>
                    <p class="mt-1 text-sm text-soft-muted">Curated celebrations, thoughtfully planned.</p>
                </div>

                <nav class="flex flex-wrap items-center gap-4 text-sm text-soft-muted">
                    <a href="{{ route('events.index') }}" class="soft-footer-link">Events</a>
                    <a href="{{ route('home') }}" class="soft-footer-link">Home</a>
                    @auth
                        @if (auth()->user()->isUser())
                            <a href="{{ route('user.dashboard') }}" class="soft-footer-link">My Tickets</a>
                        @endif
                    @endauth
                    <a href="{{ $profileUrl }}" class="soft-footer-link">Profile</a>
                </nav>

                <div class="flex items-center gap-2">
                    <a href="#" class="soft-social" aria-label="Instagram">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="2" y="2" width="20" height="20" rx="6" ry="6"
                                stroke-width="2" />
                            <path d="M16 11.37a4 4 0 1 1-7.999.001A4 4 0 0 1 16 11.37Z" stroke-width="2" />
                            <path d="M17.5 6.5h.01" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </a>
                    <a href="#" class="soft-social" aria-label="Facebook">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.51 1.5-3.89 3.78-3.89 1.09 0 2.23.2 2.23.2v2.46H15.2c-1.24 0-1.63.77-1.63 1.56V12h2.77l-.44 2.89h-2.33v6.99A10 10 0 0 0 22 12Z" />
                        </svg>
                    </a>
                    <a href="#" class="soft-social" aria-label="X">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M18.9 2H22l-6.77 7.74L23 22h-6.14l-4.8-6.27L6.58 22H3.46l7.23-8.25L1 2h6.29l4.34 5.72L18.9 2Zm-1.08 18h1.7L6.37 3.9H4.53L17.82 20Z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
