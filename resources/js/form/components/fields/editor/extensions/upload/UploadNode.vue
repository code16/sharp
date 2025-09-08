<script setup lang="ts">
    import { Upload as UploadExtension, UploadNodeAttributes } from "./Upload"
    import { computed, nextTick, onBeforeUnmount, onMounted, onUnmounted, ref } from "vue";
    import { __ } from "@/utils/i18n";
    import NodeRenderer from "../../NodeRenderer.vue";
    import { showAlert } from "@/utils/dialogs";
    import Upload from "@/form/components/fields/upload/Upload.vue";
    import { FormUploadFieldData } from "@/types";
    import { ExtensionNodeProps } from "@/form/components/fields/editor/types";
    import { useParentEditor } from "@/form/components/fields/editor/useParentEditor";
    import { useEditorNode } from "@/form/components/fields/editor/useEditorNode";
    import { DropdownMenuItem } from "@/components/ui/dropdown-menu";

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
        props.editor.commands.setNodeSelection(props.getPos());
        props.deleteNode();
        setTimeout(() => {
            props.editor.commands.focus();
        });
    }

    function onEdit(event: CustomEvent) {
        if(parentEditor.props.field.uploads.fields.legend) {
            event.preventDefault();
            uploadModal.value.open({ id: props.node.attrs['data-key'], locale: props.extension.options.locale });
        }
    }
</script>

<template>
    <NodeRenderer
        class="block my-4 first:mt-0 last:mb-0 border rounded-md p-4 outline-none"
        :class="{ 'group-focus/editor:border-primary': props.selected }"
        :node="node"
    >
        <Upload
            :field="{
                ...parentEditor.props.field.uploads.fields.file,
                readOnly: parentEditor.props.field.readOnly,
            }"
            :field-error-key="`${parentEditor.props.fieldErrorKey}-upload-${props.node.attrs['data-key']}`"
            :value="upload?.file"
            as-editor-embed
            persist-thumbnail-url
            :legend="upload.legend"
            :dropdown-edit-label="parentEditor.props.field.uploads.fields.legend ? __('sharp::form.editor.extension_node.edit_button') : null"
            :aria-label="props.node.attrs.isImage
                ? __('sharp::form.editor.extension_node.upload_image.aria_label')
                : __('sharp::form.editor.extension_node.upload.aria_label')"
            @thumbnail="onThumbnailGenerated"
            @transform="onUploadTransformed"
            @error="onUploadError"
            @success="onUploadSuccess"
            @remove="onRemove"
            @edit="onEdit"
            ref="uploadComponent"
        >
            <template #dropdown-menu>
                <DropdownMenuItem @click="props.editor.commands.copyNode(props.getPos())">
                    {{ __('sharp::form.editor.extension_node.copy_button') }}
                </DropdownMenuItem>
            </template>
        </Upload>
    </NodeRenderer>
</template>
