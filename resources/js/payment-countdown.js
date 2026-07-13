const CRITICAL_THRESHOLD_MS = 2 * 60 * 1000;

function formatRemaining(ms) {
    const totalSeconds = Math.max(0, Math.floor(ms / 1000));
    const minutes = Math.floor(totalSeconds / 60);
    const seconds = totalSeconds % 60;

    return `${minutes}:${String(seconds).padStart(2, '0')}`;
}

function startCountdown(el) {
    const deadline = new Date(el.dataset.deadline).getTime();
    const valueEl = el.querySelector('[data-payment-countdown-value]');

    if (!valueEl || Number.isNaN(deadline)) {
        return;
    }

    let interval;

    const tick = () => {
        const remaining = deadline - Date.now();

        if (remaining <= 0) {
            valueEl.textContent = '0:00';
            clearInterval(interval);
            window.location.reload();
            return;
        }

        valueEl.textContent = formatRemaining(remaining);
        el.dataset.state = remaining <= CRITICAL_THRESHOLD_MS ? 'critical' : 'normal';
    };

    tick();
    interval = setInterval(tick, 1000);
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-payment-countdown]').forEach(startCountdown);
});
