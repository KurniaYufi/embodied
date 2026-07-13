@props([
    'name',
    'price',
    'stock' => 0,
    'badge' => null,
    'gradient' => 'from-neutral-200 to-neutral-300',
    'image' => null,
    'href' => '#',
])

<div class="group relative border border-neutral-200 bg-cream p-3 transition-all hover:shadow-md">
    <a href="{{ $href }}" class="absolute inset-0 z-0" aria-label="{{ $name }}"></a>

    <div class="pointer-events-none relative z-10 mb-4 aspect-4/5 w-full overflow-hidden bg-neutral-100">
        @if ($badge)
            <span class="absolute top-3 left-3 z-10 bg-neutral-900 px-2 py-1 text-[10px] tracking-[0.15em] text-white uppercase">{{ $badge }}</span>
        @endif
        <button
            type="button"
            class="pointer-events-auto absolute top-3 right-3 z-10 text-neutral-500 hover:text-rose-500"
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

    <h3 class="pointer-events-none relative z-10 text-sm font-medium">{{ $name }}</h3>
    <p class="pointer-events-none relative z-10 mb-2 text-sm text-neutral-500">{{ $price }}</p>

    <p class="pointer-events-none relative z-10 text-xs text-neutral-500">
        {{ $stock > 0 ? "Stock: {$stock}" : 'Out of stock' }}
    </p>
</div>
