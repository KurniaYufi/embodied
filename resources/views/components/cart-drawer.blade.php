<div data-cart-overlay class="fixed inset-0 z-40 hidden bg-black/40" data-cart-close></div>

<aside data-cart-drawer class="fixed top-0 right-0 z-50 flex h-full w-full max-w-md translate-x-full flex-col bg-stone-50 shadow-xl transition-transform duration-300">
    <div class="flex shrink-0 items-center justify-between border-b border-neutral-200 px-6 py-5">
        <p class="text-xs tracking-[0.25em] uppercase">Your Bag (<span data-cart-count>0</span>)</p>
        <button type="button" data-cart-close aria-label="Close bag" class="text-neutral-500 hover:text-neutral-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
            </svg>
        </button>
    </div>

    <div data-cart-list class="flex-1 overflow-y-auto"></div>
    <p data-cart-empty class="px-6 py-8 text-sm text-neutral-500">Your bag is empty.</p>

    <div class="shrink-0 border-t border-neutral-200 px-6 py-6">
        <div class="mb-2 flex items-center justify-between text-sm tracking-[0.1em] uppercase">
            <span>Subtotal</span>
            <span data-cart-subtotal>Rp 0</span>
        </div>
        <p class="mb-4 text-xs text-neutral-500">Shipping calculated at checkout</p>
        <button type="button" class="mb-3 w-full bg-neutral-900 py-3 text-[11px] tracking-[0.2em] text-white uppercase hover:bg-black">Checkout</button>
        <button type="button" data-cart-close class="w-full border border-neutral-300 py-3 text-[11px] tracking-[0.2em] uppercase hover:border-neutral-900">Continue Shopping</button>
    </div>
</aside>
