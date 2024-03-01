import { FormEditorFieldData } from "@/types";
import { ContentUploadManager } from "@/content/ContentUploadManager";
import { provide } from "vue";
import { Extension } from "@tiptap/core";
import { Upload } from "@/form/components/fields/editor/extensions/upload/Upload";
import { Form } from "@/form/Form";
import { FormFieldProps } from "@/form/types";

export function useUploads(
    props: FormFieldProps<FormEditorFieldData>,
    uploadManager: ContentUploadManager<Form>,
    content: string,
) {
    if(!props.field.uploads) {
        return { extensions: [] };
    }

    provide('uploads', uploadManager);

    const updatedContent = uploadManager.withUploadUniqueId(content);

    uploadManager.resolveContentUploads(updatedContent);

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

