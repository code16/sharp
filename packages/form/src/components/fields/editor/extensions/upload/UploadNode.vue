<template>
    <NodeRenderer class="editor__node" :node="node">
        <VueClip
            :value="value"
            :root="false"
            :options="options"
            :focused="selected"
            :invalid="!!error"
            v-bind="fieldProps"
            @updated="handleUpdated"
            @removed="handleRemoveClicked"
            @success="handleSuccess"
            @error="handleError"
        />
        <template v-if="error">
            <div class="invalid-feedback d-block" style="font-size: .75rem">
                {{ error }}
            </div>
        </template>
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
            ready() {
                if(this.node.attrs.file) {
                    return true;
                }
                return this.extension.options.isReady(this.node.attrs);
            },
            file() {
                return this.extension.options.getFile(this.node.attrs);
            },
            value() {
                const attrs = this.node.attrs;
                if(attrs.file) {
                    return {
                        file: attrs.file,
                    }
                }
                return {
                    ...this.serializableAttrs,
                    ...this.file,
                }
            },
            serializableAttrs() {
                const attrs = this.node.attrs;
                return {
                    path: attrs.path,
                    disk: attrs.disk,
                    name: attrs.name,
                    filters: attrs.filters,
                    uploaded: attrs.uploaded,
                };
            },
            error() {
                const attrs = this.node.attrs;
                if(this.ready && !attrs.file && !this.file) {
                    return lang('form.editor.errors.unknown_file')
                        .replace(':path', attrs.path ?? '');
                }
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
            handleError(message, file) {
                this.deleteNode();
                showAlert(`${message}<br>&gt;&nbsp;${file.name}`, {
                    isError: true,
                    title: lang(`modals.error.title`),
                });
            },
            handleUpdated(value) {
                this.updateAttributes({
                    filters: value.filters,
                });
                if(!this.node.attrs.file) {
                    this.extension.options.onUpdate(value);
                }
            },
            handleSuccess(value) {
                this.updateAttributes({
                    name: value.name,
                    disk: value.disk,
                    path: value.path,
                    file: null,
                    uploaded: true,
                });
                this.extension.options.onSuccess(value);
            },
        },
        created() {
            if(!this.node.attrs.file) {
                this.extension.options.registerFile({
                    ...this.serializableAttrs,
                });
            }
        },
        beforeDestroy() {
            if(!this.node.attrs.file) {
                this.extension.options.onRemove(this.value);
            }
        },
    }
</script>
