const CART_KEY = 'embodied-cart';

function readCart() {
    try {
        return JSON.parse(localStorage.getItem(CART_KEY)) ?? [];
    } catch {
        return [];
    }
}

function writeCart(items) {
    localStorage.setItem(CART_KEY, JSON.stringify(items));
    document.dispatchEvent(new CustomEvent('cart:updated'));
}

function addToCart({ name, priceValue, gradient, size }) {
    const items = readCart();
    const existing = items.find((item) => item.name === name && item.size === size);

    if (existing) {
        existing.qty += 1;
    } else {
        items.push({ name, priceValue, gradient, size, qty: 1 });
    }

    writeCart(items);
    openCart();
}

function updateQty(index, delta) {
    const items = readCart();
    const item = items[index];

    if (!item) {
        return;
    }

    item.qty += delta;

    if (item.qty <= 0) {
        items.splice(index, 1);
    }

    writeCart(items);
}

function formatRupiah(value) {
    return 'Rp ' + value.toLocaleString('id-ID');
}

function renderCart() {
    const list = document.querySelector('[data-cart-list]');

    if (!list) {
        return;
    }

    const items = readCart();
    const empty = document.querySelector('[data-cart-empty]');
    const subtotal = document.querySelector('[data-cart-subtotal]');

    list.innerHTML = '';
    empty?.classList.toggle('hidden', items.length > 0);

    let total = 0;
    let totalQty = 0;

    items.forEach((item, index) => {
        total += item.priceValue * item.qty;
        totalQty += item.qty;

        const row = document.createElement('div');
        row.className = 'flex gap-4 border-b border-neutral-200 px-6 py-6';
        row.innerHTML = `
            <div class="h-20 w-20 shrink-0 overflow-hidden bg-neutral-100">
                <div class="h-full w-full bg-linear-to-br ${item.gradient}"></div>
            </div>
            <div class="flex flex-1 flex-col">
                <p class="text-sm font-medium">${item.name}</p>
                <p class="mb-3 text-xs text-neutral-500">Size: ${item.size}</p>
                <div class="mt-auto flex items-center justify-between">
                    <div class="flex items-center gap-3 border border-neutral-300 px-3 py-1">
                        <button type="button" class="text-neutral-500 hover:text-neutral-900" data-qty-decrease="${index}">&minus;</button>
                        <span class="w-4 text-center text-sm">${item.qty}</span>
                        <button type="button" class="text-neutral-500 hover:text-neutral-900" data-qty-increase="${index}">&plus;</button>
                    </div>
                    <span class="text-sm">${formatRupiah(item.priceValue * item.qty)}</span>
                </div>
            </div>
        `;
        list.appendChild(row);
    });

    if (subtotal) {
        subtotal.textContent = formatRupiah(total);
    }

    document.querySelectorAll('[data-cart-count]').forEach((el) => {
        el.textContent = totalQty;
    });
}

function openCart() {
    document.querySelector('[data-cart-drawer]')?.classList.remove('translate-x-full');
    document.querySelector('[data-cart-overlay]')?.classList.remove('hidden');
}

function closeCart() {
    document.querySelector('[data-cart-drawer]')?.classList.add('translate-x-full');
    document.querySelector('[data-cart-overlay]')?.classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', renderCart);
document.addEventListener('cart:updated', renderCart);

document.addEventListener('click', (event) => {
    const addButton = event.target.closest('[data-add-to-cart]');
    if (addButton) {
        const selectedSize = addButton.closest('[data-product-form]')?.querySelector('input[name="size"]:checked');

        addToCart({
            name: addButton.dataset.name,
            priceValue: Number(addButton.dataset.priceValue),
            gradient: addButton.dataset.gradient,
            size: selectedSize?.value ?? addButton.dataset.size,
        });
        return;
    }

    if (event.target.closest('[data-cart-open]')) {
        openCart();
        return;
    }

    if (event.target.closest('[data-cart-close]')) {
        closeCart();
        return;
    }

    const decrease = event.target.closest('[data-qty-decrease]');
    if (decrease) {
        updateQty(Number(decrease.dataset.qtyDecrease), -1);
        return;
    }

    const increase = event.target.closest('[data-qty-increase]');
    if (increase) {
        updateQty(Number(increase.dataset.qtyIncrease), 1);
    }
});
