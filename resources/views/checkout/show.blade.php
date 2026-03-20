<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-slate-900">Secure Checkout</h2>
    </x-slot>

    <section class="max-w-5xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="mb-4 rounded-xl bg-rose-100 px-4 py-3 text-rose-800">
                {{ $errors->first() }}
            </div>
        @endif

        @if ($booking->isReservationExpired())
            <div class="mb-4 rounded-xl bg-amber-100 px-4 py-3 text-amber-900">
                This reservation expired. Please book the slot again.
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-5">
            <div
                class="lg:col-span-2 rounded-2xl bg-white p-6 border border-white/70 shadow-[0_16px_32px_rgba(50,33,21,0.08)]">
                <h3 class="text-lg font-semibold text-slate-900">Booking Summary</h3>
                <div class="mt-4 space-y-2 text-sm text-slate-600">
                    <p><span class="font-medium text-slate-800">Booking:</span> #{{ $booking->id }}</p>
                    <p><span class="font-medium text-slate-800">Event:</span> {{ $booking->event->title }}</p>
                    <p><span class="font-medium text-slate-800">Date:</span> {{ $booking->date->format('d M, Y') }}</p>
                    <p><span class="font-medium text-slate-800">Slot:</span> {{ $booking->slot }}</p>
                </div>
                <div class="mt-5 rounded-xl bg-[#efcfae]/70 p-4">
                    <p class="text-xs uppercase tracking-wider text-slate-700">Total Amount</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">${{ number_format($booking->total_amount, 2) }}
                    </p>
                </div>
            </div>

            <div
                class="lg:col-span-3 rounded-2xl bg-white p-6 border border-white/70 shadow-[0_16px_32px_rgba(50,33,21,0.08)]">
                <h3 class="text-lg font-semibold text-slate-900">Payment Method</h3>
                <form method="POST" action="{{ route('checkout.process', $booking) }}" class="mt-5 space-y-4">
                    @csrf
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="rounded-xl border border-slate-300 p-4 text-sm text-slate-700 cursor-pointer">
                            <input type="radio" name="method" value="razorpay" @checked(($razorpayOrderId ?? null) !== null)
                                onclick="toggleRazorpay(true)" class="me-2" @disabled(($razorpayOrderId ?? null) === null || $booking->isReservationExpired())>
                            Razorpay
                            <p class="mt-1 text-xs text-slate-500">Pay online instantly.</p>
                        </label>
                        <label class="rounded-xl border border-slate-300 p-4 text-sm text-slate-700 cursor-pointer">
                            <input type="radio" name="method" value="cod" onclick="toggleRazorpay(false)"
                                class="me-2" @checked(($razorpayOrderId ?? null) === null) @disabled($booking->isReservationExpired())> Cash on
                            Delivery
                            <p class="mt-1 text-xs text-slate-500">Pay at event check-in.</p>
                        </label>
                    </div>

                    <div id="razorpay-field">
                        <label class="text-sm text-slate-700">Razorpay Order</label>
                        <input type="text"
                            value="{{ $razorpayOrderId ?? 'Unavailable (check Razorpay key configuration)' }}"
                            class="mt-1 w-full rounded-xl border-slate-300 bg-slate-50" readonly />
                        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id" />
                        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id"
                            value="{{ $razorpayOrderId }}" />
                        <input type="hidden" name="razorpay_signature" id="razorpay_signature" />
                        <p class="mt-1 text-xs text-slate-500">Order is created server-side and verified by signature
                            before payment is marked paid.</p>
                    </div>

                    <button id="checkout-submit"
                        class="rounded-xl bg-[#c86b43] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#a95430]"
                        @disabled($booking->isReservationExpired())>Complete
                        Payment</button>
                </form>
            </div>
        </div>
    </section>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        const form = document.querySelector('form[action="{{ route('checkout.process', $booking) }}"]');
        const submitBtn = document.getElementById('checkout-submit');
        const razorpayOrderId = "{{ $razorpayOrderId }}";
        const razorpayKey = "{{ $razorpayKey }}";

        function toggleRazorpay(show) {
            document.getElementById('razorpay-field').style.display = show ? 'block' : 'none';
        }

        toggleRazorpay(document.querySelector('input[name="method"]:checked')?.value === 'razorpay');

        if (form) {
            form.addEventListener('submit', function(e) {
                const method = document.querySelector('input[name="method"]:checked')?.value;

                if (method !== 'razorpay') {
                    return;
                }

                e.preventDefault();

                if (!razorpayOrderId || !razorpayKey || typeof Razorpay === 'undefined') {
                    alert('Razorpay is not available right now. Please use COD or configure Razorpay keys.');
                    return;
                }

                const options = {
                    key: razorpayKey,
                    amount: {{ (int) round(((float) $booking->total_amount) * 100) }},
                    currency: "{{ config('services.razorpay.currency', 'INR') }}",
                    name: "{{ config('app.name') }}",
                    description: "Booking #{{ $booking->id }}",
                    order_id: razorpayOrderId,
                    handler: function(response) {
                        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                        document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                        document.getElementById('razorpay_signature').value = response.razorpay_signature;
                        form.submit();
                    },
                    modal: {
                        ondismiss: function() {
                            submitBtn.disabled = false;
                        }
                    }
                };

                submitBtn.disabled = true;
                const rzp = new Razorpay(options);
                rzp.open();
            });
        }
    </script>
</x-app-layout>
