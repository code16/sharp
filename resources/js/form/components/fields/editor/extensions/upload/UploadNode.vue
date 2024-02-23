<script setup lang="ts">
    import { Upload as UploadExtension, UploadNodeAttributes } from "./Upload"
    import { computed, inject, onUnmounted, ref } from "vue";
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
    import { UploadManager } from "@/form/components/fields/editor/extensions/upload/UploadManager";

    const props = defineProps<Omit<NodeViewProps, 'extension' | 'node'> & {
        extension: typeof UploadExtension,
        node: Omit<NodeViewProps['node'], 'attrs'> & { attrs: UploadNodeAttributes }
    }>();

    const uploads = inject<UploadManager>('uploads');

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
            uploads.onUploadTransformed(value);
        }
    }

    function onRemove() {
        props.deleteNode();
        uploads.onUploadRemoved(props.node.attrs.file);
        setTimeout(() => {
            props.editor.commands.focus();
        }, 0);
    }

    function onSuccess(value: FormUploadFieldData['value']) {
        props.updateAttributes({
            file: value,
            htmlFile: null,
        });
        uploads.onUploadSuccess(value);
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

        const registeredFile = await uploads.registerUploadFile(
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
        if(props.extension.options.editorField.uploads.fields.legend) {
            event.preventDefault();
            const formProps = {
                ...props.extension.options.editorField.uploads,
                data: props.node.attrs,
            } as FormData;
            editForm.value = new Form(formProps, parentForm.entityKey, parentForm.instanceId);
            showEditModal.value = true;
        }
    }

    async function postForm(data) {
        const responseData = await uploads.postForm(data);

        props.updateAttributes({
            file: responseData.file,
            legend: responseData.legend,
        });
    }

    onUnmounted(() => {
        if(!props.node.attrs.htmlFile) {
            uploads.onRemove(props.node.attrs.file);
        }
    });
</script>

<template>
    <NodeRenderer class="editor__node" :node="node">
        <Upload
            :field="extension.options.editorField.uploads.fields.file"
            :field-error-key="null"
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
