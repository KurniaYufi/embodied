<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div>
            <p class="mb-1 text-xs tracking-[0.3em] text-neutral-500 uppercase">Overview</p>
            <h1 class="font-serif text-3xl text-neutral-900">Dashboard</h1>
        </div>

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <a href="{{ route('seller.products') }}" wire:navigate class="border border-neutral-200 bg-white p-6 hover:border-neutral-400">
                <p class="mb-2 text-xs tracking-[0.2em] text-neutral-500 uppercase">Products</p>
                <p class="font-serif text-3xl text-neutral-900">{{ $productCount }}</p>
            </a>
            <a href="{{ route('seller.categories') }}" wire:navigate class="border border-neutral-200 bg-white p-6 hover:border-neutral-400">
                <p class="mb-2 text-xs tracking-[0.2em] text-neutral-500 uppercase">Categories</p>
                <p class="font-serif text-3xl text-neutral-900">{{ $categoryCount }}</p>
            </a>
            <a href="{{ route('seller.products') }}" wire:navigate class="border border-neutral-200 bg-white p-6 hover:border-neutral-400">
                <p class="mb-2 text-xs tracking-[0.2em] text-neutral-500 uppercase">Out of Stock</p>
                <p class="font-serif text-3xl text-neutral-900">{{ $outOfStockCount }}</p>
            </a>
            <a href="{{ route('seller.transactions') }}" wire:navigate class="border border-neutral-200 bg-white p-6 hover:border-neutral-400">
                <p class="mb-2 text-xs tracking-[0.2em] text-neutral-500 uppercase">Total Orders</p>
                <p class="font-serif text-3xl text-neutral-900">{{ $orderCount }}</p>
            </a>
            <a href="{{ route('seller.transactions') }}" wire:navigate class="border border-neutral-200 bg-white p-6 hover:border-neutral-400">
                <p class="mb-2 text-xs tracking-[0.2em] text-neutral-500 uppercase">Awaiting Confirmation</p>
                <p class="font-serif text-3xl text-neutral-900">{{ $awaitingConfirmationCount }}</p>
            </a>
        </div>

        <div class="h-full flex-1 border border-neutral-200 bg-white p-6">
            <p class="mb-4 text-xs tracking-[0.2em] text-neutral-500 uppercase">Recently Added Products</p>

            @if ($recentProducts->isEmpty())
                <p class="text-sm text-neutral-500">No products yet — add one from Master Data.</p>
            @else
                <ul class="divide-y divide-neutral-100">
                    @foreach ($recentProducts as $product)
                        <li class="flex items-center justify-between py-3 text-sm">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 shrink-0 overflow-hidden bg-neutral-100">
                                    <x-product-image :image="$product->image_url" :gradient="$product->gradient" :alt="$product->name" />
                                </div>
                                <span class="text-neutral-900">{{ $product->name }}</span>
                                <span class="text-neutral-500">{{ $product->category?->name ?? 'Uncategorized' }}</span>
                            </div>
                            <span class="text-neutral-500">{{ $product->formatted_price }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-layouts::app>
