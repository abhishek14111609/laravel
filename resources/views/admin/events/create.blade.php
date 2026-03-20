<x-admin.panel title="Create Event">
    <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data"
        class="rounded-xl bg-white p-6 shadow-sm space-y-4">
        @csrf
        <div class="grid gap-4 md:grid-cols-2">
            <input type="text" name="title" placeholder="Event title" class="rounded-md border-slate-300" required />
            <select name="category_id" class="rounded-md border-slate-300" required>
                <option value="">Select category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <input type="number" step="0.01" min="0" name="price" placeholder="Price"
                class="rounded-md border-slate-300" required />
            <input type="text" name="location" placeholder="Location" class="rounded-md border-slate-300" required />
        </div>
        <textarea name="description" rows="4" placeholder="Description" class="w-full rounded-md border-slate-300"
            required></textarea>
        <div>
            <label for="image" class="mb-1 block text-sm font-medium text-slate-700">Event Image</label>
            <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp"
                class="w-full rounded-md border-slate-300 file:mr-4 file:rounded-md file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white" />
            <p class="mt-1 text-xs text-slate-500">Optional. Max 2MB. JPG, PNG, or WEBP.</p>
        </div>

        <div class="rounded-md border border-slate-200 p-4">
            <p class="font-semibold text-slate-900">Slot 1</p>
            <div class="mt-3 grid gap-3 md:grid-cols-3">
                <input type="date" name="slots[0][date]" class="rounded-md border-slate-300" required />
                <input type="text" name="slots[0][slot]" placeholder="e.g. 10:00 AM - 12:00 PM"
                    class="rounded-md border-slate-300" required />
                <input type="number" min="1" name="slots[0][capacity]" placeholder="Capacity"
                    class="rounded-md border-slate-300" required />
            </div>
        </div>

        <div class="rounded-md border border-slate-200 p-4">
            <p class="font-semibold text-slate-900">Slot 2</p>
            <div class="mt-3 grid gap-3 md:grid-cols-3">
                <input type="date" name="slots[1][date]" class="rounded-md border-slate-300" required />
                <input type="text" name="slots[1][slot]" placeholder="e.g. 2:00 PM - 4:00 PM"
                    class="rounded-md border-slate-300" required />
                <input type="number" min="1" name="slots[1][capacity]" placeholder="Capacity"
                    class="rounded-md border-slate-300" required />
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="is_active" value="1" checked>
            Active event
        </label>

        <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Save Event</button>
    </form>
</x-admin.panel>
