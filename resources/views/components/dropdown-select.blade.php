@props(['name', 'options' => [], 'selected' => null, 'form' => null])

<div class="relative inline-block" data-dropdown>
    <input type="hidden" name="{{ $name }}" value="{{ $selected }}" @if ($form) form="{{ $form }}" @endif data-dropdown-value>

    <button
        type="button"
        aria-haspopup="listbox"
        aria-expanded="false"
        data-dropdown-trigger
        {{ $attributes->class('flex min-w-40 cursor-pointer items-center justify-between gap-2 border border-neutral-300 bg-cream px-3 py-2 text-xs tracking-widest text-neutral-900 uppercase shadow-xs transition-colors hover:border-neutral-900 focus:border-neutral-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2') }}
    >
        <span data-dropdown-label>{{ $options[$selected] ?? reset($options) }}</span>
        <flux:icon.chevron-down data-dropdown-chevron class="h-3 w-3 shrink-0 text-neutral-500 transition-transform duration-200" />
    </button>

    <ul
        role="listbox"
        data-dropdown-menu
        class="invisible absolute right-0 z-20 mt-2 w-full min-w-max scale-95 border border-neutral-300 bg-cream opacity-0 shadow-lg transition duration-150 ease-out"
    >
        @foreach ($options as $value => $label)
            <li>
                <button
                    type="button"
                    role="option"
                    aria-selected="{{ (string) $selected === (string) $value ? 'true' : 'false' }}"
                    data-dropdown-option
                    data-value="{{ $value }}"
                    class="block w-full px-4 py-2.5 text-left text-xs tracking-widest whitespace-nowrap uppercase transition-colors hover:bg-neutral-900 hover:text-white {{ (string) $selected === (string) $value ? 'bg-neutral-100 font-medium text-neutral-900' : 'text-neutral-700' }}"
                >
                    {{ $label }}
                </button>
            </li>
        @endforeach
    </ul>
</div>
