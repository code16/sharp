import qs from 'qs';
import { transformParams } from "@/api/paramsSerializer";


export function stringifyQuery(query) {
    // take fro mhttps://github.com/tighten/ziggy/blob/2.x/src/js/Router.js#L48
    return qs.stringify(transformParams(query), {
        addQueryPrefix: true,
        arrayFormat: 'indices',
        encodeValuesOnly: true,
        skipNulls: true,
        encoder: (value, encoder) =>
            typeof value === 'boolean' ? String(Number(value)) : encoder(value),
    });
}

export function parseQuery(query) {
    return qs.parse(query, { ignoreQueryPrefix: true, strictNullHandling: true });
}

