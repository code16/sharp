import * as testForm from '../_test-form';
import { ignoreWarns } from '../util';

export default {
    props: {
        test: Boolean
    },
    methods: {
        getTestForm() {
            console.log('testable form patched');
            this.mount(testForm);

            this.ready = true;
            this.$nextTick(_=> {
                this.errors = testForm.errors;
            });
            this.glasspane.$emit('hide');
            return Promise.resolve(testForm)
        }
    },
    created() {
        if(!this.test) return;
        this.get = this.getTestForm;
    }
}