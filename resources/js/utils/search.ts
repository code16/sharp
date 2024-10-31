import Fuse, { type FuseOptions } from 'fuse.js';

const defaultOptions: FuseOptions = {
    caseSensitive: false,
    distance: 0,
    findAllMatches: false,
    id: null,
    include: [],
    keys: ['value'],
    location: 0,
    matchAllTokens: false,
    maxPatternLength: 64,
    minMatchCharLength: 1,
    shouldSort: true,
    threshold: 0.0,
    tokenize: true,
}

export function fuzzySearch<T>(list: T[], query: string, { searchKeys }: { searchKeys: string[] }) {
    const fuse = new Fuse(list, {
        ...defaultOptions,
        keys: searchKeys,
    });
    return fuse.search<T>(query);
}
