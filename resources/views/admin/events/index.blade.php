<x-admin.panel title="Event Management">
    <x-slot name="actions">
        <a href="{{ route('admin.events.create') }}"
            class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Create Event</a>
    </x-slot>

    <div class="rounded-xl bg-white p-6 shadow-sm overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b text-left text-slate-500">
                    <th class="py-2">Title</th>
                    <th class="py-2">Category</th>
                    <th class="py-2">Price</th>
                    <th class="py-2">Location</th>
                    <th class="py-2">Slots</th>
                    <th class="py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($events as $event)
                    <tr class="border-b">
                        <td class="py-2">{{ $event->title }}</td>
                        <td class="py-2">{{ $event->category->name }}</td>
                        <td class="py-2">${{ number_format($event->price, 2) }}</td>
                        <td class="py-2">{{ $event->location }}</td>
                        <td class="py-2">{{ $event->total_slots }}</td>
                        <td class="py-2 flex gap-2">
                            <a href="{{ route('admin.events.edit', $event) }}"
                                class="rounded-md bg-emerald-600 px-3 py-1 text-xs font-semibold text-white">Edit</a>
                            <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                                onsubmit="return confirm('Delete this event?')">
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
        <div class="mt-4">{{ $events->links() }}</div>
    </div>
</x-admin.panel>
