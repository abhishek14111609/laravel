@props(['title'])

<x-app-layout :show-navigation="false">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-slate-900">{{ $title }}</h2>
            @isset($actions)
                {{ $actions }}
            @endisset
        </div>
    </x-slot>

    <section class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-[240px_minmax(0,1fr)]">
            <aside class="rounded-2xl bg-white p-4 shadow-sm border border-slate-100 h-fit lg:sticky lg:top-24">
                <p class="px-2 pb-3 text-xs font-semibold uppercase tracking-wider text-slate-500">Admin Panel</p>
                <nav class="space-y-1 text-sm">
                    <a href="{{ route('admin.dashboard') }}"
                        class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Dashboard</a>
                    <a href="{{ route('admin.events.index') }}"
                        class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.events.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Events</a>
                    <a href="{{ route('admin.categories.index') }}"
                        class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.categories.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Categories</a>
                    <a href="{{ route('admin.bookings.index') }}"
                        class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.bookings.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Bookings</a>
                    <a href="{{ route('admin.users.index') }}"
                        class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.users.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Users</a>
                    <a href="{{ route('admin.reports.index') }}"
                        class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.reports.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Reports</a>
                </nav>
            </aside>

            <div class="min-w-0">
                {{ $slot }}
            </div>
        </div>
    </section>
</x-app-layout>
