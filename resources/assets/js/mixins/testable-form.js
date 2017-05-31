import { fields, data, layout, errors } from '../_test-form';

export default {
    props: {
        test: Boolean
    },
    mounted() {
        if(this.test) {
            this.fields = fields;
            this.data = data;
            this.layout = layout;

            this.ready = true;
            this.$nextTick(_=> {
                this.errors = errors;
            });
        }
    }
}