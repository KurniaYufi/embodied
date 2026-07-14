document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-filters-toggle]');

    if (!toggle) {
        return;
    }

    const expanded = toggle.getAttribute('aria-expanded') === 'true';

    toggle.setAttribute('aria-expanded', String(!expanded));
    toggle.querySelector('[data-filters-chevron]')?.classList.toggle('rotate-180', !expanded);

    document.querySelectorAll('[data-filters-panel]').forEach((panel) => {
        panel.classList.toggle('hidden', expanded);
    });
});
