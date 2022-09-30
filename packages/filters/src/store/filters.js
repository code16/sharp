import Vue from 'vue';
import { parseRange, serializeRange } from "sharp";
import { getFiltersQueryParams, getFiltersValuesFromQuery, filterQueryKey } from '../util/query';

export const SET_FILTERS = 'SET_FILTERS';
export const SET_FILTER_VALUE = 'SET_FILTER_VALUE';

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
        [SET_FILTER_VALUE](state, { key, value }) {
            Vue.set(state.values, key, value);
        },
    },

    getters: {
        value(state) {
            return key => state.values[key];
        },
        filters(state) {
            return state.filters || []
        },
        values(state) {
            return state.values;
        },
        filter(state) {
            return key => (state.filters || []).find(filter => filter.key === key);
        },

        defaultValue() {
            return filter => (filter||{}).default;
        },

        filterQueryKey() {
            return key => filterQueryKey(key);
        },
        getQueryParams(state, getters) {
            return values => {
                return getFiltersQueryParams(values, (value, key) =>
                    getters.serializeValue({ filter:getters.filter(key), value })
                );
            }
        },
        getValuesFromQuery() {
            return query => getFiltersValuesFromQuery(query);
        },
        resolveFilterValue(state, getters) {
            return ({ filter, value }) => {
                if(value == null) {
                    return getters.defaultValue(filter);
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
        update({ commit, dispatch }, { filters, values }) {
            commit(SET_FILTERS, filters);

            return Promise.all(
                (filters || []).map(filter => {
                    dispatch('setFilterValue', {
                        filter,
                        value: (values || {})[filter.key]
                    })
                })
            );
        },
        setFilterValue({ commit, getters }, { filter, value }) {
            commit(SET_FILTER_VALUE, {
                key: filter.key,
                value: getters.resolveFilterValue({ filter, value })
            });
        },
    }
}
