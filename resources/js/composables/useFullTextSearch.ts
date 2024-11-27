import Flexsearch from "flexsearch";
import { MaybeRefOrGetter, toValue, watch } from "vue";

export function useFullTextSearch<T>(list: MaybeRefOrGetter<T[] | null>, { id, searchKeys }: { id: string, searchKeys: string[] }) {
    let index: Flexsearch.Document<T, true>;

    function fullTextSearch(query: string) {
        return index.search(query, undefined, { enrich: true })
            .map(result => result.result.map(r => r.doc))
            .flat()
    }

    watch(() => toValue(list), (list) => {
        if(list) {
            index = new Flexsearch.Document<T, true>({
                document: {
                    id,
                    index: searchKeys,
                    store: true,
                },
                tokenize: 'forward',
                charset: 'latin:simple',
            });
            list.forEach(item => index.add(item));
        }
    }, { immediate: true })

    return {
         fullTextSearch: fullTextSearch,
    };
}
