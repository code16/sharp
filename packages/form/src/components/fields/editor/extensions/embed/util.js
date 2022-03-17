
export const additionalDataAttributeName = '_additional-data';

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

export function kebabCase(attributeName) {
    return attributeName
        .replace(/[A-Z]+(?![a-z])|[A-Z]/g, (char, ofs) => (ofs ? '-' : '') + char.toLowerCase())
}

export function serializeEmbedElement(element) {
    element.removeAttribute(additionalDataAttributeName);
}
