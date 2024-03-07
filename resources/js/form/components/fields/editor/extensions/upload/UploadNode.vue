<script setup lang="ts">
    import { Upload as UploadExtension, UploadNodeAttributes } from "./Upload"
    import { computed, nextTick, onMounted, onUnmounted, ref } from "vue";
    import { __ } from "@/utils/i18n";
    import NodeRenderer from "../../NodeRenderer.vue";
    import { showAlert } from "@/utils/dialogs";
    import Upload from "@/form/components/fields/upload/Upload.vue";
    import { FormUploadFieldData, FormData } from "@/types";
    import EmbedFormModal from "@/form/components/fields/editor/extensions/embed/EmbedFormModal.vue";
    import { Form } from "@/form/Form";
    import { useParentForm } from "@/form/useParentForm";
    import { ExtensionNodeProps } from "@/form/components/fields/editor/types";
    import { FormEditorUploadData } from "@/content/types";
    import { useParentEditor } from "@/form/components/fields/editor/useParentEditor";

    const props = defineProps<ExtensionNodeProps<typeof UploadExtension, UploadNodeAttributes>>();

    const modalVisible = ref(false);
    const editorUploadForm = ref<Form>(null);
    const parentForm = useParentForm();
    const parentEditor = useParentEditor();
    const uploadManager = parentEditor.uploadManager;
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
            fields: parentEditor.props.field.uploads.fields,
            layout: parentEditor.props.field.uploads.layout,
            data: {
                file: props.node.attrs.file,
                legend: props.node.attrs.legend,
            },
        } as FormData;
        editorUploadForm.value = new Form(formProps, parentForm.entityKey, parentForm.instanceId);
        modalVisible.value = true;
    }

    async function postModalForm(data: FormEditorUploadData) {
        const responseData = await uploadManager.postForm(
            props.node.attrs['data-unique-id'],
            data
        );

        props.updateAttributes({
            file: {
                ...responseData.file,
                thumbnail: data.file.thumbnail,
            },
            legend: responseData.legend,
            isNew: false,
            droppedFile: null,
        });

        modalVisible.value = false;
    }

    function onThumbnailGenerated(preview: string) {
        props.editor.commands.withoutHistory(() => {
            props.updateAttributes({
                file: {
                    ...props.node.attrs.file,
                    thumbnail: preview,
                },
            });
        });
    }

    function onUploadTransformed(value: FormUploadFieldData['value']) {
        props.updateAttributes({
            file: value,
        });

        if(!props.node.attrs.isNew) {
            uploadManager.updateUpload(props.node.attrs['data-unique-id'], {
                file: value,
                legend: props.node.attrs.legend,
            });
        }
    }

    function onRemove() {
        props.deleteNode();
        uploadManager.removeUpload(props.node.attrs['data-unique-id']);
        setTimeout(() => {
            props.editor.commands.focus();
        }, 0);
    }

    function onEdit(event: CustomEvent) {
        if(parentEditor.props.field.uploads.fields.legend) {
            event.preventDefault();
            showFormModal();
        }
    }

    async function onUploadSuccess(value: FormUploadFieldData['value']) {
        await postModalForm({
            file: value,
        });
    }

    function onUploadError(message: string, file: File) {
        props.deleteNode();
        showAlert(`${message}<br>&gt;&nbsp;${file.name}`, {
            isError: true,
            title: __(`sharp::modals.error.title`),
        });
    }

    function onModalCancel() {
        modalVisible.value = false;

        if(props.node.attrs.isNew) {
            props.editor.commands.withoutHistory(() => {
                props.deleteNode();
            });
            setTimeout(() => {
                props.editor.commands.focus();
            }, 0);
        }
    }

    async function init() {
        if(props.node.attrs.droppedFile) {
            uploadComponent.value.upload(props.node.attrs.droppedFile);

            return;
        }

        if(props.node.attrs.isNew) {
            if(parentEditor.props.field.uploads.fields.legend) {
                showFormModal();
            } else {
                await nextTick();
                const chosen = await uploadComponent.value.browseFiles();
                props.editor.commands.focus(props.getPos() + props.node.nodeSize);
                if(!chosen) {
                    props.editor.commands.withoutHistory(() => {
                        props.deleteNode();
                    });
                }
            }
        } else {
            const resolved = await uploadManager.getResolvedUpload(
                props.node.attrs['data-unique-id']
            );
            if(resolved) {
                props.editor.commands.withoutHistory(() => {
                    props.updateAttributes({
                        file: resolved.file,
                    });
                });
            }
        }
    }

    onMounted(() => {
        init();
    });

    onUnmounted(() => {
        uploadManager.removeUpload(props.node.attrs['data-unique-id']);
    });
</script>

<template>
    <NodeRenderer class="editor__node" :node="node">
        <div v-show="!node.attrs.isNew || node.attrs.droppedFile">
            <div class="border rounded p-4">
                <Upload
                    :field="parentEditor.props.field.uploads.fields.file"
                    :field-error-key="`${parentEditor.props.fieldErrorKey}-upload-${props.node.attrs['data-unique-id']}`"
                    :value="node.attrs.file ? {
                        ...node.attrs.file,
                    } : null"
                    :has-error="!!error"
                    :root="false"
                    @thumbnail="onThumbnailGenerated"
                    @transform="onUploadTransformed"
                    @error="onUploadError"
                    @success="onUploadSuccess"
                    @remove="onRemove"
                    @edit="onEdit"
                    ref="uploadComponent"
                ></Upload>
                <template v-if="node.attrs.legend">
                    <div class="text-sm mt-2">
                        {{ node.attrs.legend }}
                    </div>
                </template>
            </div>
        </div>

        <template v-if="error">
            <div class="text-sm text-red-700 mt-1">
                {{ error }}
            </div>
        </template>

        <EmbedFormModal
            :visible="modalVisible"
            :form="editorUploadForm"
            :post="postModalForm"
            @cancel="onModalCancel"
        >
            <template v-slot:title>
                <template v-if="props.node.attrs.isNew">
                    {{ __('sharp::form.editor.dialogs.upload.title.new') }}
                </template>
                <template v-else>
                    {{ __('sharp::form.editor.dialogs.upload.title.update') }}
                </template>
            </template>
        </EmbedFormModal>
    </NodeRenderer>
</template>
