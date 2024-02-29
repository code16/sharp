import { FormFieldProps } from "@/form/components/types";
import { FormEditorFieldData } from "@/types";
import { UploadManager } from "@/form/components/fields/editor/extensions/upload/UploadManager";
import { provide } from "vue";
import { Extension } from "@tiptap/core";
import { Upload } from "@/form/components/fields/editor/extensions/upload/Upload";
import { Form } from "@/form/Form";

export function useUploads(
    props: FormFieldProps<FormEditorFieldData>,
    uploadManager: UploadManager<Form>,
    content: string,
) {
    if(!props.field.uploads) {
        return { extensions: [] };
    }

    provide('uploads', uploadManager);

    const updatedContent = uploadManager.withUploadUniqueId(content);

    uploadManager.resolveUploads(updatedContent);

    return {
        extensions: [
            Extension.create({
                name: 'initUploads',
                onBeforeCreate() {
                    this.editor.setOptions({
                        content: updatedContent,
                    });
                },
            }),
            Upload.configure({
                editorField: props.field,
                uploadManager,
            }),
        ]
    };
}

