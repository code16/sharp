import { FormFieldProps } from "@/form/types";
import { FormEditorFieldData } from "@/types";
import { inject } from "vue";
import { ContentEmbedManager } from "@/content/ContentEmbedManager";
import { Form } from "@/form/Form";
import { ContentUploadManager } from "@/content/ContentUploadManager";

/**
 * @see import('./Editor.vue') provide('editor')
 */
export type ParentEditor = {
    props: FormFieldProps<FormEditorFieldData>,
    embedManager: ContentEmbedManager<Form>
    uploadManager: ContentUploadManager<Form>
};

export function useParentEditor() {
    return inject<ParentEditor>('editor');
}
