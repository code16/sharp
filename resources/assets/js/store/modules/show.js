import filters from './filters';
import { getShowView, postShowCommand, getShowCommandFormData, postShowState } from "../../api";

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
        breadcrumb(state) {
            return state.show.breadcrumb;
        },
        authorizations(state) {
            return state.show.authorizations;
        },
        canEdit(state, getters) {
            return getters.authorizations.update;
        },
        canChangeState(state, getters) {
            // TODO https://github.com/code16/sharp-dev/issues/3
            return getters.config.state && getters.config.state.authorizaton;
        },
        authorizedCommands(state, getters) {
            // TODO https://github.com/code16/sharp-dev/issues/3
            return (getters.config.commands.instance || [])
                .map(group => group.filter(command => command.authorization));
        },
        instanceState() {
            // TODO https://github.com/code16/sharp-dev/issues/5
            return null;
        },
        stateValues(state, getters) {
            return getters.config.state
                ? getters.config.state.values
                : null;
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
        postCommand({ state }, { command, data }) {
            return postShowCommand({
                entityKey: state.entityKey,
                instanceId: state.instanceId,
                commandKey: command.key,
                data,
            });
        },
        getCommandFormData({ state }, { command }) {
            return getShowCommandFormData({
                entityKey: state.entityKey,
                instanceId: state.instanceId,
                commandKey: command.key,
            });
        },
        postState({ state, getters }, value) {
            return postShowState({
                entityKey: state.entityKey,
                attribute: getters.config.state.attribute,
                value,
            });
        },
        setEntityKey({ commit }, entityKey) {
            commit(SET_ENTITY_KEY, entityKey);
        },
        setInstanceId({ commit }, instanceId) {
            commit(SET_INSTANCE_ID, instanceId);
        },
    },
}