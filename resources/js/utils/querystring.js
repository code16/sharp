import qs from 'qs';
import moment from 'moment';
import paramsSerializer, { transformParams } from "@/api/paramsSerializer";

const RANGE_DATE_FORMAT = 'YYYYMMDD';

export function stringifyQuery(query) {
    // return qs.stringify(query, { addQueryPrefix: true, skipNulls: true });
    return qs.stringify(transformParams(query), { addQueryPrefix: true, strictNullHandling:true });
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
