<template>
    <component v-if="exists" :is="template.compName" :props="templateProps">
    </component>
</template>

<script>
    import Template from '../app/models/Template';

    export default {
        name: 'SharpTemplate',
        props: {
            fieldKey: String,
            templateProps: {
                type:Object,
                default() { return {} }
            },
            name: String
        },
        data() {
            return {
                template: null,
                exists: false
            }
        },
        watch: {
            'template.exists'(val) {
                this.exists = val;
            }
        },
        created() {
            this.template = new Template(this.fieldKey, this.name)
        }
    }
</script>