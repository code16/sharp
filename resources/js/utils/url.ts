import { ZiggyRouter } from '@/types/globals';
import { Config, RouteParam, RouteParamsWithQueryOverload } from 'ziggy-js';

export function getAppendableUri() {
    return location.pathname.replace(/^.+?s-list\//, '');
}

export function route(
    name?: undefined,
    params?: RouteParamsWithQueryOverload | RouteParam,
    absolute?: boolean,
    config?: Config,
): ZiggyRouter;

export function route(
    name: string,
    params?: RouteParamsWithQueryOverload | RouteParam,
    absolute?: boolean,
    config?: Config,
): string;

export function route(name?: string, params?, absolute?, config?) {
    if(name && params.uri) {
        return (globalThis.route(name, { ...params, uri: '(uri)' }, absolute, config) as string)
            .replace('(uri)', params.uri);
    }
    return globalThis.route();
}

// export route;

