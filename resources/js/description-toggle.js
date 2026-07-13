function revealToggleIfClamped(description) {
    const toggle = description.nextElementSibling;

    if (!toggle?.hasAttribute('data-description-toggle')) {
        return;
    }

    if (description.scrollHeight > description.clientHeight + 1) {
        toggle.classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-description]').forEach(revealToggleIfClamped);
});

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-description-toggle]');

    if (!toggle) {
        return;
    }

    const description = toggle.previousElementSibling;

    if (!description?.hasAttribute('data-description')) {
        return;
    }

    const isNowClamped = description.classList.toggle('line-clamp-3');
    toggle.textContent = isNowClamped ? 'See more' : 'See less';
});
