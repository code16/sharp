<script setup lang="ts">
    import { Upload as UploadExtension, UploadNodeAttributes } from "./upload"
    import { computed, onUnmounted, ref } from "vue";
    import { __ } from "@/utils/i18n";
    import { NodeViewProps } from "@tiptap/core";
    import NodeRenderer from "../../NodeRenderer.vue";
    import { showAlert } from "@/utils/dialogs";
    import { Upload } from "@/form/components/fields";
    import { FormUploadFieldData, FormData } from "@/types";
    import { api } from "@/api";
    import { route } from "@/utils/url";
    import EmbedFormModal from "@/form/components/fields/editor/extensions/embed/EmbedFormModal.vue";
    import { Form } from "@/form/Form";
    import { useParentForm } from "@/form/useParentForm";

    const props = defineProps<Omit<NodeViewProps, 'extension' | 'node'> & {
        extension: typeof UploadExtension,
        node: Omit<NodeViewProps['node'], 'attrs'> & { attrs: UploadNodeAttributes }
    }>();

    const error = computed(() => {
        if(props.node.attrs.notFound) {
            return __('sharp::form.editor.errors.unknown_file', {
                path: props.node.attrs.file.path ?? ''
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
    const editForm = ref(null);
    const parentForm = useParentForm();

    function onEdit(event: CustomEvent) {
        const editorProps = props.extension.options.editorProps;
        if(editorProps.field.embeds.upload.fields.legend) {
            event.preventDefault();
            const formProps = {
                ...editorProps.field.embeds.upload,
                data: props.node.attrs,
            } as FormData;
            editForm.value = new Form(formProps, parentForm.entityKey, parentForm.instanceId);
            showEditModal.value = true;
        }
    }

    async function postForm() {
        const attributes = await api.post(route('code16.sharp.api.form.editor.upload.form.update'))
            .then(response => response.data);

        props.updateAttributes(attributes);
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
            :field="extension.options.editorProps.field.embeds.upload.fields.file"
            :field-error-key="extension.options.editorProps.fieldErrorKey"
            :value="{
                ...node.attrs.file,
                file: node.attrs.htmlFile,
            }"
            :has-error="!!error"
            :root="false"
            @thumbnail="onThumbnail"
            @update="onUpdate"
            @error="onError"
            @sucess="onSuccess"
            @remove="onRemove"
            @edit="onEdit"
        ></Upload>

        <EmbedFormModal :visible="showEditModal" :form="editForm" :post="postForm">
            <template v-slot:title>
                Upload
            </template>
        </EmbedFormModal>

        <template v-if="error">
            <div class="invalid-feedback d-block" style="font-size: .75rem">
                {{ error }}
            </div>
        </template>
    </NodeRenderer>
</template>
