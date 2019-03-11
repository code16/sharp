import { postEntityListReorder } from "../../api";
import filters from './filters';

export const SET_ENTITY_KEY = 'SET_ENTITY_KEY';

export default {
    namespaced: true,
    modules: {
        filters,
    },

    state: {
        entityKey: null,
    },

    mutations: {
        [SET_ENTITY_KEY](state, entityKey) {
            state.entityKey = entityKey;
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
        }
    }
}