import * as testForm from '../_test-form';

export default {
    props: {
        test: Boolean
    },
    methods: {
        get() {
            console.log('testable form patched');
            this.mount(testForm);

            this.ready = true;
            this.$nextTick(_=> {
                this.errors = testForm.errors;
            });
            this.glasspane.$emit('hide');
            return Promise.resolve(testForm);
        }
    },
    created() {
        if(this.test)
            this.entityKey = 1;
    }
}