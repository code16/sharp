import {
    getDashboard,
    postDashboardCommand,
    getDashboardCommandFormData,
} from "../../api";
import filters from './filters';
import commands from './commands';

export const UPDATE = 'UPDATE';
export const SET_DASHBOARD_KEY = 'SET_DASHBOARD_KEY';

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
        config: null,
        layout: null,
    },
    mutations: {
        [UPDATE](state, { data, layout, widgets, config }) {
            state.data = data;
            state.widgets = widgets;
            state.layout = layout;
            state.config = config;
        },
        [SET_DASHBOARD_KEY](state, dashboardKey) {
            state.dashboardKey = dashboardKey;
        }
    },
    actions: {
        update({ commit, dispatch }, { data, widgets, layout, config, filtersValues }) {
            commit(UPDATE, {
                data,
                widgets,
                layout,
                config,
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
                data: data.data,
                widgets: data.widgets,
                layout: data.layout,
                config: data.config,
                filtersValues,
            });
        },
        postCommand({ state }, { command, data, query }) {
            return postDashboardCommand({
                dashboardKey: state.dashboardKey,
                commandKey: command.key,
                data,
                query,
            });
        },
        getCommandFormData({ state }, { command, query }) {
            return getDashboardCommandFormData({
                dashboardKey: state.dashboardKey,
                commandKey: command.key,
                query,
            });
        },
        setDashboardKey({ commit }, dashboardKey) {
            commit(SET_DASHBOARD_KEY, dashboardKey);
        }
    }
}