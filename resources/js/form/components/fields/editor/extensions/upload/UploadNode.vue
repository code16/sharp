<script setup lang="ts">
    import { Upload as UploadExtension, UploadNodeAttributes } from "./Upload"
    import { computed, inject, onMounted, onUnmounted, ref } from "vue";
    import { __ } from "@/utils/i18n";
    import NodeRenderer from "../../NodeRenderer.vue";
    import { showAlert } from "@/utils/dialogs";
    import { Upload } from "@/form/components/fields";
    import { FormUploadFieldData, FormData } from "@/types";
    import EmbedFormModal from "@/form/components/fields/editor/extensions/embed/EmbedFormModal.vue";
    import { Form } from "@/form/Form";
    import { useParentForm } from "@/form/useParentForm";
    import {
        FormEditorUploadData,
        UploadManager
    } from "@/form/components/fields/editor/extensions/upload/UploadManager";
    import { ExtensionNodeProps } from "@/form/components/fields/editor/types";

    const props = defineProps<ExtensionNodeProps<typeof UploadExtension, UploadNodeAttributes>>();

    const modalVisible = ref(false);
    const editorUploadForm = ref<Form>(null);
    const parentForm = useParentForm();
    const uploads = inject<UploadManager>('uploads');
    const uploadComponent = ref<InstanceType<typeof Upload>>();

    const error = computed(() => {
        if(props.node.attrs.file?.not_found) {
            return __('sharp::form.editor.errors.unknown_file', {
                path: props.node.attrs.file.path ?? ''
            });
        }
    });

    function showFormModal() {
        const formProps = {
            fields: props.extension.options.editorField.uploads.fields,
            layout: props.extension.options.editorField.uploads.layout,
            data: {
                file: props.node.attrs.file,
                legend: props.node.attrs.legend,
            },
        } as FormData;
        editorUploadForm.value = new Form(formProps, parentForm.entityKey, parentForm.instanceId);
        modalVisible.value = true;
    }

    async function postForm(data: FormEditorUploadData) {
        const responseData = await uploads.postForm(
            props.node.attrs['data-unique-id'],
            data
        );

        props.updateAttributes({
            file: responseData.file,
            legend: responseData.legend,
        });

        modalVisible.value = false;
    }

    function onThumbnail(preview: string) {
        props.updateAttributes({
            file: {
                ...props.node.attrs.file,
                thumbnail: preview,
            }
        });
    }

    function onTransformed(value: FormUploadFieldData['value']) {
        props.updateAttributes({
            file: {
                ...props.node.attrs.file,
                filters: value.filters,
            }
        });

        if(!props.node.attrs.isNew) {
            uploads.updateUpload(props.node.attrs['data-unique-id'], {
                file: props.node.attrs.file,
                legend: props.node.attrs.legend,
            })
        }
    }

    function onRemove() {
        props.deleteNode();
        uploads.removeUpload(props.node.attrs['data-unique-id']);
        setTimeout(() => {
            props.editor.commands.focus();
        }, 0);
    }

    async function onSuccess(value: FormUploadFieldData['value']) {
        const responseData = await uploads.postForm(
            props.node.attrs['data-unique-id'],
            {
                file: value,
            }
        )
        props.updateAttributes({
            file: responseData.file,
            nativeFile: null,
        });
    }

    function onError(message: string, file: File) {
        props.deleteNode();
        showAlert(`${message}<br>&gt;&nbsp;${file.name}`, {
            isError: true,
            title: __(`sharp::modals.error.title`),
        });
    }

    function onEdit(event: CustomEvent) {
        if(props.extension.options.editorField.uploads.fields.legend) {
            event.preventDefault();
            showFormModal();
        }
    }

    async function init() {
        if(props.node.attrs.isNew) {
            if(props.extension.options.editorField.uploads.fields.legend) {
                showFormModal();
            } else {
                uploadComponent.value.browseFiles();
            }
        } else {
            const resolved = await uploads.getResolvedUpload(
                props.node.attrs['data-unique-id']
            );
            if(resolved) {
                props.updateAttributes({
                    file: resolved.file,
                });
            }
        }
    }

    onMounted(() => {
        init();
    });

    onUnmounted(() => {
        uploads.removeUpload(props.node.attrs['data-unique-id']);
    });
</script>

<template>
    <NodeRenderer class="editor__node" :node="node">
        <Upload
            :field="extension.options.editorField.uploads.fields.file"
            :field-error-key="null"
            :value="node.attrs.file"
            :has-error="!!error"
            :root="false"
            @thumbnail="onThumbnail"
            @update="onTransformed"
            @error="onError"
            @success="onSuccess"
            @remove="onRemove"
            @edit="onEdit"
            ref="uploadComponent"
        ></Upload>

        <template v-if="error">
            <div class="text-sm text-red-700 mt-1">
                {{ error }}
            </div>
        </template>

        <EmbedFormModal
            :visible="modalVisible"
            :form="editorUploadForm"
            :post="postForm"
        >
            <template v-slot:title>
                Upload
            </template>
        </EmbedFormModal>
    </NodeRenderer>
</template>
