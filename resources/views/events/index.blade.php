<x-guest-layout>
    <section class="mx-auto max-w-7xl px-4 pb-8 pt-6 sm:px-6 lg:px-8 lg:pt-9">
        <div class="fade-up mt-2 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="display-font text-xs uppercase tracking-[0.24em] text-soft-muted">Explore</p>
                <h1 class="display-font mt-2 text-3xl font-semibold text-soft-ink sm:text-[2.45rem]">Find Your Next Event
                </h1>
            </div>
            <p class="text-sm text-soft-muted">{{ $events->total() }} events available</p>
        </div>

        <div class="fade-up delay-1 mt-6">
            <x-site.search-bar :compact="true" />
        </div>

        <form method="GET" action="{{ route('events.index') }}"
            class="fade-up delay-1 mt-3 soft-surface rounded-[26px] p-3 sm:p-4">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="location" value="{{ request('location') }}">
            <input type="hidden" name="date" value="{{ request('date') }}">
            <div class="grid gap-2 md:grid-cols-8">
                <div class="md:col-span-3">
                    <select name="category" class="soft-filter-input">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <input type="number" step="0.01" name="price_min" value="{{ request('price_min') }}"
                        placeholder="Min Price" class="soft-filter-input" />
                </div>
                <div class="md:col-span-2">
                    <input type="number" step="0.01" name="price_max" value="{{ request('price_max') }}"
                        placeholder="Max Price" class="soft-filter-input" />
                </div>
                <div class="md:col-span-1">
                    <button class="soft-btn w-full">Apply</button>
                </div>
            </div>
        </form>

        <div class="fade-up delay-2 mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($events as $event)
                <x-site.event-card :event="$event" />
            @empty
                <div
                    class="sm:col-span-2 lg:col-span-3 rounded-[28px] border border-dashed border-[#ead6c1] bg-white/70 p-12 text-center text-soft-muted">
                    No events match your filters.
                </div>
            @endforelse
        </div>

        <div class="mt-10 pagination-soft">
            {{ $events->links() }}
        </div>
    </section>
</x-guest-layout>
