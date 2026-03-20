<x-staff.panel title="Staff Dashboard">
    <div class="rounded-xl bg-white p-6 shadow-sm overflow-x-auto">
        <h3 class="text-lg font-semibold text-slate-900">Assigned Bookings</h3>
        <table class="mt-4 min-w-full text-sm">
            <thead>
                <tr class="border-b text-left text-slate-500">
                    <th class="py-2">Booking</th>
                    <th class="py-2">User</th>
                    <th class="py-2">Event</th>
                    <th class="py-2">Current Status</th>
                    <th class="py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assignedBookings as $assignment)
                    <tr class="border-b">
                        <td class="py-2">#{{ $assignment->booking->id }}</td>
                        <td class="py-2">{{ $assignment->booking->user->name }}</td>
                        <td class="py-2">{{ $assignment->booking->event->title }}</td>
                        <td class="py-2 capitalize">{{ $assignment->booking->status }}</td>
                        <td class="py-2">
                            <form method="POST"
                                action="{{ route('staff.bookings.update-status', $assignment->booking) }}"
                                class="flex gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="rounded-md border-slate-300 text-xs">
                                    @php
                                        $staffTransitions =
                                            [
                                                'pending' => ['approved', 'rejected'],
                                                'approved' => ['completed'],
                                            ][$assignment->booking->status] ?? [];
                                    @endphp
                                    @forelse ($staffTransitions as $status)
                                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                    @empty
                                        <option value="" selected disabled>No transition</option>
                                    @endforelse
                                </select>
                                <button @disabled(count($staffTransitions) === 0)
                                    class="rounded-md bg-slate-900 px-3 py-1 text-xs font-semibold text-white">Update</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-slate-500">No assigned bookings.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $assignedBookings->links() }}</div>
    </div>
</x-staff.panel>
