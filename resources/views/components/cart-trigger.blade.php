<button type="button" data-cart-open {{ $attributes->merge(['class' => 'relative']) }} aria-label="Open bag">
    <flux:icon.shopping-bag class="h-5 w-5" />
    <span class="absolute -top-2 -right-2 flex h-4 w-4 items-center justify-center rounded-full bg-neutral-900 text-[9px] text-white"><span data-cart-count>0</span></span>
</button>
