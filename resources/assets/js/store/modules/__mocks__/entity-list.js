import filters from './filters';

export default {
    namespaced: true,
    modules: {
        filters,
    },

    state: {
        entityKey: null,
    },

    actions: {
        reorder: jest.fn(),
        setEntityKey: jest.fn(),
    }
}