
export default {
    namespaced: true,

    state: {
        entityKey: null,
    },

    actions: {
        reorder: jest.fn(),
        setEntityKey: jest.fn(),
    }
}