
export default {
    state: {
        loading: false,
        dialogs: [],
    },
    mutations: {
        setLoading(state, loading) {
            state.loading = !!loading;
        },
        setDialogs(state, dialogs) {
            state.dialogs = dialogs;
        },
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
    },
}