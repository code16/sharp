import qs from 'qs';
import moment from 'moment';
import { transformParams } from "@/api/paramsSerializer";

const RANGE_DATE_FORMAT = 'YYYYMMDD';

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

export function parseRange(rangeStr) {
    const [start, end] = (rangeStr || '').split('..');
    return {
        start: start
            ? moment(start, RANGE_DATE_FORMAT).toDate()
            : null,
        end: end
            ? moment(end, RANGE_DATE_FORMAT).toDate()
            : null,
    }
}

export function serializeRange(range) {
    if(typeof range === 'string') {
        return range;
    }

    let start = (range || {}).start;
    let end = (range || {}).end;

    if(start) {
        start = moment(start).format(RANGE_DATE_FORMAT);
    }
    if(end) {
        end = moment(end).format(RANGE_DATE_FORMAT);
    }

    return start || end
        ? `${start || ''}..${end || ''}`
        : null;
}
