import { api } from "@/api";
import { route } from "@/utils/url";
import { Form } from "@/form/Form";
import { FormUploadFieldValueData } from "@/types";


export class UploadManager {

    editorUploadFiles: FormUploadFieldValueData[] = [];

    allInitialUploadsResolved = Promise.withResolvers();

    parentForm: Form;

    onUpdated: (uploadedOrTransformedFiles: FormUploadFieldValueData[]) => any;

    onUploadSuccess: (uploadedFile: FormUploadFieldValueData) => any;

    onUploadRemoved: (removedFile: FormUploadFieldValueData) => any;

    editorCreated = false;

    constructor(parentForm: Form, { onUpdated }: { onUpdated: UploadManager['onUpdated'] }) {
        this.parentForm = parentForm;
        this.onUpdated = onUpdated;
    }

    async registerUploadFile(file: FormUploadFieldValueData): Promise<FormUploadFieldValueData> {
        if(this.editorCreated) {
            this.onUpdated([file]);
            return file;
        }

        const index = this.editorUploadFiles.length;
        this.editorUploadFiles.push(file);

        await this.allInitialUploadsResolved.promise;

        return this.editorUploadFiles[index];
    }

    postResolveForm(upload: UploadData, contentUploadAttributes: UploadAttributesData): Promise<FormData> {
        const { entityKey, instanceId } = this.parentForm;

        return api
            .post(
                instanceId
                    ? route('code16.sharp.api.upload.instance.form.show', { uploadKey: upload.key, entityKey, instanceId })
                    : route('code16.sharp.api.upload.form.show', { uploadKey: upload.key, entityKey }),
                { ...contentUploadAttributes }
            )
            .then(response => response.data);
    }

    async postForm(upload: UploadData, data: FormData['data'], uploadForm: Form): Promise<UploadAttributesData> {
        const { entityKey, instanceId } = this.parentForm;
        const responseData = await api
            .post(
                instanceId
                    ? route('code16.sharp.api.upload.instance.form.update', { uploadKey: upload.key, entityKey, instanceId })
                    : route('code16.sharp.api.upload.form.update', { uploadKey: upload.key, entityKey }),
                { ...data }
            )
            .then(response => response.data);

        this.onUploadUpdated(uploadForm.getAllUploadedOrTransformedFiles(responseData));

        return responseData;
    }

    async resolveAllContentUploads() {
        const { entityKey, instanceId } = this.parentForm;

        const uploads = Object.entries(this.contentUploads)
            .map(([uploadKey, attributes]) => ({ key: uploadKey, attributes }));

        const responseData = await api
            .post(
                instanceId
                    ? route('code16.sharp.api.upload.instance.form.show', { entityKey, instanceId })
                    : route('code16.sharp.api.upload.form.show', { entityKey }),
                { uploads }
            )
            .then(response => response.data);

        this.onUploadUpdated(this.parentForm.getAllUploadedOrTransformedFiles(responseData));
    }

}
