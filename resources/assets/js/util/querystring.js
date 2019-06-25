
export function parseRange(rangeStr) {
    const [start, end] = (rangeStr || '').split('..');
    return {
        start: start || null,
        end: end || null,
    }
}

function serializeRangeValue(value) {
    if(value instanceof Date) {
        return value.toISOString();
    }
    if(typeof value === 'number') {
        return value.toString();
    }
    return value || '';
}

export function serializeRange(range) {
    const start = serializeRangeValue((range || {}).start);
    const end = serializeRangeValue((range || {}).end);
    return start || end
        ? `${start}..${end}`
        : null;
}