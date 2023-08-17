import {
    getDashboard,
    postDashboardCommand,
    getDashboardCommandForm,
} from "../api";
import { filtersModule as filters } from '@sharp/filters';
import { commandsModule as commands } from '@sharp/commands/index';

export const UPDATE = 'UPDATE';

export default {
    namespaced: true,
    modules: {
        filters,
        commands,
    },
    state: {
        dashboardKey: null,
        data: null,
        widgets: null,
        fields: null,
        config: null,
        layout: null,
    },
    mutations: {
        [UPDATE](state, { data, layout, widgets, config, fields }) {
            state.data = data;
            state.widgets = widgets;
            state.layout = layout;
            state.config = config;
            state.fields = fields;
        },
        setDashboardKey(state, dashboardKey) {
            state.dashboardKey = dashboardKey;
        },
    },
    actions: {
        update({ commit, dispatch }, { data, widgets, layout, config, fields, filtersValues }) {
            commit(UPDATE, {
                data,
                widgets,
                layout,
                config,
                fields,
            });
            return Promise.all([
                dispatch('filters/update', {
                    filters: config.filters,
                    values: filtersValues
                }),
                dispatch('commands/update', {
                    commands: config.commands
                })
            ]);
        },
        async get({ state, dispatch, getters }, { filtersValues }) {
            const data = await getDashboard({
                dashboardKey: state.dashboardKey,
                filters: getters['filters/getQueryParams'](filtersValues)
            });
            await dispatch('update', {
                ...data,
                filtersValues,
            });
        },
        postCommand({ state }, { command, query, data }) {
            return postDashboardCommand({
                dashboardKey: state.dashboardKey,
                commandKey: command.key,
                query,
                data,
            });
        },
        getCommandForm({ state }, { command, query }) {
            return getDashboardCommandForm({
                dashboardKey: state.dashboardKey,
                commandKey: command.key,
                query,
            });
        },
    }
}
