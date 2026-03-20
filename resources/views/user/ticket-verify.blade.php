<x-guest-layout>
    <section class="max-w-2xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-white p-6 shadow-[0_16px_32px_rgba(50,33,21,0.08)] border border-white/70">
            <h1 class="text-2xl font-semibold text-slate-900">Ticket Verification</h1>
            <p class="mt-2 text-sm text-slate-600">The ticket token is valid.</p>

            <dl class="mt-5 grid gap-2 text-sm">
                <div>
                    <dt class="font-semibold text-slate-800 inline">Booking:</dt>
                    <dd class="inline text-slate-600">#{{ $booking->id }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-800 inline">Name:</dt>
                    <dd class="inline text-slate-600">{{ $booking->user->name }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-800 inline">Event:</dt>
                    <dd class="inline text-slate-600">{{ $booking->event->title }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-800 inline">Date:</dt>
                    <dd class="inline text-slate-600">{{ $booking->date->format('d M, Y') }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-800 inline">Slot:</dt>
                    <dd class="inline text-slate-600">{{ $booking->slot }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-800 inline">Status:</dt>
                    <dd class="inline text-slate-600 capitalize">{{ $booking->status }}</dd>
                </div>
            </dl>
        </div>
    </section>
</x-guest-layout>
