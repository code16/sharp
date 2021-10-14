import Vue from 'vue';
import { Upload } from "./upload";
import {
    filesEquals,
    postResolveFiles,
    defaultFileThumbnailHeight,
    defaultFileThumbnailWidth,
} from "sharp-files";


export function getUploadExtension({
    fieldProps,
    uniqueIdentifier,
    fieldConfigIdentifier,
    form,
}) {

    const state = Vue.observable({
        created: false,
        registeredFiles: [],
    });

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
        onCreate: async () => {
            const files = await resolveFiles(state.registeredFiles);
            updateFiles(files);
            state.created = true;
            state.registeredFiles = [];
        },
    }

    const options = {
        fieldProps: {
            ...fieldProps,
            uniqueIdentifier,
            fieldConfigIdentifier,
        },
        state,
        isReady: (attrs) => {
            if(state.registeredFiles.find(file => filesEquals(attrs, file))) {
                return false;
            }
            return state.created;
        },
        getFile: attrs => {
            return this.value.files.find(file => filesEquals(attrs, file));
        },
        registerFile: attrs => {
            state.registeredFiles.push(attrs);

            if(state.created) {
                options.restoreFile(attrs);
            }
        },
        restoreFile: async (attrs) => {
            const files = await resolveFiles([attrs]);
            updateFiles([
                ...this.value.files,
                files[0],
            ]);
            state.registeredFiles = state.registeredFiles.filter(file => !filesEquals(file, attrs));
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
