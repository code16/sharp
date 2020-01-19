import filters from './filters';

export default {
    namespaced: true,
    modules: {
        filters,
    },

    state() {
        return {
            entityKey: null,
            query: null,
        }
    },

    getters: {
        query: jest.fn(state => state.query),
    },

    actions: {
        reorder: jest.fn(),
        setEntityKey: jest.fn(),
        setQuery: jest.fn(),
    }
}