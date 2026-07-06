@props([])

<div class="overflow-hidden bg-neutral-900 py-3 text-white">
    <div class="flex w-max animate-marquee gap-8 whitespace-nowrap text-[11px] tracking-[0.25em] uppercase">
        @for ($i = 0; $i < 2; $i++)
            <span class="flex gap-8">{{ $slot }}</span>
        @endfor
    </div>
</div>
