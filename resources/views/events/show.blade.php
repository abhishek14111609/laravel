<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl sm:text-2xl font-semibold text-slate-900">{{ $event->title }}</h2>
            <a href="{{ route('events.index') }}"
                class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-slate-700 hover:bg-slate-100">Back
                to Events</a>
        </div>
    </x-slot>

    <section class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8 space-y-8">
        @if (session('success'))
            <div class="rounded-xl bg-emerald-100 px-4 py-3 text-emerald-900">{{ session('success') }}</div>
        @endif

        <div class="grid gap-8 lg:grid-cols-3">
            <div
                class="lg:col-span-2 rounded-2xl bg-white p-6 shadow-[0_16px_32px_rgba(50,33,21,0.08)] border border-white/70">
                @if ($event->image_url)
                    <img src="{{ $event->image_url }}" alt="{{ $event->title }}"
                        class="h-64 w-full rounded-2xl object-cover" />
                @endif
                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <span
                        class="rounded-full bg-[#efcfae] px-3 py-1 text-xs font-semibold uppercase tracking-wider text-slate-800">{{ $event->category->name }}</span>
                    <span
                        class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $event->location }}</span>
                    <span
                        class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">${{ number_format($event->price, 2) }}</span>
                </div>
                <p class="mt-4 text-slate-700 leading-relaxed">{{ $event->description }}</p>
                @if ($event->image_url)
                    <p class="mt-4 text-xs text-slate-500">Ticket QR is generated automatically after checkout.</p>
                @endif
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-[0_16px_32px_rgba(50,33,21,0.08)] border border-white/70">
                <h3 class="text-lg font-semibold text-slate-900">Choose Your Slot</h3>
                @forelse ($event->slots as $slot)
                    <div class="mt-3 rounded-xl border border-slate-200 p-4">
                        <p class="text-sm font-medium text-slate-900">{{ $slot->date->format('d M, Y') }}</p>
                        <p class="text-sm text-slate-600">{{ $slot->slot }}</p>
                        <p class="mt-1 text-xs font-semibold text-[#a95430]">Available:
                            {{ $slot->availableSpots() }}</p>

                        @auth
                            @if (auth()->user()->isUser())
                                <form method="POST" action="{{ route('bookings.store', $event) }}" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="event_slot_id" value="{{ $slot->id }}" />
                                    <button
                                        class="w-full rounded-lg bg-[#c86b43] px-3 py-2 text-xs font-semibold text-white hover:bg-[#a95430]"
                                        @disabled($slot->availableSpots() <= 0)>Book This Slot</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                                class="mt-2 inline-flex text-xs font-semibold text-[#c86b43]">Login to book</a>
                        @endauth
                    </div>
                @empty
                    <p class="mt-3 text-sm text-slate-500">No slots configured.</p>
                @endforelse

                @auth
                    @if (auth()->user()->isUser())
                        <form method="POST" action="{{ route('wishlist.store', $event) }}" class="mt-4">
                            @csrf
                            <button
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-100">Add
                                to
                                Wishlist</button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-[0_16px_32px_rgba(50,33,21,0.08)] border border-white/70">
            <h3 class="text-lg font-semibold text-slate-900">Ratings and Reviews
                ({{ number_format($event->reviews_avg_rating ?? 0, 1) }}/5)</h3>

            @auth
                @if (auth()->user()->isUser())
                    <form method="POST" action="{{ route('reviews.store', $event) }}"
                        class="mt-4 grid gap-3 md:grid-cols-5 items-center">
                        @csrf
                        <select name="rating" class="rounded-lg border-slate-300 md:col-span-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }} Star</option>
                            @endfor
                        </select>
                        <input type="text" name="comment" placeholder="Share your experience"
                            class="rounded-lg border-slate-300 md:col-span-3" />
                        <button
                            class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white md:col-span-1">Submit</button>
                    </form>
                @endif
            @endauth

            <div class="mt-6 space-y-3">
                @forelse ($event->reviews as $review)
                    <article class="rounded-xl border border-slate-200 p-4">
                        <p class="text-sm font-semibold text-slate-900">{{ $review->user->name }} -
                            {{ $review->rating }}/5</p>
                        <p class="text-sm text-slate-600">{{ $review->comment }}</p>
                    </article>
                @empty
                    <p class="text-sm text-slate-500">No reviews yet.</p>
                @endforelse
            </div>
        </div>
    </section>
</x-app-layout>
