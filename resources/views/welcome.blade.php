<x-guest-layout>
    <section class="mx-auto max-w-7xl px-4 pb-8 pt-6 sm:px-6 lg:px-8 lg:pt-10">
        <div class="grid items-center gap-8 lg:grid-cols-12">
            <div class="fade-up lg:col-span-5">
                <p class="display-font text-xs uppercase tracking-[0.23em] text-soft-muted">Curated Event Experiences</p>
                <h1 class="display-font mt-4 text-3xl font-semibold leading-tight text-soft-ink sm:text-[2.55rem]">
                    GOOD TIMES ARE BETTER WHEN YOU PLAN THEM
                </h1>
                <p class="mt-4 max-w-xl text-sm leading-relaxed text-soft-muted sm:text-base">
                    Design your celebration with effortless bookings, thoughtful spaces, and a planning flow built for
                    premium events.
                </p>

                <div class="mt-6">
                    <x-site.search-bar :action="route('events.index')" />
                </div>

                <div class="mt-5">
                    <a href="{{ route('events.index') }}" class="soft-btn inline-flex">Book Now</a>
                </div>
            </div>

            <div class="fade-up delay-1 lg:col-span-7">
                <div class="relative overflow-hidden rounded-[36px] soft-card p-2 sm:p-3">
                    <img src="{{ $events->first()?->image_url ?: 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?q=80&w=1600&auto=format&fit=crop' }}"
                        alt="Event hall" class="h-[260px] w-full rounded-[30px] object-cover sm:h-[420px]" />

                    <div class="absolute bottom-5 left-5 right-5 sm:bottom-7 sm:left-7 sm:right-7">
                        <div
                            class="inline-flex items-center gap-2 rounded-full bg-white/92 px-4 py-2 text-xs font-medium text-soft-muted">
                            <span class="inline-block h-2.5 w-2.5 rounded-full bg-[#ebb17d]"></span>
                            Signature Venues and Private Gatherings
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="mt-14 fade-up delay-1">
            <h2 class="display-font text-center text-2xl font-semibold text-soft-ink sm:text-[2rem]">Popular Events</h2>
            <div class="mt-7 grid grid-cols-1 gap-7 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($events->take(4) as $event)
                    <article class="text-center">
                        <div
                            class="mx-auto h-40 w-40 overflow-hidden rounded-full border-4 border-white shadow-[0_12px_30px_rgba(44,29,18,0.16)] sm:h-44 sm:w-44">
                            <img src="{{ $event->image_url ?: 'https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=900&auto=format&fit=crop' }}"
                                alt="{{ $event->title }}" class="h-full w-full object-cover">
                        </div>
                        <h3 class="mt-4 text-base font-semibold text-soft-ink">{{ $event->title }}</h3>
                        <p class="mt-1 text-sm text-soft-muted">
                            {{ optional($event->slots->first()?->date)->format('d M, Y') ?: 'Date TBA' }}
                        </p>
                        <a href="{{ route('events.show', $event) }}" class="soft-btn-sm mt-3 inline-flex">Buy Now</a>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="mt-14 fade-up delay-2">
            <div class="mb-6 flex items-end justify-between gap-4">
                <div>
                    <p class="display-font text-xs uppercase tracking-[0.2em] text-soft-muted">Explore</p>
                    <h2 class="display-font mt-2 text-2xl font-semibold text-soft-ink sm:text-[2rem]">Featured Event
                        Cards</h2>
                </div>
                <a href="{{ route('events.index') }}" class="soft-btn-sm">View All</a>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($events->take(6) as $event)
                    <x-site.event-card :event="$event" :small="true" />
                @endforeach
            </div>
        </section>

        @if ($events->isEmpty())
            <div
                class="mt-10 rounded-[28px] border border-dashed border-[#ead6c1] bg-white/70 p-10 text-center text-soft-muted">
                No events are available yet.
            </div>
        @endif
    </section>
</x-guest-layout>
