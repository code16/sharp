import { getGlobalFilters, postGlobalFilters } from "../../api";
import filters from './filters';

export default {
    namespaced: true,
    modules: {
        filters,
    },
    actions: {
        async get({ dispatch }) {
            const data = await getGlobalFilters();
            dispatch('filters/update', {
                filters: data.filters
            });
        },
        post({}, { filter, value }) {
            return postGlobalFilters({
                filterKey: filter.key,
                value,
            });
        }
    }
}