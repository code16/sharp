<template>
    <GlobalMessageLayout class="SharpGlobalMessage" :options="options">
        <template v-if="fieldOptions">
            <TemplateRenderer
                name="GlobalMessage"
                :template-data="value"
                :template="fieldOptions.template"
            ></TemplateRenderer>
        </template>
    </GlobalMessageLayout>
</template>

<script>
    import { TemplateRenderer } from "sharp/components";
    import GlobalMessageLayout from "./GlobalMessageLayout";

    export default {
        components: {
            GlobalMessageLayout,
            TemplateRenderer,
        },
        props: {
            options: Object,
            fields: Object,
            data: Object,
        },
        computed: {
            fieldKey() {
                return this.options.fieldKey;
            },
            fieldOptions() {
                return this.fields?.[this.fieldKey];
            },
            value() {
                return this.data?.[this.fieldKey];
            },
        },
    }
</script>
