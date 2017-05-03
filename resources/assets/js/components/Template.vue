<template>
    <rendered-template v-if="exists" :comp-name="template.compName" :template-data="templateData">
    </rendered-template>
</template>

<script>
    import Template from '../app/models/Template';

    export default {
        name: 'SharpTemplate',

        components: {
            RenderedTemplate: {
                functional: true,
                render(createElement, { props }) {
                    //console.log(arguments);
                    return createElement(props.compName, {
                        props: props.templateData
                    });
                }
            }
        },

        props: {
            fieldKey: String,
            name: String,
            templateData: Object
        },
        data() {
            return {
                template: null,
                exists: false
            }
        },
        watch: {
            'template.exists': function(val) {
                this.exists = val;
            }
        },
        created() {
            this.template = new Template(this.fieldKey, this.name);
        },
        mounted() {
            //console.log(this);
        }
    }
</script>