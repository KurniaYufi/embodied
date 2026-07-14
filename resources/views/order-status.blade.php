@php
    use App\Enums\OrderStatus;

    $canUploadProof = in_array($order->status, [OrderStatus::PendingPayment, OrderStatus::AwaitingConfirmation], true);

    $canReview = auth()->check() && auth()->id() === $order->user_id && $order->status === OrderStatus::Completed;

    $reviewedProductIds = $canReview
        ? auth()->user()->reviews()->whereIn('product_id', $order->items->pluck('product_id')->filter())->pluck('rating', 'product_id')
        : collect();

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
            <flux:icon.arrow-left class="h-4 w-4" />
        </a>

        <h1 class="font-serif text-xl">Order Status</h1>

        <span class="w-4"></span>
    </header>

    <div class="mx-auto max-w-3xl px-6 py-12 sm:px-10">

        @if ($order->status === OrderStatus::PendingPayment)
            <div
                data-payment-countdown
                data-deadline="{{ $order->payment_deadline->toIso8601String() }}"
                data-state="normal"
                class="group mb-8 flex flex-wrap items-center gap-4 border-2 border-amber-500 bg-amber-50 px-6 py-4 text-amber-900 transition-colors data-[state=critical]:border-red-600 data-[state=critical]:bg-red-50 data-[state=critical]:text-red-900"
            >
                <flux:icon.clock class="h-8 w-8 shrink-0" />
                <div>
                    <p class="text-xs font-medium tracking-widest uppercase">Complete Payment Within</p>
                    <p data-payment-countdown-value class="font-serif text-3xl tabular-nums group-data-[state=critical]:animate-pulse">10:00</p>
                </div>
                <p class="text-xs opacity-80 sm:ml-auto sm:max-w-2xs">This order will be automatically cancelled if payment isn't completed in time.</p>
            </div>
        @endif

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

        <div class="mb-4 flex flex-wrap items-center justify-between gap-4 border border-neutral-200 bg-cream p-6">
            <div>
                <p class="text-xs tracking-[0.2em] text-neutral-500 uppercase">Order Number</p>
                <p class="font-serif text-2xl">{{ $order->number }}</p>
                <p class="mt-1 text-xs text-neutral-500">Placed {{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="text-left sm:text-right">
                <flux:badge :color="$order->status->color()" size="lg">{{ $order->status->label() }}</flux:badge>
                <p class="mt-2 max-w-xs text-xs text-neutral-500">{{ $statusMessage }}</p>
            </div>
        </div>

        {{-- Items --}}
        <div class="mb-4 border border-neutral-200 bg-cream">
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

        @if ($canReview)
            {{-- Rate your purchase --}}
            <div class="mb-4 border border-neutral-200 bg-cream p-6">
                <p class="mb-4 text-xs tracking-[0.2em] text-neutral-500 uppercase">Rate Your Purchase</p>

                @error('rating')
                    <p class="mb-4 text-sm text-rose-600">{{ $message }}</p>
                @enderror

                <div class="divide-y divide-neutral-100">
                    @foreach ($order->items->unique('product_id') as $item)
                        @if ($item->product_id)
                            <div class="flex flex-wrap items-center justify-between gap-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 shrink-0 overflow-hidden bg-neutral-100">
                                        <x-product-image :image="$item->image" :gradient="$item->gradient" :alt="$item->product_name" />
                                    </div>
                                    <span class="text-sm">{{ $item->product_name }}</span>
                                </div>

                                <form method="POST" action="{{ route('orders.reviews.store', $order->access_token) }}" class="flex items-center gap-3" data-star-rating>
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                    <input type="hidden" name="rating" data-star-rating-input value="{{ $reviewedProductIds[$item->product_id] ?? '' }}">
                                    <div class="flex items-center gap-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <button type="button" data-star="{{ $i }}" aria-label="Rate {{ $i }} star{{ $i > 1 ? 's' : '' }}" class="text-neutral-300 hover:text-amber-400">
                                                <flux:icon.star variant="solid" class="h-5 w-5" />
                                            </button>
                                        @endfor
                                    </div>
                                    <button type="submit" class="text-[10px] tracking-widest text-neutral-500 uppercase underline underline-offset-4 hover:text-neutral-900">
                                        {{ isset($reviewedProductIds[$item->product_id]) ? 'Update' : 'Submit' }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Shipping details --}}
        <div class="mb-4 border border-neutral-200 bg-cream p-6">
            <p class="mb-3 text-xs tracking-[0.2em] text-neutral-500 uppercase">Shipping To</p>
            <p class="text-sm">{{ $order->customer_name }}</p>
            <p class="text-sm text-neutral-600">{{ $order->customer_phone }}</p>
            <p class="mt-1 text-sm whitespace-pre-line text-neutral-600">{{ $order->shipping_address }}</p>
            @if ($order->notes)
                <p class="mt-3 text-xs text-neutral-500">Notes: {{ $order->notes }}</p>
            @endif
        </div>

        @if ($canUploadProof)
            {{-- Payment method --}}
            <div class="mb-10 border border-neutral-200 bg-cream p-6" data-payment-method-picker>
                <p class="mb-4 text-xs tracking-[0.2em] text-neutral-500 uppercase">Payment Method</p>

                @if ($paymentMethods->isEmpty())
                    <p class="text-sm text-neutral-500">No payment methods are available right now. Please contact us to complete your payment.</p>
                @else
                    <div class="mb-6 flex flex-wrap gap-2">
                        @foreach ($paymentMethods as $method)
                            <button
                                type="button"
                                data-payment-method-tab="{{ $method->id }}"
                                class="border px-4 py-2 text-xs tracking-widest uppercase transition-colors {{ $loop->first ? 'border-neutral-900 bg-neutral-900 text-white' : 'border-neutral-300 hover:border-neutral-900' }}"
                            >
                                {{ $method->name }}
                            </button>
                        @endforeach
                    </div>

                    @foreach ($paymentMethods as $method)
                        <div data-payment-method-panel="{{ $method->id }}" class="{{ $loop->first ? '' : 'hidden' }}">
                            @if ($method->type === \App\Enums\PaymentMethodType::Bank)
                                <div class="space-y-1 text-sm">
                                    <p>{{ $method->name }}</p>
                                    <p>Account Number: <span class="font-medium">{{ $method->account_number }}</span></p>
                                    <p>Account Name: {{ $method->account_name }}</p>
                                    <p class="mt-2 text-neutral-600">Amount: <span class="font-medium text-neutral-900">{{ $order->formatted_subtotal }}</span></p>
                                </div>
                            @else
                                <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center">
                                    @if ($method->image_url)
                                        <img src="{{ $method->image_url }}" alt="{{ $method->name }} QR code" class="h-48 w-48 border border-neutral-200 object-contain">
                                    @endif
                                    <p class="text-sm text-neutral-600">Scan with your banking or e-wallet app.<br>Amount: <span class="font-medium text-neutral-900">{{ $order->formatted_subtotal }}</span></p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Proof upload --}}
            <div class="border border-neutral-200 bg-cream p-6">
                <p id="proof-heading" class="mb-3 text-xs tracking-[0.2em] text-neutral-500 uppercase">
                    {{ $order->hasPaymentProof() ? 'Replace Proof of Payment' : 'Upload Proof of Payment' }}
                </p>

                @if ($order->hasPaymentProof())
                    <img src="{{ $order->payment_proof_url }}" alt="Payment proof" class="mb-4 max-h-64 border border-neutral-200 object-contain">
                @endif

                <form method="POST" action="{{ route('orders.payment-proof', $order->access_token) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="payment_method_id" value="{{ $paymentMethods->first()?->id }}" data-payment-method-input>
                    <input type="file" name="proof" accept="image/*" required aria-labelledby="proof-heading" class="block w-full text-sm">
                    @error('payment_method_id')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="bg-neutral-900 px-6 py-3 text-[11px] tracking-[0.2em] text-white uppercase hover:bg-black">
                        Upload Proof
                    </button>
                </form>
            </div>
        @elseif ($order->hasPaymentProof())
            <div class="border border-neutral-200 bg-cream p-6">
                <p class="mb-3 text-xs tracking-[0.2em] text-neutral-500 uppercase">Proof of Payment</p>
                <img src="{{ $order->payment_proof_url }}" alt="Payment proof" class="max-h-64 border border-neutral-200 object-contain">
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => window.EmbodiedCart?.clearCart());
    </script>
</x-layout>
