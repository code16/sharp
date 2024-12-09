import Flexsearch from 'flexsearch';


export function fuzzySearch<T>(list: T[], query: string, { id, searchKeys }: { id: string, searchKeys: string[] }) {
    const index = new Flexsearch.Document<T, true>({
        document: {
            id,
            index: searchKeys,
            store: true,
        },
        tokenize: 'forward',
        charset: 'latin:simple',
    });
    list.forEach(item => index.add(item));
    return index.search(query, undefined, { enrich: true })
        .map(result => result.result.map(r => r.doc))
        .flat();
}
