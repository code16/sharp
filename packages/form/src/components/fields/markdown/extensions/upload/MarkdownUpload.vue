<template>
    <NodeViewWrapper>
        <VueClip
            class="SharpMarkdownUpload"
            :value="value"
            :root="false"
            :options="options"
            :focused="selected"
            v-bind="fieldProps"
            @updated="handleUpdate"
            @removed="handleRemoveClicked"
            @success="handleSuccess"
            @error="handleError"
        />
    </NodeViewWrapper>
</template>

<script>
    import VueClip from "../../../upload/VueClip";
    import { NodeViewWrapper } from '@tiptap/vue-2';
    import { lang, showAlert, getUniqueId } from "sharp";
    import { getUploadOptions } from "../../../../../util/upload";

    export default {
        components: {
            NodeViewWrapper,
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
            value() {
                return this.node.attrs.value;
            },
            fieldProps() {
                const props = this.extension.options.fieldProps;
                return {
                    ...props,
                    uniqueIdentifier: `${props.uniqueIdentifier}.upload.${getUniqueId(this)}`,
                }
            },
            options() {
                return getUploadOptions({
                    fileFilter: this.fieldProps.fileFilter,
                    maxFileSize: this.fieldProps.maxFileSize,
                });
            },
        },
        methods: {
            handleRemoveClicked() {
                this.deleteNode();
                setTimeout(() => {
                    this.editor.commands.focus();
                }, 0);
            },
            handleError(message) {
                showAlert(message, {
                    isError: true,
                    title: lang(`modals.error.title`),
                });
            },
            handleUpdate(value) {
                this.updateAttributes({
                    value,
                });
                this.extension.options.onUpdate(value);
            },
            handleSuccess(value) {
                this.updateAttributes({
                    value,
                });
                this.extension.options.onSuccess(value);
            },
        },
        beforeDestroy() {
            this.extension.options.onRemove(this.value);
        },
    }
</script>
