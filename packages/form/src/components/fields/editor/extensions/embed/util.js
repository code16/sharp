

export function hyphenate(attributeName) {
    return attributeName
        .replace(/[A-Z]+(?![a-z])|[A-Z]/g, (char, ofs) => (ofs ? '-' : '') + char.toLowerCase())
}
