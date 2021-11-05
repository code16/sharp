
export function getPreloadConfig(api) {
    return {
        async adapter(config) {
            const url = api.getUri({
                url: `${config.baseURL}/${config.url.replace(/^\//, '')}`,
                params: config.params,
            })
            const response = await fetch(url, {
                method: config.method,
            })
            return {
                data: await response.json(),
                status: response.status,
                statusText: response.statusText,
                headers: Object.fromEntries(response.headers.entries()),
                config,
            };
        }
    }
}
