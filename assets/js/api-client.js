const Api = (function () {
    const BASE_URL = '../assets/api/';

    function endpoint(recurso) {
        return `${BASE_URL}api-${recurso}.php`;
    }

    async function parseResponse(response) {
        let body;
        try {
            body = await response.json();
        } catch (e) {
            body = { success: false, message: 'Respuesta inválida del servidor.' };
        }
        return {
            ok: response.ok && body.success !== false,
            status: response.status,
            data: body.data ?? null,
            meta: body.meta ?? null,
            message: body.message ?? '',
            error: body.error ?? null,
        };
    }

    async function request(method, recurso, { id = null, query = null, body = null } = {}) {
        let url = endpoint(recurso);

        const params = new URLSearchParams(query || {});
        if (id !== null && id !== undefined) {
            params.set('id', id);
        }
        const qs = params.toString();
        if (qs) {
            url += '?' + qs;
        }

        const options = {
            method,
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin', 
        };

        if (body !== null) {
            options.body = JSON.stringify(body);
        }

        try {
            const response = await fetch(url, options);
            return await parseResponse(response);
        } catch (err) {
            return { ok: false, status: 0, data: null, meta: null, message: 'No se pudo conectar con el servidor.', error: String(err) };
        }
    }

    return {
        get(recurso, query = null) {
            return request('GET', recurso, { query });
        },
        getOne(recurso, id) {
            return request('GET', recurso, { id });
        },
        post(recurso, body) {
            return request('POST', recurso, { body });
        },
        put(recurso, id, body) {
            return request('PUT', recurso, { id, body });
        },
        patch(recurso, id, body) {
            return request('PATCH', recurso, { id, body });
        },
        del(recurso, id) {
            return request('DELETE', recurso, { id });
        },
    };
})();
