@props([
    'name',
    'price',
    'priceValue' => 0,
    'stock' => 0,
    'badge' => null,
    'gradient' => 'from-neutral-200 to-neutral-300',
    'image' => null,
    'size' => 'One Size',
    'href' => '#',
])

<div class="group relative">
    <a href="{{ $href }}" class="absolute inset-0 z-0" aria-label="{{ $name }}"></a>

    <div class="relative z-10 mb-4 aspect-4/5 w-full overflow-hidden bg-neutral-100">
        @if ($badge)
            <span class="absolute top-3 left-3 z-10 bg-neutral-900 px-2 py-1 text-[10px] tracking-[0.15em] text-white uppercase">{{ $badge }}</span>
        @endif
        <button
            type="button"
            class="absolute top-3 right-3 z-10 text-neutral-500 hover:text-rose-500"
            aria-label="Add {{ $name }} to wishlist"
            aria-pressed="false"
            data-wishlist-toggle="{{ $name }}"
        >
            <flux:icon.heart class="h-5 w-5" />
        </button>
        <x-product-image
            :image="$image"
            :gradient="$gradient"
            :alt="$name"
            class="transition-transform duration-500 group-hover:scale-105"
        />
    </div>

    <h3 class="relative z-10 text-sm font-medium">{{ $name }}</h3>
    <p class="relative z-10 mb-2 text-sm text-neutral-500">{{ $price }}</p>

    <p class="relative z-10 mb-3 text-xs text-neutral-500">
        {{ $stock > 0 ? "Stock: {$stock}" : 'Out of stock' }}
    </p>

    <button
        type="button"
        class="relative z-10 w-full border border-neutral-300 py-2 text-[11px] tracking-[0.2em] uppercase hover:border-neutral-900"
        data-add-to-cart
        data-name="{{ $name }}"
        data-price-value="{{ $priceValue }}"
        data-gradient="{{ $gradient }}"
        data-image="{{ $image }}"
        data-size="{{ $size }}"
    >
        Buy
    </button>
</div>
