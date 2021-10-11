<template>
    <NodeRenderer :node="node">
        <VueClip
            class="editor__node"
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
    </NodeRenderer>
</template>

<script>
    import VueClip from "../../../upload/VueClip";
    import NodeRenderer from "../../NodeRenderer";
    import { lang, showAlert, getUniqueId } from "sharp";
    import { getUploadOptions } from "../../../../../util/upload";

    export default {
        components: {
            NodeRenderer,
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
                const attrs = this.node.attrs;
                if(attrs.file) {
                    return {
                        file: attrs.file,
                    }
                }
                return this.extension.options.getFile(attrs);
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
                    'filter-crop': value.filters.crop,
                    'filter-rotate': value.filters.rotate,
                });
                this.extension.options.onUpdate(value);
            },
            handleSuccess(value) {
                this.updateAttributes({
                    'name': value.name,
                    'disk': value.disk,
                    'media': value.media,
                    'file': null,
                });
                this.extension.options.onSuccess(value);
            },
        },
        beforeDestroy() {
            this.extension.options.onRemove(this.value);
        },
    }
</script>
