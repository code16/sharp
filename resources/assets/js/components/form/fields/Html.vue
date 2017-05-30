<template>
    <sharp-template name="Html" :template="template" :template-data="value"></sharp-template>
</template>

<script>
    import Template from '../Template';

    export default {
        name: 'SharpHtml',

        components: {
            [Template.name]:Template
        },

        props: {
            value: Object,
            template: String
        }
    }
</script>