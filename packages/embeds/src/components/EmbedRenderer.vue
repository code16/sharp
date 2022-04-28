<template>
    <TemplateRenderer
        name="Embed"
        :template-data="templateData"
        :template-props="templateProps"
        :template="options.template"
    >
        <slot>
            <template v-if="data.slot">
                <div v-html="data.slot"></div>
            </template>
        </slot>
    </TemplateRenderer>
</template>

<script>
    import { TemplateRenderer } from "sharp/components";

    export default {
        components: {
            TemplateRenderer,
        },
        props: {
            data: Object,
            options: Object,
        },
        computed: {
            templateData() {
                const { slot, ...data } = this.data;
                return data;
            },
            templateProps() {
                return this.options.attributes
                    .filter(attr => attr !== 'slot');
            },
        },
    }
</script>
