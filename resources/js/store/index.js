
export default {
    state: {
        loading: false,
        dialogs: [],
        currentEntity: null,
    },
    mutations: {
        setLoading(state, loading) {
            state.loading = !!loading;
        },
        setDialogs(state, dialogs) {
            state.dialogs = dialogs;
        },
        setCurrentEntity(state, entity) {
            state.currentEntity = entity;
        }
    },
    getters: {
        isLoading(state) {
            return !!state.loading;
        },
    },
    actions: {
        setLoading({ commit }, loading) {
            commit('setLoading', loading);
        },
        setDialogs({ commit }, dialogs) {
            commit('setDialogs', dialogs);
        },
        setCurrentEntity({ commit }, entity) {
            commit('setCurrentEntity', entity);
        }
    },
}
