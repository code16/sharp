import Vue from 'vue';

export const SET_FILTERS = 'SET_FILTERS';
export const SET_FILTER_VALUE = 'SET_FILTER_VALUE';

export default {
    namespaced: true,

    state: {
        filters: null,
        value: {}
    },

    mutations: {
        [SET_FILTERS](state, filters) {
            state.filters = filters;
        },
        [SET_FILTER_VALUE](state, { key, value }) {
            Vue.set(state.value, key, value);
        }
    },

    getters: {
        value(state) {
            return key => state.value[key];
        },
        findByKey(state) {
            return key => state.filters.find(filter => filter.key === key);
        },

        defaultValue() {
            return filter => filter.default;
        },
    },

    actions: {
        setFilters({ commit, dispatch }, filters) {
            commit(SET_FILTERS, filters);

            return Promise.all(
                filters.map(filter =>
                    dispatch('setFilterValueOrDefault', { filter })
                )
            );
        },
        setFilterValue({ dispatch, getters }, { key, value }) {
            return dispatch('setFilterValueOrDefault', {
                filter: getters.findByKey(key),
                value
            });
        },
        setFilterValueOrDefault({ commit, getters }, { filter, value }) {
            commit(SET_FILTER_VALUE, {
                key: filter.key,
                value: value == null
                    ? getters.defaultValue(filter)
                    : value
            });
        }
    }

}