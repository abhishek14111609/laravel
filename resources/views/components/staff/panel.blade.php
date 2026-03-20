@props(['title'])

<x-app-layout :show-navigation="false">
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-900">{{ $title }}</h2>
    </x-slot>

    <section class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-[240px_minmax(0,1fr)]">
            <aside class="rounded-2xl bg-white p-4 shadow-sm border border-slate-100 h-fit lg:sticky lg:top-24">
                <p class="px-2 pb-3 text-xs font-semibold uppercase tracking-wider text-slate-500">Staff Panel</p>
                <nav class="space-y-1 text-sm">
                    <a href="{{ route('staff.dashboard') }}"
                        class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('staff.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Dashboard</a>
                </nav>
            </aside>

            <div class="min-w-0">
                {{ $slot }}
            </div>
        </div>
    </section>
</x-app-layout>
