import { Document, DocumentData, FieldName, Charset } from "flexsearch";
import { MaybeRefOrGetter, toValue, watch } from "vue";

export function useFullTextSearch<T extends DocumentData>(
    list: MaybeRefOrGetter<T[] | null>,
    { id, searchKeys }: { id: string, searchKeys: FieldName<T>[] }
) {
    let index: Document<T>;

    function fullTextSearch(query: string) {
        return index.search(query, { enrich: true, merge: true })
            .map(result => result.doc)
    }

    watch(() => toValue(list), (list) => {
        if(list) {
            index = new Document<T>({
                document: {
                    id,
                    index: searchKeys,
                    store: true,
                },
                tokenize: 'forward',
                encoder: Charset.Normalize,
            });
            list.forEach(item => index.add(item));
        }
    }, { immediate: true })

    return {
         fullTextSearch: fullTextSearch,
    };
}
