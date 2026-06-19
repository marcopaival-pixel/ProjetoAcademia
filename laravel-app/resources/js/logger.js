/**
 * Client-side error reporting for NexShape.
 */
const ENDPOINT = () => {
    const base = document.querySelector('meta[name="api-base"]')?.getAttribute('content');
    return base ? `${base.replace(/\/$/, '')}/client-errors` : '/api/v1/client-errors';
};
const MAX_MESSAGE_LENGTH = 2000;

function shouldReport() {
    return typeof window !== 'undefined' && document.documentElement?.dataset?.clientErrors !== 'off';
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

export function reportClientError(payload) {
    if (!shouldReport()) {
        return;
    }

    const body = {
        type: payload.type ?? 'error',
        message: String(payload.message ?? 'Unknown error').slice(0, MAX_MESSAGE_LENGTH),
        stack: payload.stack ? String(payload.stack).slice(0, 10000) : null,
        url: payload.url ?? window.location.href,
    };

    const headers = {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    const csrf = getCsrfToken();
    if (csrf) {
        headers['X-CSRF-TOKEN'] = csrf;
    }

    fetch(ENDPOINT(), {
        method: 'POST',
        headers,
        body: JSON.stringify(body),
        credentials: 'same-origin',
        keepalive: true,
    }).catch(() => {
        // Silent — avoid recursive error loops.
    });
}

export function initClientErrorReporting() {
    if (!shouldReport() || window.__nexshapeClientErrorsInit) {
        return;
    }

    window.__nexshapeClientErrorsInit = true;

    window.addEventListener('error', (event) => {
        reportClientError({
            type: 'error',
            message: event.message,
            stack: event.error?.stack ?? null,
            url: event.filename ? `${event.filename}:${event.lineno}` : window.location.href,
        });
    });

    window.addEventListener('unhandledrejection', (event) => {
        const reason = event.reason;
        reportClientError({
            type: 'unhandledrejection',
            message: reason?.message ?? String(reason),
            stack: reason?.stack ?? null,
        });
    });
}

initClientErrorReporting();
