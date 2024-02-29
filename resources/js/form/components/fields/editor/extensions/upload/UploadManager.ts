import { api } from "@/api";
import { route } from "@/utils/url";
import { Form } from "@/form/Form";
import { EmbedData, FormEditorFieldData, FormUploadFieldValueData } from "@/types";
import { filesEquals } from "@/utils/upload";
import { parseAttributeValue } from "@/embeds/utils/attributes";
import { hyphenate } from "@/utils";
import { Show } from "@/show/Show";

export type FormEditorUploadData = {
    file: FormUploadFieldValueData,
    legend?: string,
}

type ContentUpload = {
    id: string,
    resolved?: PromiseWithResolvers<true>
    removed?: boolean,
    value: FormEditorUploadData,
}

export class UploadManager<Root extends Form | Show> {
    contentUploads: { [id:string]: ContentUpload } = {};
    root: Form | Show;
    onFilesUpdated: Root extends Form ? (files: FormUploadFieldValueData[]) => any : null;
    editorField: Root extends Form ? FormEditorFieldData : null;
    uniqueId = 0

    constructor(
        root: Root,
        config: {
            editorField: UploadManager<Root>['editorField'],
            onFilesUpdated: UploadManager<Root>['onFilesUpdated'],
        } = { editorField: null, onFilesUpdated: null }
    ) {
        this.root = root;
        this.editorField = config.editorField;
        this.onFilesUpdated = config.onFilesUpdated;
    }

    get files() {
        return Object.values(this.contentUploads)
            .filter(contentUpload => !contentUpload.removed)
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
                id: element.getAttribute('data-uid'),
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

    updateUpload(id: string, value: FormEditorUploadData) {
        this.contentUploads[id].value = value;
        this.onFilesUpdated(this.files);
    }

    removeUpload(id: string) {
        this.contentUploads[id] = {
            ...this.contentUploads[id],
            removed: true,
        };
        this.onFilesUpdated(this.files);
    }

    async getResolvedUpload(id: string): Promise<FormEditorUploadData | undefined> {
        if(this.contentUploads[id]?.removed) {
            this.contentUploads[id].removed = false;
        }

        await this.contentUploads[id]?.resolved?.promise;

        return this.contentUploads[id]?.value;
    }

    async postForm(id: string, data: FormEditorUploadData): Promise<FormEditorUploadData> {
        const responseData = await api.post(
            route('code16.sharp.api.form.editor.upload.form.update'), {
                data,
                fields: this.editorField.uploads.fields,
            }
        )
            .then(response => response.data);

        this.contentUploads[id] = {
            id,
            value: responseData,
        }

        this.onFilesUpdated(this.files);

        return responseData;
    }

}
