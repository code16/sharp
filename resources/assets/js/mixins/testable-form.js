import { fields, data, layout } from '../_test-form';

export default {
    props: {
        test: Boolean
    },
    mounted() {
        if(this.test) {
            this.fields = fields;
            this.data = data;
            this.layout = layout;

            this.parseTemplates();
            this.ready = true;
        }
    }
}