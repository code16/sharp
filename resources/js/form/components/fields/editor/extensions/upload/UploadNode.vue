<script setup lang="ts">
    import { Upload as UploadExtension } from "./upload"
    import { computed, onUnmounted } from "vue";
    import { __ } from "@/utils/i18n";
    import { NodeViewProps } from "@tiptap/core";
    import NodeRenderer from "../../NodeRenderer.vue";
    import { showAlert } from "@/utils/dialogs";
    import { Upload } from "@/form/components/fields";
    import { FormUploadFieldData } from "@/types";

    const props = defineProps<Omit<NodeViewProps, 'extension'> & {
        extension: typeof UploadExtension,
    }>();

    const error = computed(() => {
        if(props.node.attrs.notFound) {
            return __('sharp::form.editor.errors.unknown_file', {
                path: props.node.attrs.path ?? ''
            });
        }
    });

    const value = computed(() => {
        if(props.node.attrs.file) {
            return {
                file: props.node.attrs.file,
            }
        }
        return {
            path: props.node.attrs.path,
            disk: props.node.attrs.disk,
            name: props.node.attrs.name,
            filters: props.node.attrs.filters,
            thumbnail: props.node.attrs.thumbnail,
            size: props.node.attrs.size,
            uploaded: props.node.attrs.uploaded,
        }
    });

    function onThumbnail(preview: string) {
        props.updateAttributes({ thumbnail: preview });
    }

    function onUpdate(value: FormUploadFieldData['value']) {
        props.updateAttributes({
            filters: value.filters,
        });
        if(!props.node.attrs.file) {
            props.extension.options.onUpdate(value);
        }
    }

    function onRemove() {
        props.deleteNode();
        setTimeout(() => {
            props.editor.commands.focus();
        }, 0);
    }

    function onSuccess(value: FormUploadFieldData['value']) {
        props.updateAttributes({
            ...value,
            file: null,
            uploaded: true,
        });
        props.extension.options.onSuccess(value);
    }

    function onError(message: string, file: File) {
        props.deleteNode();
        showAlert(`${message}<br>&gt;&nbsp;${file.name}`, {
            isError: true,
            title: __(`sharp::modals.error.title`),
        });
    }

    async function init() {
        if(props.node.attrs.file || props.node.attrs.notFound) {
            return;
        }

        const data = await props.extension.options.registerFile(value.value);
        if(data) {
            props.updateAttributes(data);
        } else {
            props.updateAttributes({
                notFound: true,
            });
        }
    }

    init();

    onUnmounted(() => {
        if(!props.node.attrs.file) {
            props.extension.options.onRemove(value.value);
        }
    });
</script>

<template>
    <NodeRenderer class="editor__node" :node="node">
        <Upload
            :field="extension.options.fieldProps"
            :field-error-key="extension.options.fieldErrorKey"
            :value="value"
            :has-error="!!error"
            :root="false"
            @thumbnail="onThumbnail"
            @update="onUpdate"
            @error="onError"
            @sucess="onSuccess"
            @remove="onRemove"
        ></Upload>
        <template v-if="error">
            <div class="invalid-feedback d-block" style="font-size: .75rem">
                {{ error }}
            </div>
        </template>
    </NodeRenderer>
</template>
