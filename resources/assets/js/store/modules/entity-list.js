import { postEntityListReorder } from "../../api";

export const SET_ENTITY_KEY = 'SET_ENTITY_KEY';

export default {
    namespaced: true,

    state: {
        entityKey: null,
    },

    mutations: {
        [SET_ENTITY_KEY](state, entityKey) {
            state.entityKey = entityKey;
        },
    },

    actions: {
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