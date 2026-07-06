@props([
    'name',
    'price',
    'href' => '#',
    'badge' => null,
    'badgeClass' => 'bg-white text-neutral-900',
    'gradient' => 'from-neutral-200 to-neutral-300',
    'imageClass' => 'aspect-square',
])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'group block']) }}>
    <div class="relative overflow-hidden {{ $imageClass }}">
        @if ($badge)
            <span class="absolute top-3 left-3 z-10 px-2 py-1 text-[10px] tracking-[0.15em] uppercase {{ $badgeClass }}">{{ $badge }}</span>
        @endif
        <div class="h-full w-full bg-linear-to-br {{ $gradient }} transition-transform duration-500 group-hover:scale-105"></div>
    </div>
    <div class="flex items-center justify-between px-1 py-3 text-sm">
        <span>{{ $name }}</span>
        <span class="text-neutral-500">{{ $price }}</span>
    </div>
</a>
