

export function stripTags(html) {
    const el = document.createElement('div');
    el.innerHTML = html;
    return el.textContent;
}

export function truncateToWords(text, count) {
    const matches = [...text.matchAll(/\S+\s*/g)];
    return matches.length > count
        ? matches.slice(0, count).map(match => match[0]).join('')
        : text;
}