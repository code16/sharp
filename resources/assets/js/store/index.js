


export default {
    state: {
        loading: false,
    },
    mutations: {
        setLoading(state, loading) {
            state.loading = !!loading;
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
    },
}