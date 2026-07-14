<x-layout :title="'My Orders — ' . config('app.name', 'Embodied')">

    <header class="flex items-center justify-between border-b border-neutral-200 px-6 py-5 sm:px-10">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-xs font-medium tracking-[0.25em] uppercase">
            <flux:icon.arrow-left class="h-4 w-4" /> Embodied
        </a>

        <h1 class="font-serif text-xl">My Orders</h1>

        <x-cart-trigger class="relative text-neutral-900" />
    </header>

    <div class="mx-auto max-w-3xl px-6 py-12 sm:px-10">
        @if ($orders->isEmpty())
            <div class="border border-neutral-200 bg-cream p-12 text-center">
                <p class="mb-3 text-sm text-neutral-600">You haven't placed any orders yet.</p>
                <a href="{{ route('collection') }}" class="text-xs tracking-[0.15em] text-neutral-900 uppercase underline underline-offset-4">Start Shopping</a>
            </div>
        @else
            <div class="divide-y divide-neutral-200 border border-neutral-200 bg-cream">
                @foreach ($orders as $order)
                    <a href="{{ route('orders.show', $order->access_token) }}" class="flex flex-wrap items-center justify-between gap-4 p-6 transition-colors hover:bg-neutral-50">
                        <div>
                            <p class="font-serif text-lg">{{ $order->number }}</p>
                            <p class="mt-1 text-xs text-neutral-500">Placed {{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-6">
                            <span class="text-sm">{{ $order->formatted_subtotal }}</span>
                            <flux:badge :color="$order->status->color()">{{ $order->status->label() }}</flux:badge>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-layout>
