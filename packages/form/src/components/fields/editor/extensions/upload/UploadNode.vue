<template>
    <NodeRenderer class="editor__node" :node="node">
        <VueClip
            :value="value"
            :root="false"
            :options="options"
            :invalid="!!error"
            persist-thumbnails
            v-bind="fieldProps"
            @thumbnail="handleThumbnailChanged"
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
    import VueClip from "../../../upload/VueClip.vue";
    import NodeRenderer from "../../NodeRenderer.vue";
    import { showAlert, getUniqueId } from "sharp";
    import { getUploadOptions } from "../../../../../util/upload";
    import { __ } from "@/utils/i18n";

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
                return {
                    path: attrs.path,
                    disk: attrs.disk,
                    name: attrs.name,
                    filters: attrs.filters,
                    thumbnail: attrs.thumbnail,
                    size: attrs.size,
                    uploaded: attrs.uploaded,
                }
            },
            error() {
                if(this.node.attrs.notFound) {
                    return __('sharp::form.editor.errors.unknown_file', {
                        path: this.node.attrs.path ?? ''
                    });
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
            handleThumbnailChanged(thumbnail) {
                this.updateAttributes({
                    thumbnail,
                });
            },
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
                    title: __(`sharp::modals.error.title`),
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
                    ...value,
                    file: null,
                    uploaded: true,
                });
                this.extension.options.onSuccess(value);
            },
            async init() {
                if(this.node.attrs.file || this.node.attrs.notFound) {
                    return;
                }

                const data = await this.extension.options.registerFile(this.value);
                if(data) {
                    this.updateAttributes(data);
                } else {
                    this.updateAttributes({
                        notFound: true,
                    });
                }
            },
        },
        created() {
            this.init();
        },
        beforeDestroy() {
            if(!this.node.attrs.file) {
                this.extension.options.onRemove(this.value);
            }
        },
    }
</script>
