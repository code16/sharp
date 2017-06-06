export default {
    inject:['actionsBus'],

    methods: {
        emitAction() {
            this.actionsBus.$emit(...arguments);
        }
    }
}