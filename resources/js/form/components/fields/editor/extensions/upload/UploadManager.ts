import { api } from "@/api";
import { route } from "@/utils/url";
import { Form } from "@/form/Form";
import { FormEditorFieldData, FormUploadFieldValueData } from "@/types";
import { filesEquals } from "@/utils/upload";

export type FormEditorUploadData = {
    file: FormUploadFieldValueData,
    legend?: string,
}

export class UploadManager {

    files: FormUploadFieldValueData[] = [];

    editorField: FormEditorFieldData;

    allInitialUploadsResolved = Promise.withResolvers();

    parentForm: Form;

    onFilesUpdated: (files: FormUploadFieldValueData[]) => any;

    editorCreated = false;

    constructor(
        parentForm: Form,
        editorField: FormEditorFieldData,
        { onFilesUpdated }: { onFilesUpdated: UploadManager['onFilesUpdated'] }
    ) {
        this.parentForm = parentForm;
        this.editorField = editorField;
        this.onFilesUpdated = onFilesUpdated;
    }

    async registerUploadFile(file: FormUploadFieldValueData): Promise<FormUploadFieldValueData> {
        if(this.editorCreated) {
            return file;
        }

        const index = this.files.length;
        this.files.push(file);

        await this.allInitialUploadsResolved.promise;

        return this.files[index];
    }

    async resolveAllInitialContentUploads() {
        const { entityKey, instanceId } = this.parentForm;

        if(this.editorCreated) {
            return;
        }

        if(this.files.length) {
            this.files = await api.post(route('code16.sharp.api.files.show', { entityKey, instanceId }), { files: this.files })
                .then(response => response.data.files);

            this.allInitialUploadsResolved.resolve(true);
        }
    }

    async postForm(data: FormEditorUploadData): Promise<FormEditorUploadData> {
        const responseData = await api.post(
            route('code16.sharp.api.form.editor.upload.form.update'), {
                data,
                fields: this.editorField.embeds.upload.fields,
            }
        )
            .then(response => response.data);

        const index = this.files.findIndex(file => filesEquals(file, data.file));

        if(index < 0) {
            this.files.push(responseData);
        } else {
            this.files[index] = responseData;
        }

        this.onFilesUpdated(this.files);

        return responseData;
    }

}
