import { FormFieldProps } from "@/form/types";
import { FormEditorFieldData } from "@/types";
import { inject, Ref } from "vue";
import { ContentEmbedManager } from "@/content/ContentEmbedManager";
import { Form } from "@/form/Form";
import { ContentUploadManager } from "@/content/ContentUploadManager";
import UploadModal from "@/form/components/fields/editor/extensions/upload/UploadModal.vue";

/**
 * @see import('./Editor.vue') provide('editor')
 */
export type ParentEditor = {
    props: FormFieldProps<FormEditorFieldData>,
    embedManager: ContentEmbedManager<Form>,
    uploadManager: ContentUploadManager<Form>,
    uploadModal: Ref<InstanceType<typeof UploadModal>>,
};

export function useParentEditor() {
    return inject<ParentEditor>('editor');
}
