import filters from './filters';
import {getShowView} from "../../api";

const SET_ENTITY_KEY = 'SET_ENTITY_KEY';
const SET_INSTANCE_ID = 'SET_INSTANCE_ID';
const SET_SHOW = 'SET_SHOW_VIEW';


export default {
    namespaced: true,
    modules: {
        filters,
        'entity-lists': {
            namespaced: true,
        }
    },

    state: {
        entityKey: null,
        instanceId: null,
        show: null,
    },

    mutations: {
        [SET_SHOW](state, show) {
            state.show = show;
        },
        [SET_ENTITY_KEY](state, entityKey) {
            state.entityKey = entityKey;
        },
        [SET_INSTANCE_ID](state, instanceId) {
            state.instanceId = instanceId;
        },
    },

    getters: {
        config(state) {
            return state.show.config;
        },
        fields(state) {
            return state.show.fields;
        },
        layout(state) {
            return state.show.layout;
        },
        data(state) {
            return state.show.data;
        },
        authorizations(state) {
            return state.show.authorizations;
        },
        canEdit(state, getters) {
            return getters.authorizations.update;
        },
    },

    actions: {
        async get({ state, commit }) {
            const data = await getShowView({
                entityKey: state.entityKey,
                instanceId: state.instanceId,
            });
            commit(SET_SHOW, data);
        },
        setEntityKey({ commit }, entityKey) {
            commit(SET_ENTITY_KEY, entityKey);
        },
        setInstanceId({ commit }, instanceId) {
            commit(SET_INSTANCE_ID, instanceId);
        },
    },
}