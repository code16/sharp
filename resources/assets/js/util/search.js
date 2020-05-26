import Fuse from 'fuse.js';

const defaultOptions = {
    caseSensitive: false,
    include: [],
    minMatchCharLength: 1,
    shouldSort: true,
    tokenize: true,
    matchAllTokens: false,
    findAllMatches: false,
    id: null,
    keys: ['value'],
    location: 0,
    threshold: 0.0,
    distance: 0,
    maxPatternLength: 64,
}

export function search(list, query, { searchKeys } = {}) {
    const fuse = new Fuse(list, {
        ...defaultOptions,
        keys: searchKeys,
    });
    return fuse.search(query);
}