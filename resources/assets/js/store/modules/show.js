import filters from './filters';
import {getShowView} from "../../api";

const SET_ENTITY_KEY = 'SET_ENTITY_KEY';
const SET_INSTANCE_ID = 'SET_INSTANCE_ID';
const SET_SHOW_VIEW = 'SET_SHOW_VIEW';


export default {
    namespaced: true,
    modules: {
        filters,
    },

    state: {
        entityKey: null,
        instanceId: null,
        showView: null,
    },

    mutations: {
        [SET_SHOW_VIEW](state, showView) {
            state.showView = showView;
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
            return state.showView.config;
        },
        fields(state) {
            return state.showView.fields;
        },
        layout(state) {
            return state.showView.layout;
        },
        data(state) {
            return state.showView.data;
        },
        authorizations(state) {
            return state.showView.authorizations;
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
            commit(SET_SHOW_VIEW, data);
        },
        setEntityKey({ commit }, entityKey) {
            commit(SET_ENTITY_KEY, entityKey);
        },
        setInstanceId({ commit }, instanceId) {
            commit(SET_INSTANCE_ID, instanceId);
        },
    },
}