function paintStars(container, value) {
    container.querySelectorAll('[data-star]').forEach((btn) => {
        const filled = Number(btn.dataset.star) <= value;

        btn.classList.toggle('text-amber-400', filled);
        btn.classList.toggle('text-neutral-300', !filled);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-star-rating]').forEach((container) => {
        const input = container.querySelector('[data-star-rating-input]');
        paintStars(container, Number(input?.value) || 0);
    });
});

document.addEventListener('click', (event) => {
    const star = event.target.closest('[data-star]');

    if (!star) {
        return;
    }

    const container = star.closest('[data-star-rating]');
    const input = container?.querySelector('[data-star-rating-input]');

    if (!input) {
        return;
    }

    input.value = star.dataset.star;
    paintStars(container, Number(input.value));
});

document.addEventListener('mouseover', (event) => {
    const star = event.target.closest('[data-star]');

    if (!star) {
        return;
    }

    const container = star.closest('[data-star-rating]');

    if (container) {
        paintStars(container, Number(star.dataset.star));
    }
});

document.addEventListener('mouseout', (event) => {
    const star = event.target.closest('[data-star]');

    if (!star) {
        return;
    }

    const container = star.closest('[data-star-rating]');
    const input = container?.querySelector('[data-star-rating-input]');

    if (container) {
        paintStars(container, Number(input?.value) || 0);
    }
});
