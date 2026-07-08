@php
    use App\Enums\OrderStatus;

    $canUploadProof = in_array($order->status, [OrderStatus::PendingPayment, OrderStatus::AwaitingConfirmation], true);

    $statusMessage = match ($order->status) {
        OrderStatus::PendingPayment => 'Please transfer the total amount and upload your proof of payment below.',
        OrderStatus::AwaitingConfirmation => 'Thanks! We\'ve received your proof of payment and are confirming it now.',
        OrderStatus::Paid => 'Payment confirmed. Your order is being prepared.',
        OrderStatus::Shipped => 'Your order is on its way.',
        OrderStatus::Completed => 'This order is complete. Thank you for shopping with us.',
        OrderStatus::Cancelled => 'This order has been cancelled.',
    };
@endphp

<x-layout :title="'Order ' . $order->number . ' — ' . config('app.name', 'Embodied')">

    <header class="flex items-center justify-between border-b border-neutral-200 px-6 py-5 sm:px-10">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-xs font-medium tracking-[0.25em] uppercase">
            <flux:icon.arrow-left class="h-4 w-4" /> Embodied
        </a>

        <h1 class="font-serif text-xl">Order Status</h1>

        <span class="w-4"></span>
    </header>

    <div class="mx-auto max-w-3xl px-6 py-12 sm:px-10">

        @if (session('status'))
            <div class="mb-8 border border-neutral-900 bg-neutral-900 px-4 py-3 text-sm text-white">
                {{ session('status') }}
            </div>
        @endif

        @error('proof')
            <div class="mb-8 border border-rose-600 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ $message }}
            </div>
        @enderror

        <div class="mb-10 flex flex-wrap items-center justify-between gap-4 border border-neutral-200 bg-white p-6">
            <div>
                <p class="text-xs tracking-[0.2em] text-neutral-500 uppercase">Order Number</p>
                <p class="font-serif text-2xl">{{ $order->number }}</p>
                <p class="mt-1 text-xs text-neutral-500">Placed {{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="text-right">
                <flux:badge :color="$order->status->color()" size="lg">{{ $order->status->label() }}</flux:badge>
                <p class="mt-2 max-w-xs text-xs text-neutral-500">{{ $statusMessage }}</p>
            </div>
        </div>

        {{-- Items --}}
        <div class="mb-10 border border-neutral-200 bg-white">
            <div class="divide-y divide-neutral-100 px-6">
                @foreach ($order->items as $item)
                    <div class="flex items-center justify-between gap-4 py-4 text-sm">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 shrink-0 overflow-hidden bg-neutral-100">
                                <x-product-image :image="$item->image" :gradient="$item->gradient" :alt="$item->product_name" />
                            </div>
                            <div>
                                <p>{{ $item->product_name }}</p>
                                <p class="text-xs text-neutral-500">Size: {{ $item->size ?? 'One Size' }} &times; {{ $item->quantity }}</p>
                            </div>
                        </div>
                        <span>{{ $item->formatted_line_total }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between border-t border-neutral-200 px-6 py-4 text-sm tracking-[0.1em] uppercase">
                <span>Subtotal</span>
                <span>{{ $order->formatted_subtotal }}</span>
            </div>
        </div>

        {{-- Shipping details --}}
        <div class="mb-10 border border-neutral-200 bg-white p-6">
            <p class="mb-3 text-xs tracking-[0.2em] text-neutral-500 uppercase">Shipping To</p>
            <p class="text-sm">{{ $order->customer_name }}</p>
            <p class="text-sm text-neutral-600">{{ $order->customer_phone }}</p>
            <p class="mt-1 text-sm whitespace-pre-line text-neutral-600">{{ $order->shipping_address }}</p>
            @if ($order->notes)
                <p class="mt-3 text-xs text-neutral-500">Notes: {{ $order->notes }}</p>
            @endif
        </div>

        @if ($canUploadProof)
            {{-- Payment instructions --}}
            <div class="mb-10 border border-neutral-200 bg-white p-6">
                <p class="mb-3 text-xs tracking-[0.2em] text-neutral-500 uppercase">Bank Transfer</p>
                <div class="space-y-1 text-sm">
                    <p>Bank Central Asia (BCA)</p>
                    <p>Account Number: <span class="font-medium">1234567890</span></p>
                    <p>Account Name: PT Embodied Studio</p>
                    <p class="mt-2 text-neutral-600">Amount: <span class="font-medium text-neutral-900">{{ $order->formatted_subtotal }}</span></p>
                </div>
            </div>

            {{-- Proof upload --}}
            <div class="border border-neutral-200 bg-white p-6">
                <p id="proof-heading" class="mb-3 text-xs tracking-[0.2em] text-neutral-500 uppercase">
                    {{ $order->hasPaymentProof() ? 'Replace Proof of Payment' : 'Upload Proof of Payment' }}
                </p>

                @if ($order->hasPaymentProof())
                    <img src="{{ $order->payment_proof_url }}" alt="Payment proof" class="mb-4 max-h-64 border border-neutral-200 object-contain">
                @endif

                <form method="POST" action="{{ route('orders.payment-proof', $order->access_token) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="file" name="proof" accept="image/*" required aria-labelledby="proof-heading" class="block w-full text-sm">
                    <button type="submit" class="bg-neutral-900 px-6 py-3 text-[11px] tracking-[0.2em] text-white uppercase hover:bg-black">
                        Upload Proof
                    </button>
                </form>
            </div>
        @elseif ($order->hasPaymentProof())
            <div class="border border-neutral-200 bg-white p-6">
                <p class="mb-3 text-xs tracking-[0.2em] text-neutral-500 uppercase">Proof of Payment</p>
                <img src="{{ $order->payment_proof_url }}" alt="Payment proof" class="max-h-64 border border-neutral-200 object-contain">
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => window.EmbodiedCart?.clearCart());
    </script>
</x-layout>
