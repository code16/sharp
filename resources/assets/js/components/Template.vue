<template>
    <component v-if="template.exists"
               :is="template.compName">
    </component>
</template>

<script>
    import Template from '../app/models/Template';

    export default {
        name: 'SharpTemplate',
        props: {
            fieldKey: String,
            name: String
        },
        computed: {
            template() {
                return new Template(this.fieldKey, this.name);
            }
        }
    }
</script>