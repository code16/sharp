<template>
    <GlobalMessageLayout class="SharpGlobalMessage" :options="options" v-show="visible">
        <template v-if="fieldOptions">
            <TemplateRenderer
                name="GlobalMessage"
                :template-data="value"
                :template="fieldOptions.template"
                @content-change="handleTemplateContentChanged"
            ></TemplateRenderer>
        </template>
    </GlobalMessageLayout>
</template>

<script>
    import { TemplateRenderer } from "sharp/components";
    import GlobalMessageLayout from "./GlobalMessageLayout.vue";

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
        data() {
            return {
                visible: true,
            }
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
        methods: {
            handleTemplateContentChanged({ isEmpty }) {
                this.visible = !isEmpty;
            },
        }
    }
</script>
