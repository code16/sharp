

export function parseAttributeValue(value) {
    try {
        return JSON.parse(value);
    } catch {
        return value;
    }
}

export function serializeAttributeValue(value) {
    if(value && typeof value === 'object') {
        return JSON.stringify(value);
    }

    return value;
}
