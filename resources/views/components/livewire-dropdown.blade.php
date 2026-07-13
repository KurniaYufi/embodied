@props(['bind', 'options' => [], 'selected' => null, 'placeholder' => null])

<div
    class="relative"
    x-data="{
        open: false,
        show() {
            const trigger = this.$refs.trigger;
            const menu = this.$refs.menu;
            const rect = trigger.getBoundingClientRect();

            menu.style.top = `${rect.bottom + 8}px`;
            menu.style.left = 'auto';
            menu.style.right = `${window.innerWidth - rect.right}px`;
            menu.style.minWidth = `${rect.width}px`;

            menu.showPopover();
            this.open = true;
        },
        close() {
            this.$refs.menu?.hidePopover();
            this.open = false;
        },
    }"
    @click.outside="close()"
    @keydown.escape.window="close()"
>
    <button
        x-ref="trigger"
        type="button"
        aria-haspopup="listbox"
        :aria-expanded="open"
        @click="open ? close() : show()"
        {{ $attributes->class('flex min-w-40 cursor-pointer items-center justify-between gap-2 border border-neutral-200 bg-cream px-3 py-2 text-xs tracking-widest text-neutral-900 uppercase shadow-xs transition-colors hover:border-neutral-900 focus:border-neutral-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-100 dark:hover:border-neutral-400') }}
    >
        <span>{{ ($selected !== null && $selected !== '' && isset($options[$selected])) ? $options[$selected] : ($placeholder ?? reset($options)) }}</span>
        <flux:icon.chevron-down class="h-3 w-3 shrink-0 text-neutral-500 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" />
    </button>

    <ul
        x-ref="menu"
        role="listbox"
        popover="manual"
        class="fixed m-0 max-h-72 overflow-y-auto border border-neutral-200 bg-cream shadow-lg dark:border-neutral-800 dark:bg-neutral-900"
    >
        @if ($placeholder)
            <li>
                <button
                    type="button"
                    role="option"
                    wire:click="$set('{{ $bind }}', '')"
                    @click="close()"
                    aria-selected="{{ ($selected === null || $selected === '') ? 'true' : 'false' }}"
                    class="block w-full px-4 py-2.5 text-left text-xs tracking-widest whitespace-nowrap uppercase transition-colors hover:bg-neutral-900 hover:text-white dark:hover:bg-neutral-100 dark:hover:text-neutral-900 {{ ($selected === null || $selected === '') ? 'bg-neutral-100 font-medium text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100' : 'text-neutral-700 dark:text-neutral-300' }}"
                >
                    {{ $placeholder }}
                </button>
            </li>
        @endif

        @foreach ($options as $value => $label)
            <li>
                <button
                    type="button"
                    role="option"
                    wire:click="$set('{{ $bind }}', '{{ $value }}')"
                    @click="close()"
                    aria-selected="{{ (string) $selected === (string) $value ? 'true' : 'false' }}"
                    class="block w-full px-4 py-2.5 text-left text-xs tracking-widest whitespace-nowrap uppercase transition-colors hover:bg-neutral-900 hover:text-white dark:hover:bg-neutral-100 dark:hover:text-neutral-900 {{ (string) $selected === (string) $value ? 'bg-neutral-100 font-medium text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100' : 'text-neutral-700 dark:text-neutral-300' }}"
                >
                    {{ $label }}
                </button>
            </li>
        @endforeach
    </ul>
</div>
