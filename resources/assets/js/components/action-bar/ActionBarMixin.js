export default {
    inject:['actionsBus'],

    data() {
        return {
            ready: false
        }
    },

    methods: {
        emitAction() {
            this.actionsBus.$emit(...arguments);
        }
    },

    mounted() {
        this.actionsBus.$on('setup', () => {
            this.ready = true;
        });
    }
}