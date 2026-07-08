<x-layout :title="'Checkout — ' . config('app.name', 'Embodied')">

    <header class="flex items-center justify-between border-b border-neutral-200 px-6 py-5 sm:px-10">
        <a href="{{ route('collection') }}" class="flex items-center gap-2 text-xs font-medium tracking-[0.25em] uppercase">
            <flux:icon.arrow-left class="h-4 w-4" /> Embodied
        </a>

        <h1 class="font-serif text-xl">Checkout</h1>

        <span class="w-4"></span>
    </header>

    <div class="mx-auto grid max-w-5xl grid-cols-1 gap-12 px-6 py-12 sm:px-10 lg:grid-cols-2">

        {{-- Order summary --}}
        <div>
            <p class="mb-4 text-xs tracking-[0.3em] text-neutral-500 uppercase">Your Bag</p>

            <div data-checkout-empty class="hidden border border-neutral-200 bg-cream p-8 text-center text-sm text-neutral-500">
                Your bag is empty.
                <a href="{{ route('collection') }}" class="mt-2 block text-neutral-900 underline underline-offset-4">Continue shopping</a>
            </div>

            <div data-checkout-items class="divide-y divide-neutral-100 border-t border-neutral-200"></div>

            <div class="mt-4 flex items-center justify-between border-t border-neutral-200 pt-4 text-sm tracking-[0.1em] uppercase">
                <span>Subtotal</span>
                <span data-checkout-subtotal>Rp 0</span>
            </div>
        </div>

        {{-- Details form --}}
        <div data-checkout-form>
            <p class="mb-4 text-xs tracking-[0.3em] text-neutral-500 uppercase">Shipping Details</p>

            <form method="POST" action="{{ route('checkout.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="items" data-checkout-items-input>

                <div>
                    <label for="customer_name" class="mb-1 block text-xs tracking-[0.1em] text-neutral-500 uppercase">Full Name</label>
                    <input id="customer_name" type="text" name="customer_name" value="{{ old('customer_name') }}" required class="w-full border border-neutral-300 px-3 py-2 text-sm focus:border-neutral-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2">
                    @error('customer_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="customer_phone" class="mb-1 block text-xs tracking-[0.1em] text-neutral-500 uppercase">Phone Number</label>
                    <input id="customer_phone" type="text" name="customer_phone" value="{{ old('customer_phone') }}" required class="w-full border border-neutral-300 px-3 py-2 text-sm focus:border-neutral-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2">
                    @error('customer_phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="shipping_address" class="mb-1 block text-xs tracking-[0.1em] text-neutral-500 uppercase">Shipping Address</label>
                    <textarea id="shipping_address" name="shipping_address" rows="3" required class="w-full border border-neutral-300 px-3 py-2 text-sm focus:border-neutral-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2">{{ old('shipping_address') }}</textarea>
                    @error('shipping_address') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="notes" class="mb-1 block text-xs tracking-[0.1em] text-neutral-500 uppercase">Notes (optional)</label>
                    <textarea id="notes" name="notes" rows="2" class="w-full border border-neutral-300 px-3 py-2 text-sm focus:border-neutral-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2">{{ old('notes') }}</textarea>
                </div>

                @error('items') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror

                <button type="submit" class="w-full bg-neutral-900 py-3 text-[11px] tracking-[0.2em] text-white uppercase hover:bg-black">
                    Continue to Payment
                </button>
            </form>
        </div>
    </div>
</x-layout>
