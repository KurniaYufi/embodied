<button type="button" data-cart-open {{ $attributes->merge(['class' => 'relative']) }} aria-label="Open bag">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h15l-1.5 9h-12L6 3H3" />
        <circle cx="9" cy="20" r="1" fill="currentColor" />
        <circle cx="18" cy="20" r="1" fill="currentColor" />
    </svg>
    <span class="absolute -top-2 -right-2 flex h-4 w-4 items-center justify-center rounded-full bg-neutral-900 text-[9px] text-white"><span data-cart-count>0</span></span>
</button>
