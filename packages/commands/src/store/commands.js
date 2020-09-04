

export const SET_COMMANDS = 'SET_COMMANDS';

export default {
    namespaced: true,

    state: {
        commands: null,
    },
    mutations: {
        [SET_COMMANDS](state, commands) {
            state.commands = commands;
        }
    },
    getters: {
        forType(state) {
            return type => state.commands ? state.commands[type] : null;
        }
    },
    actions: {
        update({ commit }, { commands }) {
            commit(SET_COMMANDS, commands);
        }
    },
}