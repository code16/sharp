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
        update({ commit, dispatch }, { data, widgets, layout, config, filterValues }) {
            commit(UPDATE, {
                data,
                widgets,
                layout,
                config,
            });
            return Promise.all([
                dispatch('filters/update', {
                    filters: config.filters,
                    values: filterValues
                }),
                dispatch('commands/update', {
                    commands: config.commands
                })
            ]);
        },
        async get({ state, dispatch, getters }, { filterValues }) {
            const data = await getDashboard({
                dashboardKey: state.dashboardKey,
                filters: getters['filters/getQueryParams'](filterValues)
            });
            await dispatch('update', {
                data: data.data,
                widgets: data.widgets,
                layout: data.layout,
                config: data.config,
                filterValues,
            });
        },
        postCommand({ state }, { data, query }) {
            return postDashboardCommand({
                dashboardKey: state.dashboardKey,
                data,
                query,
            });
        },
        getCommandFormData({ state }, { query }) {
            return getDashboardCommandFormData({
                dashboardKey: state.dashboardKey,
                query,
            });
        },
        setDashboardKey({ commit }, dashboardKey) {
            commit(SET_DASHBOARD_KEY, dashboardKey);
        }
    }
}