<template>
    <NodeRenderer class="editor__node" :node="node">
        <VueClip
            :value="resolvedValue"
            :root="false"
            :options="options"
            :focused="selected"
            :invalid="!!error"
            v-bind="fieldProps"
            @updated="handleUpdate"
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
            value() {
                const attrs = this.node.attrs;
                if(attrs.file) {
                    return {
                        file: attrs.file,
                    }
                }
                return this.extension.options.getFile(attrs);
            },
            resolvedValue() {
                return this.value ?? this.node.attrs;
            },
            error() {
                if(!this.value) {
                    return lang('form.editor.errors.unknown_file')
                        .replace(':path', this.node.attrs.path ?? '');
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
