import filters from './filters';
import { getDashboard } from "../../api";


export const UPDATE = 'UPDATE';

export default {
    namespaced: true,
    modules: {
        filters
    },
    state: {
        data: null,
        widgets: null,
        config: null,
        layout: null
    },
    mutations: {
        [UPDATE](state, { data, layout, widgets, config }) {
            state.data = data;
            state.widgets = widgets;
            state.layout = layout;
            state.config = config;
        }
    },
    actions: {
        async get({ state, commit, dispatch, getters }, { dashboardKey, filterValues }) {
            const data = await getDashboard({
                dashboardKey,
                filters: getters['filters/getQueryParams'](filterValues)
            });
            commit(UPDATE, {
                data: data.data,
                widgets: data.widgets,
                layout: data.layout,
                config: data.config
            });
            await dispatch('filters/update', {
                filters: data.config.filters,
                values: filterValues
            });
        }
    }
}