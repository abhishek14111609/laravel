<x-admin.panel title="Booking Management">
    <div class="rounded-xl bg-white p-6 shadow-sm overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b text-left text-slate-500">
                    <th class="py-2">Booking</th>
                    <th class="py-2">User</th>
                    <th class="py-2">Event</th>
                    <th class="py-2">Status</th>
                    <th class="py-2">Payment</th>
                    <th class="py-2">Assign Staff</th>
                    <th class="py-2">Update Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $booking)
                    <tr class="border-b align-top">
                        <td class="py-2">
                            #{{ $booking->id }}<br>{{ $booking->date->format('d M Y') }}<br>{{ $booking->slot }}
                        </td>
                        <td class="py-2">{{ $booking->user->name }}</td>
                        <td class="py-2">{{ $booking->event->title }}</td>
                        <td class="py-2 capitalize">{{ $booking->status }}</td>
                        <td class="py-2 uppercase">{{ $booking->payment_status }}</td>
                        <td class="py-2">
                            <form method="POST" action="{{ route('admin.bookings.assign-staff', $booking) }}"
                                class="flex gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="staff_id" class="rounded-md border-slate-300 text-xs">
                                    <option value="">Select</option>
                                    @foreach ($staffUsers as $staff)
                                        <option value="{{ $staff->id }}" @selected($booking->staffAssignment?->staff_id === $staff->id)>
                                            {{ $staff->name }}</option>
                                    @endforeach
                                </select>
                                <button
                                    class="rounded-md bg-emerald-600 px-3 py-1 text-xs font-semibold text-white">Assign</button>
                            </form>
                        </td>
                        <td class="py-2">
                            <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}"
                                class="flex gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="rounded-md border-slate-300 text-xs">
                                    @php
                                        $adminTransitions =
                                            [
                                                'pending' => ['approved', 'rejected'],
                                            ][$booking->status] ?? [];
                                    @endphp
                                    @forelse ($adminTransitions as $status)
                                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                    @empty
                                        <option value="" selected disabled>No transition</option>
                                    @endforelse
                                </select>
                                <button @disabled(count($adminTransitions) === 0)
                                    class="rounded-md bg-slate-900 px-3 py-1 text-xs font-semibold text-white">Save</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $bookings->links() }}</div>
    </div>
</x-admin.panel>
