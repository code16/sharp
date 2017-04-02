import Template from '../app/models/Template';

export default {
    computed: {

    },
    methods: {
        template(fieldKey, templateName) {
            let tpl=new Template(fieldKey, templateName);
            return tpl;
        },
    }
};