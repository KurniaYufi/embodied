<x-layout :title="$product['name'] . ' — ' . config('app.name', 'Embodied')">

    <header class="flex items-center justify-between border-b border-neutral-200 px-6 py-5 sm:px-10">
        <a href="{{ route('collection') }}" class="flex items-center gap-2 text-xs font-medium tracking-[0.25em] uppercase">
            <span aria-hidden="true">&larr;</span> Embodied
        </a>

        <h1 class="hidden font-serif text-xl sm:block">{{ $product['name'] }}</h1>

        <x-cart-trigger class="relative text-neutral-900" />
    </header>

    <div class="mx-auto grid max-w-6xl grid-cols-1 gap-12 px-6 py-12 sm:px-10 lg:grid-cols-2">

        {{-- Gallery --}}
        <div>
            <div class="relative aspect-4/5 w-full overflow-hidden bg-neutral-100">
                @if ($product['badge'])
                    <span class="absolute top-4 left-4 z-10 bg-neutral-900 px-2 py-1 text-[10px] tracking-[0.15em] text-white uppercase">{{ $product['badge'] }}</span>
                @endif
                <div class="h-full w-full bg-linear-to-br {{ $product['gradient'] }}"></div>
            </div>

            <div class="mt-4 grid grid-cols-4 gap-4">
                @for ($i = 0; $i < 4; $i++)
                    <div class="aspect-square w-full overflow-hidden bg-neutral-100">
                        <div class="h-full w-full bg-linear-to-br {{ $product['gradient'] }}"></div>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Details --}}
        <div>
            <p class="mb-2 text-xs tracking-[0.3em] text-neutral-500 uppercase">Collection 01</p>
            <h2 class="mb-3 font-serif text-3xl sm:text-4xl">{{ $product['name'] }}</h2>

            <x-star-rating :rating="$product['rating']" :reviews="$product['reviews'] . ' reviews'" class="mb-4" />

            <p class="mb-6 text-xl">{{ $product['price'] }}</p>

            <p class="mb-8 text-sm leading-relaxed text-neutral-600">{{ $product['description'] }}</p>

            <div data-product-form>
                <p class="mb-3 text-xs tracking-[0.2em] text-neutral-500 uppercase">Size</p>
                <div class="mb-8 flex flex-wrap gap-2">
                    @foreach ($product['sizes'] as $index => $size)
                        <label class="cursor-pointer border border-neutral-300 px-4 py-2 text-xs tracking-[0.1em] uppercase has-checked:border-neutral-900 has-checked:bg-neutral-900 has-checked:text-white">
                            <input type="radio" name="size" value="{{ $size }}" class="sr-only" @checked($index === 0)>
                            {{ $size }}
                        </label>
                    @endforeach
                </div>

                <button
                    type="button"
                    class="w-full border border-neutral-900 bg-neutral-900 py-3 text-[11px] tracking-[0.2em] text-white uppercase hover:bg-black"
                    data-add-to-cart
                    data-name="{{ $product['name'] }}"
                    data-price-value="{{ $product['priceValue'] }}"
                    data-gradient="{{ $product['gradient'] }}"
                    data-size="{{ $product['sizes'][0] }}"
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
