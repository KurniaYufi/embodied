@props(['rating' => 5, 'reviews' => 0])

<div {{ $attributes->merge(['class' => 'flex items-center gap-1 text-neutral-900']) }}>
    @for ($i = 1; $i <= 5; $i++)
        <flux:icon.star :variant="$i <= $rating ? 'solid' : 'outline'" class="h-3.5 w-3.5 {{ $i <= $rating ? 'text-neutral-900' : 'text-neutral-300' }}" />
    @endfor
    <span class="ml-1 text-xs text-neutral-500">{{ $reviews }}</span>
</div>
