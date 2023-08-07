

export function slugify(text) {
    return text
        .toLowerCase()
        .normalize("NFD").replace(/\p{Diacritic}/gu, "")
        .replace(/[^\w ]+/g, '')
        .replace(/ +/g, '-');
}
