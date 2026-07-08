const WISHLIST_KEY = 'embodied-wishlist';

function readWishlist() {
    try {
        return JSON.parse(localStorage.getItem(WISHLIST_KEY)) ?? [];
    } catch {
        return [];
    }
}

function toggleWishlist(name) {
    const items = readWishlist();
    const index = items.indexOf(name);

    if (index >= 0) {
        items.splice(index, 1);
    } else {
        items.push(name);
    }

    localStorage.setItem(WISHLIST_KEY, JSON.stringify(items));
    renderWishlistButtons();
}

function renderWishlistButtons() {
    const items = readWishlist();

    document.querySelectorAll('[data-wishlist-toggle]').forEach((button) => {
        const active = items.includes(button.dataset.wishlistToggle);
        button.classList.toggle('text-rose-500', active);
        button.classList.toggle('text-neutral-500', !active);
        button.setAttribute('aria-pressed', active ? 'true' : 'false');
    });
}

document.addEventListener('DOMContentLoaded', renderWishlistButtons);

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-wishlist-toggle]');

    if (button) {
        toggleWishlist(button.dataset.wishlistToggle);
    }
});
