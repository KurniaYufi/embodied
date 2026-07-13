@props(['items' => []])

<div class="overflow-hidden bg-neutral-900 py-3 text-white" aria-hidden="true">
    <div class="flex w-max animate-marquee whitespace-nowrap font-marquee text-[11px] tracking-[0.25em] uppercase">
        @for ($i = 0; $i < 2; $i++)
            @foreach ($items as $item)
                <span class="px-4">{{ $item }}</span>
                <span class="px-4" aria-hidden="true">&bull;</span>
            @endforeach
        @endfor
    </div>
</div>
