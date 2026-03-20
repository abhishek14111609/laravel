<x-admin.panel title="Reports">
    <div class="space-y-6">
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="grid gap-3 md:grid-cols-4">
                <input type="date" name="from" value="{{ optional($from)->format('Y-m-d') }}"
                    class="rounded-md border-slate-300" />
                <input type="date" name="to" value="{{ optional($to)->format('Y-m-d') }}"
                    class="rounded-md border-slate-300" />
                <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Apply</button>
            </form>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-xl bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Revenue</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">${{ number_format($revenue, 2) }}</p>
            </div>
            <div class="rounded-xl bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Booking Status Breakdown</p>
                <ul class="mt-3 space-y-1 text-sm text-slate-700">
                    @foreach ($bookingStatusSummary as $status => $count)
                        <li>{{ ucfirst($status) }}: {{ $count }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-900">Daily Revenue</h3>
            <table class="mt-3 min-w-full text-sm">
                <thead>
                    <tr class="border-b text-left text-slate-500">
                        <th class="py-2">Date</th>
                        <th class="py-2">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dailyRevenue as $row)
                        <tr class="border-b">
                            <td class="py-2">{{ $row->date }}</td>
                            <td class="py-2">${{ number_format($row->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin.panel>
