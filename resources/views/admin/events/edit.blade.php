<x-admin.panel title="Edit Event">
    <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data"
        class="rounded-xl bg-white p-6 shadow-sm space-y-4">
        @csrf
        @method('PUT')
        <div class="grid gap-4 md:grid-cols-2">
            <input type="text" name="title" value="{{ old('title', $event->title) }}"
                class="rounded-md border-slate-300" required />
            <select name="category_id" class="rounded-md border-slate-300" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected($event->category_id === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $event->price) }}"
                class="rounded-md border-slate-300" required />
            <input type="text" name="location" value="{{ old('location', $event->location) }}"
                class="rounded-md border-slate-300" required />
        </div>
        <textarea name="description" rows="4" class="w-full rounded-md border-slate-300" required>{{ old('description', $event->description) }}</textarea>
        <div>
            <label for="image" class="mb-1 block text-sm font-medium text-slate-700">Replace Event Image</label>
            <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp"
                class="w-full rounded-md border-slate-300 file:mr-4 file:rounded-md file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white" />
            <p class="mt-1 text-xs text-slate-500">Optional. Leave empty to keep current image.</p>
        </div>

        @if ($event->image_url)
            <div class="rounded-xl border border-slate-200 p-3">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Current Image</p>
                <img src="{{ $event->image_url }}" alt="{{ $event->title }}"
                    class="h-40 w-full rounded-lg object-cover">
            </div>
        @endif

        @foreach ($event->slots as $index => $slot)
            <div class="rounded-md border border-slate-200 p-4">
                <p class="font-semibold text-slate-900">Slot {{ $index + 1 }}</p>
                <input type="hidden" name="slots[{{ $index }}][id]" value="{{ $slot->id }}" />
                <div class="mt-3 grid gap-3 md:grid-cols-3">
                    <input type="date" name="slots[{{ $index }}][date]"
                        value="{{ $slot->date->format('Y-m-d') }}" class="rounded-md border-slate-300" required />
                    <input type="text" name="slots[{{ $index }}][slot]" value="{{ $slot->slot }}"
                        class="rounded-md border-slate-300" required />
                    <input type="number" min="1" name="slots[{{ $index }}][capacity]"
                        value="{{ $slot->capacity }}" class="rounded-md border-slate-300" required />
                </div>
            </div>
        @endforeach

        @for ($i = $event->slots->count(); $i < $event->slots->count() + 2; $i++)
            <div class="rounded-md border border-slate-200 p-4">
                <p class="font-semibold text-slate-900">New Slot {{ $i - $event->slots->count() + 1 }}</p>
                <div class="mt-3 grid gap-3 md:grid-cols-3">
                    <input type="date" name="slots[{{ $i }}][date]"
                        class="rounded-md border-slate-300" />
                    <input type="text" name="slots[{{ $i }}][slot]" placeholder="e.g. 5:00 PM - 7:00 PM"
                        class="rounded-md border-slate-300" />
                    <input type="number" min="1" name="slots[{{ $i }}][capacity]"
                        placeholder="Capacity" class="rounded-md border-slate-300" />
                </div>
            </div>
        @endfor

        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="is_active" value="1" @checked($event->is_active)>
            Active event
        </label>

        <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update Event</button>
    </form>
</x-admin.panel>
