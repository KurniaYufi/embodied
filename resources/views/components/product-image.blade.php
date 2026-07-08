@props([
    'image' => null,
    'gradient' => 'from-neutral-300 to-neutral-400',
    'alt' => '',
])

@if ($image)
    <img
        src="{{ $image }}"
        alt="{{ $alt }}"
        loading="lazy"
        {{ $attributes->merge(['class' => 'h-full w-full object-cover']) }}
    >
@else
    <div {{ $attributes->merge(['class' => 'h-full w-full bg-linear-to-br ' . $gradient]) }} role="img" aria-label="{{ $alt }}"></div>
@endif
