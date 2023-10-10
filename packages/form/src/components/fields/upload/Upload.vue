<script setup lang="ts">
    import { FormUploadFieldData } from "@/types";
    import Uppy from '@uppy/core';
    import type { UppyFile } from "@uppy/core";
    import DragDrop from '@uppy/drag-drop';
    import ThumbnailGenerator from '@uppy/thumbnail-generator';
    import XHRUpload from '@uppy/xhr-upload';
    import Cropper from 'cropperjs';
    import { computed, onUnmounted, ref, watch } from "vue";
    import { getErrorMessage, getXsrfToken, handleErrorAlert } from "@/api";
    import { getFiltersFromCropData } from "./util/filters";
    import { Button } from "@sharp/ui";

    import '@uppy/core/dist/style.min.css';
    import '@uppy/drag-drop/dist/style.min.css';
    import { __ } from "@/utils/i18n";
    import { filesizeLabel } from "@/utils/file";

    const props = defineProps<{
        field: FormUploadFieldData,
        fieldErrorKey: string,
        value: FormUploadFieldData['value'] & { file?: File },
        root: boolean,
        hasError: boolean,
        entityKey: string,
        instanceId?: string | number,
    }>();

    const emit = defineEmits(['input', 'error', 'success', 'clear', 'thumbnail', 'uploading', 'remove', 'update']);
    const extension = computed(() => props.value?.name?.match(/\.[0-9a-z]+$/i)[0]);
    const isTransformable = computed(() => {
        const { field } = props;
        return field.transformable &&
            (!field.transformableFileTypes || field.transformableFileTypes?.includes(extension.value));
    });
    const transformedImg = ref();
    const dropTarget = ref<HTMLElement>();
    const uppyFile = ref<UppyFile>();
    const uppy = new Uppy({
        id: props.fieldErrorKey,
        restrictions: {
            maxFileSize: props.field.maxFileSize,
            maxNumberOfFiles: 1,
            allowedFileTypes: props.field.fileFilter
        },
        locale: {
            strings: {
                exceedsSize: __('sharp::form.upload.message.file_too_big', {
                    size: filesizeLabel((props.field.maxFileSize ?? 0) * 1024 * 1024),
                }),
                youCanOnlyUploadFileTypes: __('sharp::form.upload.message.bad_extension'),
            },
        },
        autoProceed: true,
    })
        .use(ThumbnailGenerator, { thumbnailWidth: 300, thumbnailHeight: 300 })
        .use(XHRUpload, {
            endpoint: route('code16.sharp.api.form.upload'),
            headers: {
                'accept': 'application/json',
                'X-XSRF-TOKEN': getXsrfToken(),
            },
        });

    if(props.value?.file) {
        uppy.addFile({
            name: props.value.file.name,
            type: props.value.file.type,
            data: props.value.file,
        });
        emit('input', {});
    }

    watch(dropTarget, () => {
        if(dropTarget.value) {
            uppy.use(DragDrop, {
                target: dropTarget.value,
            });
        } else {
            uppy.removePlugin(uppy.getPlugin('DragDrop'));
        }
    });

    onUnmounted(() => {
        uppy.close({ reason: 'unmount' });
        emit('uploading', false);
    });

    function transformImage(cropper: Cropper) {
        const cropData = cropper.getData(true);
        const imageData = cropper.getImageData();
        const value = {
            ...props.value,
            transformed: true,
            filters: {
                ...props.value?.filters,
                ...getFiltersFromCropData({
                    cropData,
                    imageWidth: imageData.naturalWidth,
                    imageHeight: imageData.naturalHeight,
                }),
            }
        }
        emit('update', value);
        emit('input', value);

        cropper.getCroppedCanvas().toBlob(blob => {
            transformedImg.value = URL.createObjectURL(blob);
        });
    }

    function onRemove() {
        emit('input', null);
        emit('uploading', false);
        emit('remove');
        if(uppyFile.value) {
            uppy.removeFile(uppyFile.value.id);
            uppyFile.value = null;
        }
    }

    function onTransformSubmit(cropper: Cropper) {
        transformImage(cropper);
    }

    uppy.on('file-added', (file) => {
        emit('clear');
        uppyFile.value = file;
    });

    uppy.on('restriction-failed', (file, error) => {
        emit('error', error.message, file);
    });

    uppy.on('thumbnail:generated', async (file, preview) => {
        const { field } = props;
        emit('thumbnail', preview);
        uppyFile.value = file;
        if(isTransformable.value && field.ratioX && field.ratioY) {
            const cropper = await new Promise<Cropper>((resolve) => {
                const image = document.createElement('img');
                image.src = preview;
                return new Cropper(image, {
                    aspectRatio: props.field.ratioY / props.field.ratioX,
                    autoCropArea: 1,
                    ready: (e) => {
                        resolve(e.currentTarget.cropper);
                    },
                })
            });
            transformImage(cropper);
        }
    });

    uppy.on('upload', () => {
        emit('uploading', true);
    });

    uppy.on('upload-success', (file, response) => {
        emit('input', {
            ...response.body,
            uploaded: true,
        });
        emit('success', {
            ...response.body,
            size: file.size,
        });
        uppyFile.value = file;
    });

    uppy.on('upload-error', (file, error, response) => {
        if(response) {
            if(response.status === 422) {
                emit('error', response.body.errors.file?.join(', '), file);
            } else {
                const message = getErrorMessage({ data: response.body, status: response.status });
                handleErrorAlert({ data: response.body, status: response.status, method: 'post' });
                emit('error', message, file);
            }
        } else {
            emit('error', error.message, file);
        }
    });

    uppy.on('complete', () => {
        emit('uploading', false);
    });
</script>

<template>
    <template v-if="value || uppyFile">
        <div class="bg-white rounded border p-4">
            <div class="flex">
                <template v-if="value?.thumbnail ?? transformedImg ?? uppyFile.preview">
                    <img class="mr-4"
                        width="100"
                        :src="value?.thumbnail ?? transformedImg ?? uppyFile.preview"
                        alt=""
                    >
                </template>
                <div>
                    <div class="text-sm font-medium truncate text-gray-800">
                        {{ value?.name?.split('/').at(-1) ?? uppyFile.name }}
                    </div>
                    <div class="flex gap-2 mt-2">
                        <template v-if="value?.size ?? uppyFile.size">
                            <div class="text-sm text-gray-600">
                                {{ filesizeLabel(value?.size ?? uppyFile.size) }}
                            </div>
                        </template>
                        <template v-if="value?.path">
                            <a class="text-sm text-primary-700 underline"
                                :href="route('code16.sharp.api.download.show', { instanceId, entityKey, disk: value.disk, path: value.path })"
                            >
                                {{ __('sharp::form.upload.download_link') }}
                            </a>
                        </template>
                    </div>
                    <template v-if="!field.readOnly">
                        <div class="flex gap-2 mt-2">
                            <template v-if="value && isTransformable && !hasError">
                                <Button class="mr-2" outline small>
                                    {{ __('sharp::form.upload.edit_button') }}
                                </Button>
                            </template>
                            <Button variant="danger" outline small @click="onRemove">
                                {{ __('sharp::form.upload.remove_button') }}
                            </Button>
                        </div>
                    </template>
                    <template v-if="uppyFile.progress.percentage < 100 && !hasError">
                        <div class="mt-2">
                            <div class="bg-primary h-0.5 transition-all" :style="{ width: `${uppyFile.progress.percentage}%` }" role="progressbar">
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
    <template v-else>
        <div ref="dropTarget">
        </div>
    </template>
</template>
