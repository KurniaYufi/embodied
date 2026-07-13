function closeDropdown(root) {
    const trigger = root.querySelector('[data-dropdown-trigger]');
    const menu = root.querySelector('[data-dropdown-menu]');
    const chevron = root.querySelector('[data-dropdown-chevron]');

    trigger.setAttribute('aria-expanded', 'false');
    menu.classList.add('invisible', 'scale-95', 'opacity-0');
    chevron?.classList.remove('rotate-180');
}

function openDropdown(root) {
    document.querySelectorAll('[data-dropdown]').forEach((other) => {
        if (other !== root) {
            closeDropdown(other);
        }
    });

    const trigger = root.querySelector('[data-dropdown-trigger]');
    const menu = root.querySelector('[data-dropdown-menu]');
    const chevron = root.querySelector('[data-dropdown-chevron]');

    trigger.setAttribute('aria-expanded', 'true');
    menu.classList.remove('invisible', 'scale-95', 'opacity-0');
    chevron?.classList.add('rotate-180');
}

function isOpen(root) {
    return root.querySelector('[data-dropdown-trigger]').getAttribute('aria-expanded') === 'true';
}

function selectOption(root, option) {
    const hiddenInput = root.querySelector('[data-dropdown-value]');
    const label = root.querySelector('[data-dropdown-label]');

    hiddenInput.value = option.dataset.value;
    label.textContent = option.textContent.trim();

    root.querySelectorAll('[data-dropdown-option]').forEach((o) => {
        o.setAttribute('aria-selected', o === option ? 'true' : 'false');
    });

    closeDropdown(root);
    hiddenInput.form?.submit();
}

document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-dropdown-trigger]');
    if (trigger) {
        const root = trigger.closest('[data-dropdown]');
        isOpen(root) ? closeDropdown(root) : openDropdown(root);
        return;
    }

    const option = event.target.closest('[data-dropdown-option]');
    if (option) {
        selectOption(option.closest('[data-dropdown]'), option);
        return;
    }

    document.querySelectorAll('[data-dropdown]').forEach((root) => {
        if (!root.contains(event.target)) {
            closeDropdown(root);
        }
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        document.querySelectorAll('[data-dropdown]').forEach(closeDropdown);
    }
});
