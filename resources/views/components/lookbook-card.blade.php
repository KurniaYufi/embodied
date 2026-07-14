@props([
    'label',
    'price',
    'href' => '#',
    'gradient' => 'from-neutral-300 to-neutral-400',
    'image' => null,
])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'group block']) }}>
    <div class="relative aspect-3/4 w-full overflow-hidden bg-neutral-100">
        <x-product-image :image="$image" :gradient="$gradient" :alt="$label" class="transition-transform duration-500 group-hover:scale-105" />
    </div>
    <div class="mt-3 flex items-center justify-between text-xs tracking-[0.15em] uppercase">
        <span>{{ $label }}</span>
        <span class="text-neutral-500 normal-case">{{ $price }}</span>
    </div>
    <span class="mt-1 block text-xs tracking-[0.15em] text-neutral-500 uppercase underline underline-offset-4">Shop Look</span>
</a>
