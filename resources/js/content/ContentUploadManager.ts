import { api } from "@/api";
import { route } from "@/utils/url";
import { Form } from "@/form/Form";
import { FormEditorFieldData } from "@/types";
import { Show } from "@/show/Show";
import { ContentManager } from "@/content/ContentManager";
import { FormEditorUploadData, MaybeLocalizedContent } from "@/content/types";
import { reactive } from "vue";

type ContentUpload = {
    removed?: boolean,
    value: FormEditorUploadData,
}

export class ContentUploadManager<Root extends Form | Show> extends ContentManager {
    root: Form | Show;
    onUploadsUpdated: Root extends Form ? (uploads: { [id:string]: FormEditorUploadData }) => any : null;
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
        this.uniqueId = initialUploads?.length ?? 0;
        this.editorField = config.editorField;
        this.onUploadsUpdated = config.onUploadsUpdated;
        this.contentUploads = Object.fromEntries(
            Object.entries(initialUploads ?? {}).map(([id, upload]) =>
                [id, { value:upload }]
            )
        );
    }

    get contentUploads() {
        return this.state.contentUploads;
    }

    set contentUploads(contentUploads: { [id:string]: ContentUpload }) {
        this.state.contentUploads = contentUploads;
    }

    get serializedUploads() {
        return Object.fromEntries(
            Object.entries(this.contentUploads)
                .filter(([id, contentUpload]) => !contentUpload.removed)
                .map(([id, contentUpload]) => [id, contentUpload.value])
        );
    }

    newUpload(nativeFile?: File) {
        const id = String(this.uniqueId++);
        this.contentUploads[id] = {
            value: {
                file: nativeFile ? { nativeFile } : null,
                legend: null,
            },
        };
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

    async postForm(id: string|null, data: FormEditorUploadData): Promise<string> {
        const { entityKey, instanceId } = this.root;

        const responseData = await api.post(
            route('code16.sharp.api.form.editor.upload.form.update', { entityKey, instanceId }), {
                data,
                fields: this.editorField.uploads.fields,
            }
        )
            .then(response => response.data);

        id ??= this.newUpload();

        this.contentUploads[id] = {
            value: {
                ...responseData,
                file: {
                    ...responseData.file,
                    thumbnail: data.file.thumbnail,
                }
            },
        }

        this.onUploadsUpdated(this.serializedUploads);

        return id;
    }

}
