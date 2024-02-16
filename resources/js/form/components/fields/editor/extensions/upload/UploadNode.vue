<script setup lang="ts">
    import { Upload as UploadExtension } from "./upload"
    import { computed, onUnmounted, ref } from "vue";
    import { __ } from "@/utils/i18n";
    import { NodeViewProps } from "@tiptap/core";
    import NodeRenderer from "../../NodeRenderer.vue";
    import { showAlert } from "@/utils/dialogs";
    import { Upload } from "@/form/components/fields";
    import { FormUploadFieldData } from "@/types";
    import { Modal } from "@/components/ui";
    import Form from "@/Pages/Form/Form.vue";

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

    function onThumbnail(preview: string) {
        props.updateAttributes({
            file: {
                ...props.node.attrs.file,
                thumbnail: preview,
            }
        });
    }

    function onUpdate(value: FormUploadFieldData['value']) {
        props.updateAttributes({
            file: {
                ...props.node.attrs.file,
                filters: value.filters,
            }
        });
        if(!props.node.attrs.htmlFile) {
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
            file: value,
            htmlFile: null,
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
        if(props.node.attrs.htmlFile || props.node.attrs.notFound) {
            return;
        }

        const registeredFile = await props.extension.options.registerFile(
            props.node.attrs.file
        );
        if(registeredFile) {
            props.updateAttributes({
                file: registeredFile,
            });
        } else {
            props.updateAttributes({
                notFound: true,
            });
        }
    }

    init();

    const showEditModal = ref(false);
    function onEdit(event: CustomEvent) {
        const editorProps = props.extension.options.editorProps;
        if(editorProps.field.embeds.upload.hasLegend) {
            event.preventDefault();
            showEditModal.value = true;
        }
    }

    onUnmounted(() => {
        if(!props.node.attrs.htmlFile) {
            props.extension.options.onRemove(props.node.attrs.file);
        }
    });
</script>

<template>
    <NodeRenderer class="editor__node" :node="node">
        <Upload
            :field="extension.options.editorProps.field.embeds.upload"
            :field-error-key="extension.options.editorProps.fieldErrorKey"
            :value="{
                ...props.node.attrs.file,
                file: props.node.attrs.htmlFile,
            }"
            :has-error="!!error"
            :root="false"
            @thumbnail="onThumbnail"
            @update="onUpdate"
            @error="onError"
            @sucess="onSuccess"
            @remove="onRemove"
        ></Upload>

        <Modal :visible="showEditModal">
            <template v-slot:title>
                Upload
            </template>
            <template v-if="showEditModal">

            </template>
        </Modal>

        <template v-if="error">
            <div class="invalid-feedback d-block" style="font-size: .75rem">
                {{ error }}
            </div>
        </template>
    </NodeRenderer>
</template>
