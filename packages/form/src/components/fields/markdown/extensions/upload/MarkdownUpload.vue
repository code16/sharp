<template>
    <VueClip
        class="SharpMarkdownUpload"
        v-bind="fieldProps"
    />
</template>

<script>
    import VueClip from "../../../upload/VueClip";

    export default {
        components: {
            VueClip,
        },
        props: {
            editor: Object,
            node: Object,
            selected: Object,
            extension: Object,
            getPos: Function,
            updateAttributes: Function,
            deleteNode: Function,
        },
        computed: {
            fieldProps() {
                return this.extension.options.fieldProps;
            },
        },
    }
</script>
