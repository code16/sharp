import { api } from "@/api";
import { route } from "@/utils/url";
import { Form } from "@/form/Form";
import { EmbedData, FormEditorFieldData, FormUploadFieldValueData } from "@/types";
import { filesEquals } from "@/utils/upload";
import { parseAttributeValue } from "@/embeds/utils/attributes";
import { hyphenate } from "@/utils";

export type FormEditorUploadData = {
    file: FormUploadFieldValueData,
    legend?: string,
}

type ContentUpload = {
    id: string,
    resolved?: PromiseWithResolvers<true>
    value: FormEditorUploadData,
}

export class UploadManager {
    contentUploads: { [id:string]: ContentUpload } = {};
    editorField: FormEditorFieldData;
    root: Form;
    onFilesUpdated: (files: FormUploadFieldValueData[]) => any;
    uniqueId = 0

    constructor(
        root: Form,
        { editorField, onFilesUpdated }: {
            editorField?: UploadManager['editorField'],
            onFilesUpdated?: UploadManager['onFilesUpdated']
        } = {}
    ) {
        this.root = root;
        this.editorField = editorField;
        this.onFilesUpdated = onFilesUpdated;
    }

    get files() {
        return Object.values(this.contentUploads)
            .map(contentUpload => contentUpload.value.file);
    }

    newId() {
        return `${this.uniqueId++}`;
    }

    withUploadUniqueId(content: string, toggle: boolean = true): string {
        const parser = new DOMParser();
        const document = parser.parseFromString(content, 'text/html');

        document.querySelectorAll('x-sharp-file,x-sharp-image').forEach(element => {
            if(toggle) {
                element.setAttribute('data-unique-id', this.newId());
            } else {
                element.removeAttribute('data-unique-id');
            }
        });

        return document.body.innerHTML;
    }

    serializeContent(content: string): string {
        return this.withUploadUniqueId(content, false);
    }

    async resolveUploads(content: string) {
        const { entityKey, instanceId } = this.root;
        const parser = new DOMParser();
        const document = parser.parseFromString(content, 'text/html');

        const contentUploads = [...document.querySelectorAll('x-sharp-file,x-sharp-image')]
            .map(element => ({
                id: element.getAttribute('data-unique-id'),
                resolved: Promise.withResolvers<true>(),
                value: {
                    file: parseAttributeValue(element.getAttribute('file')),
                    legend: parseAttributeValue(element.getAttribute('legend')),
                }
            }));

        if(!contentUploads.length) {
            return;
        }

        this.contentUploads = {
            ...this.contentUploads,
            ...Object.fromEntries(contentUploads.map(contentUpload => [contentUpload.id, contentUpload])),
        }

        return (async () => {
            const resolvedFiles = await api.post(route('code16.sharp.api.files.show', { entityKey, instanceId }), {
                files: contentUploads.map(contentUpload => contentUpload.value)
            })
                .then(response => response.data.files) as FormUploadFieldValueData[]

            resolvedFiles.forEach((resolvedFile, index) => {
                this.contentUploads[contentUploads[index].id].value = {
                    ...this.contentUploads[contentUploads[index].id].value,
                    file: resolvedFile,
                }
                this.contentUploads[contentUploads[index].id].resolved.resolve(true);
            });
        })();
    }

    async postForm(id: string, data: FormEditorUploadData): Promise<FormEditorUploadData> {
        const responseData = await api.post(
            route('code16.sharp.api.form.editor.upload.form.update'), {
                data,
                fields: this.editorField.embeds.upload.fields,
            }
        )
            .then(response => response.data);

        this.contentUploads[id].value = responseData;

        this.onFilesUpdated(this.files);

        return responseData;
    }

}
