import * as testForm from '../_test-form';

export default {
    props: {
        test: Boolean
    },
    mounted() {
        if(this.test) {
            this.mount(testForm);

            this.ready = true;
            this.$nextTick(_=> {
                this.errors = testForm.errors;
            });
            this.glasspane.$emit('hide');
        }
    }
}