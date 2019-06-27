import moment from 'moment';

const RANGE_DATE_FORMAT = 'YYYYMMDD';

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