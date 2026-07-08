@props(['title', 'links'])

<div>
    <p class="mb-4 text-xs tracking-[0.2em] text-white/60 uppercase">{{ $title }}</p>
    <ul class="space-y-3 text-sm text-white/70">
        @foreach ($links as $label => $href)
            <li><a href="{{ $href }}" class="hover:text-white">{{ $label }}</a></li>
        @endforeach
    </ul>
</div>
