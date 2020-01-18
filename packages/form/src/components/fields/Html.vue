<template>
    <TemplateRenderer 
        name="Html" 
        :template="template" 
        :template-data="value"
    />
</template>

<script>
    import { TemplateRenderer } from 'sharp/components';

    export default {
        name: 'SharpHtml',

        components: {
            TemplateRenderer,
        },

        props: {
            value: Object,
            template: String
        }
    }
</script>