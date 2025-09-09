import { api } from "@/api/api";
import { route } from "@/utils/url";
import { Form } from "@/form/Form";
import { FormEditorFieldData } from "@/types";
import { Show } from "@/show/Show";
import { FormEditorUploadData } from "@/content/types";
import { reactive } from "vue";

type ContentUpload = {
    removed?: boolean,
    value: FormEditorUploadData,
}

export class ContentUploadManager<Root extends Form | Show> {
    root: Form | Show;
    editorField: Root extends Form ? FormEditorFieldData : null;
    state = reactive<{ contentUploads: { [id:string]: ContentUpload } }>({
        contentUploads: {}
    });

    constructor(
        root: Root,
        initialUploads: FormEditorFieldData['value']['uploads'] | undefined,
        config: {
            editorField: ContentUploadManager<Root>['editorField'],
        } = { editorField: null }
    ) {
        this.root = root;
        this.editorField = config.editorField;
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

    getUpload(id: string): FormEditorUploadData {
        return this.contentUploads[id]?.value;
    }

    newUpload(locale: string | null, value?: FormEditorUploadData) {
        const id = String(Object.keys(this.contentUploads).length);
        this.contentUploads[id] = {
            value: {
                ...value,
                _locale: locale,
            },
        };
        return id;
    }

    syncUploads(locale: string | null, ids: (string | number)[]) {
        this.contentUploads = Object.fromEntries(
            Object.entries(this.contentUploads)
                .map(([id, contentUpload]) => [
                    id,
                    ({
                        ...contentUpload,
                        removed: contentUpload.value?._locale == locale
                            ? !ids.some(i => String(i) === id)
                            : contentUpload.removed
                    })
                ])
        );
    }

    updateUpload(id: string, value: FormEditorUploadData) {
        this.contentUploads[id].value = { ...this.contentUploads[id].value, ...value };
    }

    async postForm(id: string | null, locale: string | null, data: FormEditorUploadData): Promise<{ id:string }> {
        const { entityKey, instanceId } = this.root;

        const responseData = await api.post(
            route('code16.sharp.api.form.editor.upload.form.update', { entityKey, instanceId }), {
                data,
                fields: this.editorField.uploads.fields,
            }
        )
            .then(response => response.data);

        id ??= this.newUpload(locale);

        this.contentUploads[id] = {
            value: responseData,
        }

        return { id };
    }
}
