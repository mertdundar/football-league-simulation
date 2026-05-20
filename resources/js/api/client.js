const BASE = '/api';

export class ApiError extends Error {
    constructor(code, message, status = 0, details = {}) {
        super(message);
        this.name = 'ApiError';
        this.code = code;
        this.status = status;
        this.details = details;
    }
}

async function request(method, path, body = null) {
    const opts = {
        method,
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    };
    if (body !== null) {
        opts.headers['Content-Type'] = 'application/json';
        opts.body = JSON.stringify(body);
    }

    let response;
    try {
        response = await fetch(BASE + path, opts);
    } catch (e) {
        throw new ApiError('NETWORK_ERROR', e.message || 'Network error', 0);
    }

    const text = await response.text();
    let json = null;
    if (text) {
        try {
            json = JSON.parse(text);
        } catch {
            // Non-JSON body; leave json as null.
        }
    }

    if (!response.ok) {
        const err = (json && json.error) || {};
        throw new ApiError(
            err.code || 'UNKNOWN',
            err.message || response.statusText || 'Request failed',
            response.status,
            err.details || {},
        );
    }
    return json;
}

export const api = {
    state: () => request('GET', '/state'),
    generate: () => request('POST', '/fixtures/generate'),
    playNext: () => request('POST', '/play-next'),
    playAll: () => request('POST', '/play-all'),
    editFixture: (id, scores) => request('PUT', `/fixtures/${id}`, scores),
    reset: () => request('POST', '/reset'),
};
