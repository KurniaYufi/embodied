function closeMobileNav() {
    document.querySelector('[data-mobile-nav]')?.classList.add('hidden');
    document.querySelector('[data-mobile-nav-toggle]')?.setAttribute('aria-expanded', 'false');
}

function openMobileNav() {
    document.querySelector('[data-mobile-nav]')?.classList.remove('hidden');
    document.querySelector('[data-mobile-nav-toggle]')?.setAttribute('aria-expanded', 'true');
}

document.addEventListener('click', (event) => {
    if (event.target.closest('[data-mobile-nav-toggle]')) {
        const panel = document.querySelector('[data-mobile-nav]');
        panel?.classList.contains('hidden') ? openMobileNav() : closeMobileNav();
        return;
    }

    if (event.target.closest('[data-mobile-nav-close]') || event.target.closest('[data-mobile-nav-link]')) {
        closeMobileNav();
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeMobileNav();
    }
});
