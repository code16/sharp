import { reactive } from 'vue';
import debounce from 'lodash/debounce';
import {
    filesEquals,
    postResolveFiles,
    defaultFileThumbnailHeight,
    defaultFileThumbnailWidth,
} from "@sharp/files";
import { Upload } from "./upload";


export function getUploadExtension({
    fieldProps,
    uniqueIdentifier,
    fieldConfigIdentifier,
    form,
}) {

    const state = reactive({
        registeredFiles: [],
        created: false,
        resolved: null,
        onResolve: null,
    });

    state.resolved = new Promise(resolve => state.onResolve = resolve);

    const updateFiles = files => {
        this.$emit('input', {
            ...this.value,
            files,
        });
    }

    const resolveFiles = files => {
        return postResolveFiles({
            entityKey: form.entityKey,
            instanceId: form.instanceId,
            files,
            thumbnailWidth: defaultFileThumbnailWidth,
            thumbnailHeight: defaultFileThumbnailHeight,
        });
    }

    const config = {
        onBeforeCreate: () => {
            updateFiles([])
        },
        onCreate: debounce(async () => {
            if(state.registeredFiles.length > 0) {
                const files = await resolveFiles(state.registeredFiles);
                updateFiles(files);
                state.onResolve();
            }
            state.created = true;
            state.registeredFiles = [];
        }),
    }

    const options = {
        fieldProps: {
            ...fieldProps,
            uniqueIdentifier,
            fieldConfigIdentifier,
        },
        state,
        registerFile: async attrs => {
            if(state.created) {
                updateFiles([
                    ...this.value.files,
                    attrs,
                ]);
                return attrs;
            }

            state.registeredFiles.push(attrs);
            await state.resolved;
            return this.value.files.find(file => filesEquals(attrs, file));
        },
        onSuccess: uploadedFile => {
            updateFiles([
                ...this.value.files,
                uploadedFile,
            ]);
        },
        onRemove: removedFile => {
            updateFiles(
                this.value.files
                    .filter(file => !filesEquals(file, removedFile)),
            );
        },
        onUpdate: updatedFile => {
            updateFiles(
                this.value.files
                    .map(file => filesEquals(file, updatedFile) ? updatedFile : file),
            );
        }
    };

    return Upload
        .extend(config)
        .configure(options);
}
