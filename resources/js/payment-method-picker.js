document.addEventListener('click', (event) => {
    const tab = event.target.closest('[data-payment-method-tab]');

    if (!tab) {
        return;
    }

    const picker = tab.closest('[data-payment-method-picker]');
    const id = tab.dataset.paymentMethodTab;

    picker.querySelectorAll('[data-payment-method-tab]').forEach((btn) => {
        const active = btn === tab;

        btn.classList.toggle('border-neutral-900', active);
        btn.classList.toggle('bg-neutral-900', active);
        btn.classList.toggle('text-white', active);
        btn.classList.toggle('border-neutral-300', !active);
    });

    picker.querySelectorAll('[data-payment-method-panel]').forEach((panel) => {
        panel.classList.toggle('hidden', panel.dataset.paymentMethodPanel !== id);
    });

    const input = document.querySelector('[data-payment-method-input]');

    if (input) {
        input.value = id;
    }
});
