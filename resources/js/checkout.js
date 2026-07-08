function renderCheckoutSummary() {
    const list = document.querySelector('[data-checkout-items]');

    if (!list) {
        return;
    }

    const { readCart, formatRupiah, thumbnailHtml } = window.EmbodiedCart;
    const items = readCart();
    const empty = document.querySelector('[data-checkout-empty]');
    const form = document.querySelector('[data-checkout-form]');
    const subtotal = document.querySelector('[data-checkout-subtotal]');
    const itemsInput = document.querySelector('[data-checkout-items-input]');

    list.innerHTML = '';

    if (items.length === 0) {
        empty?.classList.remove('hidden');
        form?.classList.add('hidden');
        return;
    }

    empty?.classList.add('hidden');
    form?.classList.remove('hidden');

    let total = 0;

    items.forEach((item) => {
        total += item.priceValue * item.qty;

        const row = document.createElement('div');
        row.className = 'flex items-center justify-between gap-4 py-3 text-sm';
        row.innerHTML = `
            <div class="flex items-center gap-3">
                ${thumbnailHtml(item, 'h-12 w-12')}
                <div>
                    <p>${item.name}</p>
                    <p class="text-xs text-neutral-500">Size: ${item.size} &times; ${item.qty}</p>
                </div>
            </div>
            <span>${formatRupiah(item.priceValue * item.qty)}</span>
        `;
        list.appendChild(row);
    });

    if (subtotal) {
        subtotal.textContent = formatRupiah(total);
    }

    if (itemsInput) {
        itemsInput.value = JSON.stringify(items);
    }
}

document.addEventListener('DOMContentLoaded', renderCheckoutSummary);
