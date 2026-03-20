<x-admin.panel title="Category Management">
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="grid gap-3 md:grid-cols-4">
            @csrf
            <input type="text" name="name" placeholder="Category name"
                class="rounded-md border-slate-300 md:col-span-3" required />
            <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Add Category</button>
        </form>
    </div>

    <div class="mt-6 rounded-xl bg-white p-6 shadow-sm">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b text-left text-slate-500">
                    <th class="py-2">Name</th>
                    <th class="py-2">Events</th>
                    <th class="py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr class="border-b">
                        <td class="py-2">
                            <form method="POST" action="{{ route('admin.categories.update', $category) }}"
                                class="flex gap-2">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $category->name }}"
                                    class="rounded-md border-slate-300" required />
                                <button
                                    class="rounded-md bg-emerald-600 px-3 py-1 text-xs font-semibold text-white">Save</button>
                            </form>
                        </td>
                        <td class="py-2">{{ $category->events_count }}</td>
                        <td class="py-2">
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                onsubmit="return confirm('Delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button
                                    class="rounded-md border border-rose-300 px-3 py-1 text-xs font-semibold text-rose-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $categories->links() }}</div>
    </div>
</x-admin.panel>
