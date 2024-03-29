import { filtersModule as filters } from 'sharp-filters';
import {getShowView, postShowCommand, getShowCommandForm, postShowState, deleteShow} from "../api";

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
        entityKey(state) {
            return state.entityKey;
        },
        instanceId(state) {
            return state.instanceId;
        },
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
        locales(state) {
            return state.show?.locales;
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
            return !!getters.config.state && getters.config.state.authorization;
        },
        authorizedCommands(state, getters) {
            const commands = getters.config.commands || {};
            return (commands.instance || [])
                .map(group => group.filter(command => command.authorization));
        },
        instanceState(state, getters) {
            if(getters.config.state) {
                return getters.data[getters.config.state.attribute];
            }
            return null;
        },
        instanceStateOptions(state, getters) {
            return getters.config.state?.values.find(item => item.value === getters.instanceState);
        },
        stateValues(state, getters) {
            return getters.config.state?.values;
        },
    },

    actions: {
        async get({ state, commit }) {
            const data = await getShowView({
                entityKey: state.entityKey,
                instanceId: state.instanceId,
            });
            commit(SET_SHOW, data);
            return data;
        },
        delete({ state }) {
            return deleteShow({
                entityKey: state.entityKey,
                instanceId: state.instanceId,
            });
        },
        postCommand({ state }, { command, data }) {
            return postShowCommand({
                entityKey: state.entityKey,
                instanceId: state.instanceId,
                commandKey: command.key,
                data,
            });
        },
        getCommandForm({ state }, { command, query }) {
            return getShowCommandForm({
                entityKey: state.entityKey,
                instanceId: state.instanceId,
                commandKey: command.key,
                query,
            });
        },
        postState({ state, getters }, value) {
            return postShowState({
                entityKey: state.entityKey,
                instanceId: state.instanceId,
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
