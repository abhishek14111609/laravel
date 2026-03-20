<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-slate-900">My Ticket Dashboard</h2>
    </x-slot>

    <section class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 rounded-xl bg-emerald-100 px-4 py-3 text-emerald-900">{{ session('success') }}</div>
        @endif

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl bg-white p-5 shadow-[0_12px_28px_rgba(50,33,21,0.09)] border border-white/70">
                <p class="text-xs uppercase tracking-wider text-slate-500">Total Bookings</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $bookings->total() }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-[0_12px_28px_rgba(50,33,21,0.09)] border border-white/70">
                <p class="text-xs uppercase tracking-wider text-slate-500">Wishlist Items</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $wishlistCount }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-[0_12px_28px_rgba(50,33,21,0.09)] border border-white/70">
                <p class="text-xs uppercase tracking-wider text-slate-500">Pending Bookings</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $bookings->where('status', 'pending')->count() }}
                </p>
            </div>
        </div>

        <div class="mt-8 rounded-2xl bg-white p-6 shadow-[0_16px_32px_rgba(50,33,21,0.08)] border border-white/70">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-lg font-semibold text-slate-900">My Bookings</h3>
                <a href="{{ route('events.index') }}"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-slate-700 hover:bg-slate-100">Browse
                    Events</a>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-xs uppercase tracking-wider text-slate-500">
                            <th class="py-2">Event</th>
                            <th class="py-2">Date / Slot</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Payment</th>
                            <th class="py-2">Assigned Staff</th>
                            <th class="py-2">Ticket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr class="border-b last:border-0">
                                <td class="py-3 font-medium text-slate-900">{{ $booking->event->title }}</td>
                                <td class="py-3">{{ $booking->date->format('d M Y') }} / {{ $booking->slot }}</td>
                                <td class="py-3 capitalize">
                                    <span
                                        class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold">{{ $booking->status }}</span>
                                </td>
                                <td class="py-3 uppercase">{{ $booking->payment_status }}</td>
                                <td class="py-2">{{ $booking->staffAssignment?->staff?->name ?? 'Not assigned' }}
                                </td>
                                <td class="py-2">
                                    @if ($booking->qr_image_url)
                                        <a href="{{ $booking->qr_image_url }}" target="_blank"
                                            class="text-[#c86b43] font-semibold">View
                                            QR</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-slate-500">No bookings yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $bookings->links() }}</div>
        </div>
    </section>
</x-app-layout>
