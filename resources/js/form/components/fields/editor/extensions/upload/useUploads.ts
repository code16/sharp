import { FormFieldProps } from "@/form/components/types";
import { FormEditorFieldData } from "@/types";
import { UploadManager } from "@/form/components/fields/editor/extensions/upload/UploadManager";
import { provide } from "vue";
import { Extension } from "@tiptap/core";
import { Upload } from "@/form/components/fields/editor/extensions/upload/Upload";


export function useUploads(
    props: FormFieldProps<FormEditorFieldData>,
    uploads: UploadManager,
) {
    if(!props.field.uploads) {
        return [];
    }

    provide('uploads', uploads);

    return [
        Extension.create({
            name: 'initUploads',
            onCreate() {
                uploads.resolveAllInitialContentUploads();
                uploads.editorCreated = true;
            }
        }),
        Upload.configure({
            editorField: props.field,
        }),
    ];
}
