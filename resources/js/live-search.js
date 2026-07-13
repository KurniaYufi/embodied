function debounce(fn, delay) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

let activeController = null;

async function runLiveSearch(input) {
    const form = input.form;
    const results = document.getElementById('collection-results');

    if (!form || !results) {
        return;
    }

    activeController?.abort();
    activeController = new AbortController();

    const params = new URLSearchParams(new FormData(form));
    const url = `${form.action}?${params.toString()}`;

    results.classList.add('opacity-60');

    let response;
    try {
        response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal: activeController.signal,
        });
    } catch {
        return;
    }

    if (!response.ok) {
        results.classList.remove('opacity-60');
        return;
    }

    const html = await response.text();
    const freshResults = new DOMParser().parseFromString(html, 'text/html').getElementById('collection-results');

    if (freshResults) {
        results.innerHTML = freshResults.innerHTML;
    }

    results.classList.remove('opacity-60');
    window.history.replaceState(null, '', url);
}

document.querySelectorAll('[data-live-search]').forEach((input) => {
    const delay = Number(input.dataset.liveSearch) || 400;
    input.addEventListener('input', debounce(() => runLiveSearch(input), delay));
});
