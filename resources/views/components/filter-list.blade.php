@props(['title', 'items', 'param', 'active' => ''])

<div>
    <p class="mb-4 text-xs tracking-[0.2em] text-neutral-500 uppercase">{{ $title }}</p>
    <ul class="space-y-3 text-sm">
        @foreach ($items as $label => $value)
            <li>
                <a
                    href="{{ request()->fullUrlWithQuery([$param => $value ?: null]) }}"
                    aria-current="{{ (string) $active === (string) $value ? 'true' : 'false' }}"
                    class="{{ (string) $active === (string) $value ? 'font-medium text-neutral-900 underline underline-offset-4' : 'text-neutral-700 hover:text-neutral-900' }}"
                >
                    {{ $label }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
