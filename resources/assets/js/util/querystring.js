
export function parseRange(rangeStr) {
    const [start, end] = rangeStr.split('..');
    return {
        start: start || null,
        end: end || null,
    }
}

export function serializeRange(rangeObj) {
    const range = rangeObj || {};
    const start = range.start instanceof Date ? range.start.toISOString() : range.start;
    const end = range.end instanceof Date ? range.end.toISOString() : range.end;
    return `${start || ''}..${end || ''}`;
}