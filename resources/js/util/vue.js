

export const ignoredElements = [];

export function ignoreVueElement(tag) {
    if(tag && !ignoredElements.includes(tag)) {
        ignoredElements.push(tag);
    }
}
