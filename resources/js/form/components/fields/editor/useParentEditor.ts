import { FormFieldProps } from "@/form/types";
import { FormEditorFieldData } from "@/types";
import { inject, Ref } from "vue";
import { ContentEmbedManager } from "@/content/ContentEmbedManager";
import { Form } from "@/form/Form";
import { ContentUploadManager } from "@/content/ContentUploadManager";
import EditorUploadModal from "@/form/components/fields/editor/extensions/upload/EditorUploadModal.vue";
import EditorEmbedModal from "@/form/components/fields/editor/extensions/embed/EditorEmbedModal.vue";

/**
 * @see import('./Editor.vue') -> provide('editor')
 */
export type ParentEditor = {
    props: FormFieldProps<FormEditorFieldData>,
    embedManager: ContentEmbedManager<Form>,
    embedModal: Ref<InstanceType<typeof EditorEmbedModal>>
    uploadManager: ContentUploadManager<Form>,
    uploadModal: Ref<InstanceType<typeof EditorUploadModal>>,
};

export function useParentEditor() {
    return inject<ParentEditor>('editor');
}
