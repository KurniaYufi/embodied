@props([
    'name',
    'price',
    'priceValue' => 0,
    'rating' => 5,
    'reviews' => 0,
    'badge' => null,
    'gradient' => 'from-neutral-200 to-neutral-300',
    'size' => 'One Size',
    'href' => '#',
])

<div class="group relative">
    <a href="{{ $href }}" class="absolute inset-0 z-0" aria-label="{{ $name }}"></a>

    <div class="relative z-10 mb-4 aspect-4/5 w-full overflow-hidden bg-neutral-100">
        @if ($badge)
            <span class="absolute top-3 left-3 z-10 bg-neutral-900 px-2 py-1 text-[10px] tracking-[0.15em] text-white uppercase">{{ $badge }}</span>
        @endif
        <button type="button" class="absolute top-3 right-3 z-10 text-neutral-500 hover:text-rose-500" aria-label="Add to wishlist">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20s-7-4.35-9.5-8.5C.5 8 2 4.5 5.5 4c2-.3 3.5.7 4.5 2 1-1.3 2.5-2.3 4.5-2 3.5.5 5 4 3 7.5C19 15.65 12 20 12 20z" />
            </svg>
        </button>
        <div class="h-full w-full bg-linear-to-br {{ $gradient }} transition-transform duration-500 group-hover:scale-105"></div>
    </div>

    <h3 class="relative z-10 text-sm font-medium">{{ $name }}</h3>
    <p class="relative z-10 mb-2 text-sm text-neutral-500">{{ $price }}</p>

    <x-star-rating :rating="$rating" :reviews="$reviews" class="relative z-10 mb-3" />

    <button
        type="button"
        class="relative z-10 w-full border border-neutral-300 py-2 text-[11px] tracking-[0.2em] uppercase hover:border-neutral-900"
        data-add-to-cart
        data-name="{{ $name }}"
        data-price-value="{{ $priceValue }}"
        data-gradient="{{ $gradient }}"
        data-size="{{ $size }}"
    >
        Buy
    </button>
</div>
