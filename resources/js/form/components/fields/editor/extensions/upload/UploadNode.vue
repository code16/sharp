<script setup lang="ts">
    import { Upload as UploadExtension, UploadNodeAttributes } from "./Upload"
    import { computed, nextTick, onMounted, onUnmounted, ref } from "vue";
    import { __ } from "@/utils/i18n";
    import NodeRenderer from "../../NodeRenderer.vue";
    import { showAlert } from "@/utils/dialogs";
    import Upload from "@/form/components/fields/upload/Upload.vue";
    import { FormUploadFieldData } from "@/types";
    import { ExtensionNodeProps } from "@/form/components/fields/editor/types";
    import { useParentEditor } from "@/form/components/fields/editor/useParentEditor";

    const props = defineProps<ExtensionNodeProps<typeof UploadExtension, UploadNodeAttributes>>();

    const parentEditor = useParentEditor();
    const uploadModal = useParentEditor().uploadModal;
    const uploadManager = useParentEditor().uploadManager;
    const uploadComponent = ref<InstanceType<typeof Upload>>();
    const upload = computed(() => uploadManager.getUpload(props.node.attrs['data-key']));

    function onThumbnailGenerated(preview: string) {
        uploadManager.updateUpload(props.node.attrs['data-key'], {
            file: {
                ...upload.value.file,
                thumbnail: preview,
            }
        });
    }

    function onUploadTransformed(value: FormUploadFieldData['value']) {
        uploadManager.updateUpload(props.node.attrs['data-key'], {
            file: value,
        });
    }

    async function onUploadSuccess(value: FormUploadFieldData['value']) {
        uploadManager.updateUpload(props.node.attrs['data-key'], {
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

    function onRemove() {
        props.deleteNode();
        setTimeout(() => {
            props.editor.commands.focus();
        }, 0);
    }

    function onEdit(event: CustomEvent) {
        if(parentEditor.props.field.uploads.fields.legend) {
            event.preventDefault();
            uploadModal.value.open(props.node.attrs['data-key']);
        }
    }

    onMounted(() => {
        uploadManager.restoreUpload(props.node.attrs['data-key']);
    });

    onUnmounted(() => {
        uploadManager.removeUpload(props.node.attrs['data-key']);
    });
</script>

<template>
    <NodeRenderer class="editor__node" :node="node">
        <div class="border rounded-md p-4">
            <Upload
                :field="parentEditor.props.field.uploads.fields.file"
                :field-error-key="`${parentEditor.props.fieldErrorKey}-upload-${props.node.attrs['data-key']}`"
                :value="upload?.file"
                as-editor-embed
                @thumbnail="onThumbnailGenerated"
                @transform="onUploadTransformed"
                @error="onUploadError"
                @success="onUploadSuccess"
                @remove="onRemove"
                @edit="onEdit"
                ref="uploadComponent"
            />
            <template v-if="upload.legend">
                <div class="text-sm mt-2">
                    {{ upload.legend }}
                </div>
            </template>
        </div>
    </NodeRenderer>
</template>
