

export function slugify(text) {
    return text
        .toLowerCase()
        .normalize("NFD").replace(/\p{Diacritic}/gu, "")
        .replace(/[^\w ]+/g, '')
        .replace(/ +/g, '-');
}

export function hyphenate(attributeName) {
    return attributeName
        .replace(/[A-Z]+(?![a-z])|[A-Z]/g, (char, ofs) => (ofs ? '-' : '') + char.toLowerCase())
}
