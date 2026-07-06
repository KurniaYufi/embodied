@props(['rating' => 5, 'reviews' => 0])

<div {{ $attributes->merge(['class' => 'flex items-center gap-1 text-neutral-900']) }}>
    @for ($i = 1; $i <= 5; $i++)
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 {{ $i <= $rating ? 'text-neutral-900' : 'text-neutral-300' }}" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10 1.5l2.6 5.27 5.82.85-4.21 4.1.99 5.8L10 14.75l-5.2 2.77.99-5.8-4.21-4.1 5.82-.85z" />
        </svg>
    @endfor
    <span class="ml-1 text-xs text-neutral-500">{{ $reviews }}</span>
</div>
