import { parseRange, serializeRange } from "sharp";
import { getFiltersQueryParams, getFiltersValuesFromQuery, filterQueryKey } from '../util/query';

export const SET_FILTERS = 'SET_FILTERS';
export const SET_VALUES = 'SET_VALUES';

export default {
    namespaced: true,

    state: ()=>({
        filters: null,
        values: {},
    }),

    mutations: {
        [SET_FILTERS](state, filters) {
            state.filters = filters;
        },
        [SET_VALUES](state, values) {
            state.values = values;
        },
    },

    getters: {
        value(state) {
            return key => state.values[key];
        },
        filters(state) {
            return state.filters || [];
        },
        pageFilters(state) {
            return state.filters._page || [];
        },
        values(state) {
            return state.values;
        },
        filterQueryKey() {
            return key => filterQueryKey(key);
        },
        getQueryParams(state, getters) {
            return values => {
                const allFilters = Object.values(state.filters ?? {}).flat();
                return getFiltersQueryParams(values, (value, key) =>
                    getters.serializeValue({
                        filter: allFilters.find(filter => filter.key === key),
                        value,
                    })
                );
            }
        },
        getValuesFromQuery() {
            return query => getFiltersValuesFromQuery(query);
        },
        resolveFilterValue() {
            return ({ filter, value }) => {
                if(value == null) {
                    return filter?.default;
                }
                if(filter.multiple && !Array.isArray(value)) {
                    return [value];
                }
                if(filter.type === 'daterange') {
                    return parseRange(value);
                }
                if(filter.type === 'check') {
                    return value === '1';
                }
                return value;
            }
        },
        serializeValue() {
            return ({ filter, value }) => {
                if(!filter) {
                    return value;
                }
                if(filter.type === 'daterange') {
                    return serializeRange(value);
                }
                if(filter.type === 'check') {
                    return value ? '1' : null;
                }
                return value;
            };
        },
        nextValues(state) {
            return ({ filter, value }) => {
                if(filter.master) {
                    return {
                        ...Object.fromEntries(Object.entries(state.values).map(([key, value]) => [key, null])),
                        [filter.key]: value,
                    };
                }
                return { ...state.values, [filter.key]: value };
            };
        },
        nextQuery(state, getters) {
            return ({ filter, value }) => {
                return getters.getQueryParams(getters.nextValues({ filter, value }));
            }
        },
    },

    actions: {
        update({ state, commit, dispatch, getters }, { filters, values }) {
            commit(SET_FILTERS, filters);
            commit(SET_VALUES, {
                ...Object.fromEntries(
                    Object.values(filters ?? {}).flat().map(filter =>
                        [
                            filter.key,
                            getters.resolveFilterValue({
                                filter,
                                value: values?.[filter.key],
                            }),
                        ]
                    )
                ),
            });
        },
    }
}
