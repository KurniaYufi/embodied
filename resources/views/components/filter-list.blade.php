@props(['title', 'items'])

<div>
    <p class="mb-4 text-xs tracking-[0.2em] text-neutral-500 uppercase">{{ $title }}</p>
    <ul class="space-y-3 text-sm">
        @foreach ($items as $index => $label)
            <li>
                <a href="#" class="{{ $index === 0 ? 'font-medium hover:text-neutral-600' : 'text-neutral-700 hover:text-neutral-900' }}">{{ $label }}</a>
            </li>
        @endforeach
    </ul>
</div>
