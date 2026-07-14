@props(['rating' => 0, 'count' => 0, 'size' => 'h-4 w-4'])

@if ($count > 0)
    <div {{ $attributes->merge(['class' => 'flex items-center gap-1.5']) }}>
        <div class="flex text-amber-400">
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <= round($rating))
                    <flux:icon.star variant="solid" class="{{ $size }}" />
                @else
                    <flux:icon.star variant="outline" class="{{ $size }} text-neutral-300" />
                @endif
            @endfor
        </div>
        <span class="text-xs text-neutral-500">{{ number_format($rating, 1) }} ({{ $count }})</span>
    </div>
@endif
