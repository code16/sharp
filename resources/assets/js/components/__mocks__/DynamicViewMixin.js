export default {
    data() {
        return {
            ready: false,
        }
    },
    methods: {
        get: jest.fn(() => Promise.resolve()),
        post: jest.fn(() => Promise.resolve()),
    }
}