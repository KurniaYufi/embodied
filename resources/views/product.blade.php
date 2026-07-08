<x-layout :title="$product->name . ' — ' . config('app.name', 'Embodied')">

    <header class="flex items-center justify-between border-b border-neutral-200 px-6 py-5 sm:px-10">
        <a href="{{ route('collection') }}" class="flex items-center gap-2 text-xs font-medium tracking-[0.25em] uppercase">
            <flux:icon.arrow-left class="h-4 w-4" /> Embodied
        </a>

        <h1 class="hidden font-serif text-xl sm:block">{{ $product->name }}</h1>

        <x-cart-trigger class="relative text-neutral-900" />
    </header>

    <div class="mx-auto grid max-w-6xl grid-cols-1 gap-12 px-6 py-12 sm:px-10 lg:grid-cols-2">

        {{-- Gallery --}}
        <div>
            <div class="relative aspect-4/5 w-full overflow-hidden bg-neutral-100">
                @if ($product->badge)
                    <span class="absolute top-4 left-4 z-10 bg-neutral-900 px-2 py-1 text-[10px] tracking-[0.15em] text-white uppercase">{{ $product->badge }}</span>
                @endif
                <x-product-image :image="$product->image_url" :gradient="$product->gradient" :alt="$product->name" />
            </div>
        </div>

        {{-- Details --}}
        <div>
            <p class="mb-2 text-xs tracking-[0.3em] text-neutral-500 uppercase">{{ $product->category?->name ?? 'Collection 01' }}</p>
            <h2 class="mb-3 font-serif text-3xl sm:text-4xl">{{ $product->name }}</h2>

            <p class="mb-4 text-xs text-neutral-500">
                {{ $product->stock > 0 ? "{$product->stock} in stock" : 'Out of stock' }}
            </p>

            <p class="mb-6 text-xl">{{ $product->formatted_price }}</p>

            <p class="mb-8 text-sm leading-relaxed text-neutral-600">{{ $product->description }}</p>

            @php
                $availableSizeLabels = $product->sizes->pluck('label');
                $firstAvailableSize = $product->sizes->first()?->label;
            @endphp

            <div data-product-form>
                <p class="mb-3 text-xs tracking-[0.2em] text-neutral-500 uppercase">Size</p>
                <div class="mb-8 flex flex-wrap gap-2">
                    @foreach ($allSizes as $size)
                        @php $isAvailable = $availableSizeLabels->contains($size->label); @endphp
                        <label class="{{ $isAvailable
                            ? 'cursor-pointer border border-neutral-300 px-4 py-2 text-xs tracking-[0.1em] uppercase has-checked:border-neutral-900 has-checked:bg-neutral-900 has-checked:text-white'
                            : 'cursor-not-allowed border border-neutral-200 px-4 py-2 text-xs tracking-[0.1em] text-neutral-300 uppercase line-through' }}">
                            <input
                                type="radio"
                                name="size"
                                value="{{ $size->label }}"
                                class="sr-only"
                                @disabled(! $isAvailable)
                                @checked($size->label === $firstAvailableSize)
                            >
                            {{ $size->label }}
                        </label>
                    @endforeach
                </div>

                <button
                    type="button"
                    class="w-full border border-neutral-900 bg-neutral-900 py-3 text-[11px] tracking-[0.2em] text-white uppercase hover:bg-black"
                    data-add-to-cart
                    data-name="{{ $product->name }}"
                    data-price-value="{{ $product->price }}"
                    data-gradient="{{ $product->gradient }}"
                    data-image="{{ $product->image_url }}"
                    data-size="{{ $product->sizes->first()?->label }}"
                >
                    Add to Bag
                </button>
            </div>

            <div class="mt-10 border-t border-neutral-200 pt-6 text-xs text-neutral-500">
                <p class="mb-1">Free shipping on orders over Rp 750.000.</p>
                <p>Handmade in small batches — allow 3–5 days for dispatch.</p>
            </div>
        </div>
    </div>
</x-layout>
