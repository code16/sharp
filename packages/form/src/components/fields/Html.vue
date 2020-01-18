<template>
    <sharp-template name="Html" :template="template" :template-data="value"></sharp-template>
</template>

<script>
    import SharpTemplate from '../../Template';

    export default {
        name: 'SharpHtml',

        components: {
            SharpTemplate
        },

        props: {
            value: Object,
            template: String
        }
    }
</script>