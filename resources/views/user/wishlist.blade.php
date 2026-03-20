<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-slate-900">My Wishlist</h2>
    </x-slot>

    <section class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between gap-3">
            <p class="text-sm text-slate-600">Keep track of events you love and book when ready.</p>
            <a href="{{ route('events.index') }}"
                class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-slate-700 hover:bg-slate-100">Explore
                More</a>
        </div>

        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($wishlistItems as $item)
                <article
                    class="overflow-hidden rounded-2xl bg-white shadow-[0_16px_32px_rgba(50,33,21,0.1)] border border-white/70">
                    <img src="{{ $item->event->image_url ?: 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=1200&auto=format&fit=crop' }}"
                        alt="{{ $item->event->title }}" class="h-44 w-full object-cover">
                    <div class="p-5">
                        <p class="text-xs uppercase tracking-wider text-[#a95430]">{{ $item->event->category->name }}
                        </p>
                        <h3 class="mt-2 font-semibold text-slate-900">{{ $item->event->title }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ Str::limit($item->event->description, 80) }}</p>
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('events.show', $item->event) }}"
                                class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white">Open</a>
                            <form method="POST" action="{{ route('wishlist.destroy', $item->event) }}">
                                @csrf
                                @method('DELETE')
                                <button
                                    class="rounded-lg border border-rose-300 px-3 py-2 text-xs font-semibold text-rose-700">Remove</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div
                    class="md:col-span-2 lg:col-span-3 rounded-2xl border border-dashed border-slate-300 p-12 text-center text-slate-600">
                    Your wishlist is empty.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $wishlistItems->links() }}</div>
    </section>
</x-app-layout>
