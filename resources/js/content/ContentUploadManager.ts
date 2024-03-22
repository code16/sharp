import { api } from "@/api";
import { route } from "@/utils/url";
import { Form } from "@/form/Form";
import { FormEditorFieldData } from "@/types";
import { Show } from "@/show/Show";
import { ContentManager } from "@/content/ContentManager";
import { FormEditorUploadData, MaybeLocalizedContent } from "@/content/types";
import { reactive } from "vue";

type ContentUpload = {
    id: string,
    // resolved?: PromiseWithResolvers<true>
    removed?: boolean,
    value: FormEditorUploadData,
}

export class ContentUploadManager<Root extends Form | Show> extends ContentManager {
    root: Form | Show;
    onUploadsUpdated: Root extends Form ? (uploads: FormEditorUploadData[]) => any : null;
    editorField: Root extends Form ? FormEditorFieldData : null;
    uniqueId = 0;

    state = reactive<{ contentUploads: { [id:string]: ContentUpload } }>({
        contentUploads: {}
    });

    constructor(
        root: Root,
        initialUploads: FormEditorFieldData['value']['uploads'] | undefined,
        config: {
            editorField: ContentUploadManager<Root>['editorField'],
            onUploadsUpdated: ContentUploadManager<Root>['onUploadsUpdated'],
        } = { editorField: null, onUploadsUpdated: null }
    ) {
        super();
        this.root = root;
        this.contentUploads = Object.fromEntries(initialUploads?.map((upload, index) => [index, { id:String(index), value:upload }]) ?? []);
        this.uniqueId = initialUploads?.length ?? 0;
        this.editorField = config.editorField;
        this.onUploadsUpdated = config.onUploadsUpdated;
    }

    get contentUploads() {
        return this.state.contentUploads;
    }

    set contentUploads(contentUploads: { [id:string]: ContentUpload }) {
        this.state.contentUploads = contentUploads;
    }

    get serializedUploads() {
        return Object.values(this.contentUploads)
            .filter(contentUpload => !contentUpload.removed)
            .map(contentUpload => contentUpload.value);
    }

    newId() {
        return String(this.uniqueId++);
    }

    // withUploadsUniqueId<Content extends MaybeLocalizedContent>(content: Content,): Content {
    //     if(this.editorField && !this.editorField.uploads) {
    //         return content;
    //     }
    //
    //     return this.maybeLocalized(content, content => {
    //         const contentDOM = new DOMParser().parseFromString(content, 'text/html');
    //
    //         contentDOM.querySelectorAll('x-sharp-file,x-sharp-image').forEach(element => {
    //             element.setAttribute('data-unique-id', this.newId());
    //         });
    //
    //         return contentDOM.body.innerHTML;
    //     });
    // }

    // serializeContent(content: string): string {
    //     if(this.editorField && !this.editorField.uploads) {
    //         return content;
    //     }
    //
    //     return this.maybeLocalized(content, content => {
    //         const contentDOM = new DOMParser().parseFromString(content, 'text/html');
    //
    //         contentDOM.querySelectorAll('x-sharp-file,x-sharp-image').forEach(element => {
    //             element.removeAttribute('data-unique-id');
    //             element.setAttribute('file', serializeUploadAttributeValue(parseAttributeValue(element.getAttribute('file'))));
    //         });
    //
    //         return contentDOM.body.innerHTML;
    //     });
    // }

    // async resolveContentUploads<Content extends MaybeLocalizedContent>(content: Content) {
    //     const { entityKey, instanceId } = this.root;
    //     const contentDOM = new DOMParser().parseFromString(this.allContent(content), 'text/html');
    //
    //     this.contentUploads = {
    //         ...this.contentUploads,
    //         ...Object.fromEntries(
    //             [...contentDOM.querySelectorAll('x-sharp-file,x-sharp-image')]
    //                 .map(element => ({
    //                     id: element.getAttribute('data-unique-id'),
    //                     resolved: Promise.withResolvers<true>(),
    //                     value: {
    //                         file: parseAttributeValue(element.getAttribute('file')),
    //                         legend: parseAttributeValue(element.getAttribute('legend')),
    //                     }
    //                 }))
    //                 .map(contentUpload => [contentUpload.id, contentUpload])
    //         ),
    //     }
    //
    //     this.onUploadsUpdated?.(this.serializedUploads);
    //
    //     const contentUploads = Object.values(this.contentUploads);
    //
    //     if(!contentUploads.length) {
    //         return;
    //     }
    //
    //     const resolvedFiles = await api.post(route('code16.sharp.api.files.show', { entityKey, instanceId }), {
    //         files: contentUploads.map(contentUpload => contentUpload.value.file).filter(Boolean)
    //     })
    //         .then(response => response.data.files) as FormUploadFieldValueData[]
    //
    //     resolvedFiles.forEach((resolvedFile, index) => {
    //         this.contentUploads[contentUploads[index].id].value = {
    //             ...this.contentUploads[contentUploads[index].id].value,
    //             file: resolvedFile,
    //         }
    //         this.contentUploads[contentUploads[index].id].resolved.resolve(true);
    //     });
    //
    //     this.onUploadsUpdated?.(this.serializedUploads);
    // }

    insertUpload() {
        const id = this.newId();
        this.contentUploads[id] = { id, value: { file: null, legend: null } };
        console.log('insertUpload', this);
        return id;
    }

    updateUpload(id: string, value: FormEditorUploadData) {
        this.contentUploads[id].value = { ...this.contentUploads[id].value, ...value };
        this.onUploadsUpdated(this.serializedUploads);
    }

    restoreUpload(id: string) {
        this.contentUploads[id] = {
            ...this.contentUploads[id],
            removed: false,
        };
        this.onUploadsUpdated(this.serializedUploads);
    }

    removeUpload(id: string) {
        this.contentUploads[id] = {
            ...this.contentUploads[id],
            removed: true,
        };
        this.onUploadsUpdated(this.serializedUploads);
    }

    getUpload(id: string): FormEditorUploadData {
        return this.contentUploads[id].value;
    }

    async postForm(id: string, data: FormEditorUploadData): Promise<FormEditorUploadData> {
        const { entityKey, instanceId } = this.root;

        const responseData = await api.post(
            route('code16.sharp.api.form.editor.upload.form.update', { entityKey, instanceId }), {
                data,
                fields: this.editorField.uploads.fields,
            }
        )
            .then(response => response.data);

        this.contentUploads[id] = {
            id,
            value: {
                ...responseData,
                file: {
                    ...responseData.file,
                    thumbnail: data.file.thumbnail,
                }
            },
        }

        this.onUploadsUpdated(this.serializedUploads);

        return responseData;
    }

}
