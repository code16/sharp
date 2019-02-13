
export default {
    namespaced: true,

    getters: {
        filters() {},
        values() {},
        getValuesFromQuery() {
            return jest.fn();
        },
        nextQuery() {
            return jest.fn();
        },
    }
}