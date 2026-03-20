@props([
    'action' => route('events.index'),
    'location' => request('location'),
    'occasion' => request('search'),
    'date' => request('date'),
    'compact' => false,
])

<form action="{{ $action }}" method="GET" class="soft-surface rounded-[28px] p-3 sm:p-4">
    <div class="grid grid-cols-1 gap-2 {{ $compact ? 'md:grid-cols-8' : 'md:grid-cols-10' }}">
        <label class="soft-search-field {{ $compact ? 'md:col-span-2' : 'md:col-span-3' }}">
            <span class="soft-search-icon" aria-hidden="true">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657 13.414 12.414a4 4 0 1 0-5.657 5.657 4 4 0 0 0 5.657-5.657l4.243 4.243" />
                </svg>
            </span>
            <input type="text" name="location" value="{{ $location }}" placeholder="Location"
                class="soft-search-input" />
        </label>

        <label class="soft-search-field {{ $compact ? 'md:col-span-3' : 'md:col-span-4' }}">
            <span class="soft-search-icon" aria-hidden="true">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" />
                </svg>
            </span>
            <input type="text" name="search" value="{{ $occasion }}" placeholder="Occasion"
                class="soft-search-input" />
        </label>

        <label class="soft-search-field {{ $compact ? 'md:col-span-2' : 'md:col-span-2' }}">
            <span class="soft-search-icon" aria-hidden="true">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10" />
                </svg>
            </span>
            <input type="date" name="date" value="{{ $date }}" class="soft-search-input" />
        </label>

        <button type="submit" class="soft-btn {{ $compact ? 'md:col-span-1' : 'md:col-span-1' }}">Search</button>
    </div>
</form>
