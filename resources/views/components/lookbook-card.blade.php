@props([
    'label',
    'price',
    'href' => '#',
    'gradient' => 'from-neutral-300 to-neutral-400',
])

<a href="{{ $href }}" class="group block">
    <div class="relative aspect-3/4 w-full overflow-hidden bg-linear-to-br {{ $gradient }}">
        <span class="absolute bottom-3 left-3 text-xs tracking-[0.1em] text-white uppercase drop-shadow">{{ $label }}</span>
    </div>
    <div class="mt-3 flex items-center justify-between text-xs tracking-[0.15em] uppercase">
        <span class="underline underline-offset-4">Shop Look</span>
        <span class="text-neutral-500 normal-case">{{ $price }}</span>
    </div>
</a>
