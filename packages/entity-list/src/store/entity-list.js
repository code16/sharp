import { postEntityListReorder } from "../api";
import { filters } from 'sharp/store';

export const SET_ENTITY_KEY = 'SET_ENTITY_KEY';
export const SET_QUERY = 'SET_QUERY';

export default {
    namespaced: true,
    modules: {
        filters,
    },

    state() {
        return {
            entityKey: null,
            query: {},
        }
    },

    mutations: {
        [SET_ENTITY_KEY](state, entityKey) {
            state.entityKey = entityKey;
        },
        [SET_QUERY](state, query) {
            state.query = query;
        }
    },

    getters: {
        query(state) {
            return state.query;
        },
    },

    actions: {
        update({ dispatch }, { data, layout, config, filtersValues }) {
            return Promise.all([
                dispatch('filters/update', {
                    filters: config.filters,
                    values: filtersValues
                }),
            ]);
        },
        reorder({ state }, { instances }) {
            return postEntityListReorder({
                entityKey: state.entityKey,
                instances,
            });
        },
        setEntityKey({ commit }, entityKey) {
            commit(SET_ENTITY_KEY, entityKey);
        },
        setQuery({ commit }, query) {
            commit(SET_QUERY, query);
        }
    }
}