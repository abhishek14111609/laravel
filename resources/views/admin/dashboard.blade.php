<x-admin.panel title="Admin Dashboard">
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Users</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $totalUsers }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Bookings</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $totalBookings }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Revenue (Paid)</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">${{ number_format($totalRevenue, 2) }}</p>
        </div>
    </div>

    <div class="mt-8 rounded-xl bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-slate-900">Recent Bookings</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b text-left text-slate-500">
                        <th class="py-2">User</th>
                        <th class="py-2">Event</th>
                        <th class="py-2">Status</th>
                        <th class="py-2">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentBookings as $booking)
                        <tr class="border-b">
                            <td class="py-2">{{ $booking->user->name }}</td>
                            <td class="py-2">{{ $booking->event->title }}</td>
                            <td class="py-2 capitalize">{{ $booking->status }}</td>
                            <td class="py-2">{{ $booking->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin.panel>
