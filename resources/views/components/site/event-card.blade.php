@props(['event', 'small' => false])

<article class="soft-card group {{ $small ? 'h-full' : '' }}">
    <div class="relative overflow-hidden rounded-[28px]">
        <img src="{{ $event->image_url ?: 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=1200&auto=format&fit=crop' }}"
            alt="{{ $event->title }}"
            class="h-52 w-full object-cover transition duration-700 ease-out group-hover:scale-105 {{ $small ? 'sm:h-40' : '' }}" />
        <span class="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1 text-[11px] font-medium text-soft-muted">
            {{ $event->category->name }}
        </span>
    </div>

    <div class="p-5 sm:p-6">
        <h3 class="text-lg font-semibold leading-tight text-soft-ink">{{ $event->title }}</h3>
        <p class="mt-2 text-sm text-soft-muted">
            {{ optional($event->slots->first()?->date)->format('d M, Y') ?: 'Date to be announced' }}
            <span class="px-1.5">|</span>
            {{ $event->location }}
        </p>

        <div class="mt-5 flex items-center justify-between">
            <span class="text-base font-semibold text-soft-ink">${{ number_format($event->price, 2) }}</span>
            <a href="{{ route('events.show', $event) }}"
                class="soft-btn-sm">{{ $small ? 'Buy Now' : 'View Event' }}</a>
        </div>
    </div>
</article>
