import { FormFieldProps } from "@/form/components/types";
import { FormEditorFieldData } from "@/types";
import { UploadManager } from "@/form/components/fields/editor/extensions/upload/UploadManager";
import { provide } from "vue";
import { Extension } from "@tiptap/core";
import { Embed } from "@/form/components/fields/editor/extensions/embed/Embed";
import { Upload } from "@/form/components/fields/editor/extensions/upload/Upload";


export function useUploadExtensions(
    props: FormFieldProps<FormEditorFieldData>,
    uploads: UploadManager,
) {
    if(!props.field.uploads) {
        return [];
    }

    provide('uploads', uploads);

    return [
        Extension.create({
            onCreate() {
                uploads.editorCreated = true;
                uploads.resolveAllInitialContentUploads();
            }
        }),
        Upload,
    ];
}
