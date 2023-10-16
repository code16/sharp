<script setup lang="ts">
    import { FormUploadFieldData } from "@/types";
    import Uppy from '@uppy/core';
    import type { UppyFile } from "@uppy/core";
    import ThumbnailGenerator from '@uppy/thumbnail-generator';
    import XHRUpload from '@uppy/xhr-upload';
    import FileInput from '@uppy/vue/lib/file-input';
    import DropTarget from '@uppy/drop-target';
    import Cropper from 'cropperjs';
    import { computed, onUnmounted, ref, watch } from "vue";
    import { getErrorMessage, getXsrfToken, handleErrorAlert } from "@/api";
    import { getFiltersFromCropData } from "./util/filters";
    import { Button } from "@sharp/ui";
    import { ArrowDownOnSquareIcon } from "@heroicons/vue/24/outline";

    import { __ } from "@/utils/i18n";
    import { filesizeLabel } from "@/utils/file";
    import EditModal from "./EditModal.vue";
    import { useForm } from "../../../useForm";
    import UploadDropText from "./UploadDropText.vue";

    const props = defineProps<{
        field: FormUploadFieldData,
        fieldErrorKey: string,
        value: FormUploadFieldData['value'] & { file?: File },
        root: boolean,
        hasError: boolean,
    }>();

    defineOptions({
        inheritAttrs: false,
    });

    const emit = defineEmits(['input', 'error', 'success', 'clear', 'thumbnail', 'uploading', 'remove', 'update']);
    const form = useForm();
    const extension = computed(() => props.value?.name?.match(/\.[0-9a-z]+$/i)[0]);
    const showEditModal = ref(false);
    const isTransformable = computed(() => {
        const { field } = props;
        return field.transformable &&
            (!field.transformableFileTypes || field.transformableFileTypes?.includes(extension.value));
    });
    const transformedImg = ref();
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
            fieldName: 'file',
            headers: {
                'accept': 'application/json',
                'X-XSRF-TOKEN': getXsrfToken(),
            },
        })
        .on('file-added', (file) => {
            emit('clear');
            uppyFile.value = uppy.getFile(file.id);
            console.log('file-added', JSON.parse(JSON.stringify(uppyFile.value)));
        })
        .on('restriction-failed', (file, error) => {
            emit('error', error.message, file);
        })
        .on('thumbnail:generated', async (file, preview) => {
            const { field } = props;
            emit('thumbnail', preview);
            uppyFile.value = uppy.getFile(file.id);
            console.log('thumbnail:generated', JSON.parse(JSON.stringify(uppyFile.value)));

            if(isTransformable.value && field.ratioX && field.ratioY) {
                const cropper = await new Promise<Cropper>((resolve) => {
                    const container = document.createElement('div');
                    const image = document.createElement('img');
                    image.src = preview;
                    container.appendChild(image);
                    return new Cropper(image, {
                        aspectRatio: props.field.ratioY / props.field.ratioX,
                        autoCropArea: 1,
                        ready: (e) => {
                            resolve(e.currentTarget.cropper);
                        },
                    })
                });
                await onImageTransform(cropper);
                cropper.destroy();
            }
        })
        .on('upload', () => {
            emit('uploading', true);
        })
        .on('upload-progress', (file) => {
            uppyFile.value = uppy.getFile(file.id);
            console.log('upload-progress', JSON.parse(JSON.stringify(uppyFile.value)));
        })
        .on('upload-success', (file, response) => {
            emit('input', {
                ...response.body,
                uploaded: true,
            });
            emit('success', {
                ...response.body,
                size: file.size,
            });
            uppyFile.value = uppy.getFile(file.id);
            console.log('upload-success', JSON.parse(JSON.stringify(uppyFile.value)));
        })
        .on('upload-error', (file, error, response) => {
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
        })
        .on('complete', () => {
            emit('uploading', false);
        });

    if(props.value?.file) {
        uppy.addFile({
            name: props.value.file.name,
            type: props.value.file.type,
            data: props.value.file,
        });
        emit('input', {});
    }

    const isDraggingOver = ref(false);
    const dropTarget = ref<HTMLElement>();
    watch(dropTarget, () => {
        if(dropTarget.value) {
            uppy.use(DropTarget, {
                target: dropTarget.value,
                onDragOver: () => isDraggingOver.value = true,
                onDragLeave: () => isDraggingOver.value = false,
                onDrop: () => isDraggingOver.value = false,
            });
        } else {
            uppy.removePlugin(uppy.getPlugin('DropTarget'));
        }
    });

    onUnmounted(() => {
        uppy.close({ reason: 'unmount' });
        emit('uploading', false);
    });

    async function onImageTransform(cropper: Cropper) {
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

        const blob = await new Promise<Blob>(resolve => cropper.getCroppedCanvas().toBlob(resolve));
        transformedImg.value = URL.createObjectURL(blob);
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
        onImageTransform(cropper);
    }
</script>

<template>
    <template v-if="value || uppyFile">
        <div class="bg-white rounded border p-4">
            <div class="flex">
                <template v-if="value?.thumbnail ?? transformedImg ?? uppyFile?.preview">
                    <img class="mr-4"
                        width="100"
                        :src="value?.thumbnail ?? transformedImg ?? uppyFile.preview"
                        alt=""
                    >
                </template>
                <div>
                    <div class="text-sm font-medium truncate text-gray-800">
                        {{ value?.name?.split('/').at(-1) ?? uppyFile?.name }}
                    </div>
                    <div class="flex gap-2 mt-2">
                        <template v-if="value?.size ?? uppyFile?.size">
                            <div class="text-sm text-gray-600">
                                {{ filesizeLabel(value?.size ?? uppyFile.size) }}
                            </div>
                        </template>
                        <template v-if="value?.path">
                            <a class="text-sm text-primary-700 underline"
                                :href="route('code16.sharp.download.show', {
                                    entityKey: form.entityKey,
                                    instanceId: form.instanceId,
                                    disk: value.disk,
                                    path: value.path,
                                })"
                                :download="value?.name?.split('/').at(-1)"
                            >
                                {{ __('sharp::form.upload.download_link') }}
                            </a>
                        </template>
                    </div>
                    <template v-if="!field.readOnly">
                        <div class="flex gap-2 mt-2">
                            <template v-if="value && isTransformable && !hasError">
                                <Button class="mr-2" outline small @click="showEditModal = true">
                                    {{ __('sharp::form.upload.edit_button') }}
                                </Button>
                            </template>
                            <Button variant="danger" outline small @click="onRemove">
                                {{ __('sharp::form.upload.remove_button') }}
                            </Button>
                        </div>
                    </template>
                    <template v-if="uppyFile?.progress.percentage < 100 && !hasError">
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
        <div class="relative flex justify-center rounded-lg border border-dashed  px-6 py-10"
            :class="[isDraggingOver ? 'border-primary-600' : 'border-gray-900/25']"
            ref="dropTarget"
        >
            <div class="text-center" :class="{ 'invisible': isDraggingOver }">
                <div class="text-sm leading-6 text-gray-600">
                    <UploadDropText>
                        <template #link="{ text }">
                            <label class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                <span>{{ text }}</span>
                                <FileInput class="sr-only" :uppy="uppy" :props="{ pretty: false }" />
                            </label>
                        </template>
                    </UploadDropText>
                </div>
                <p class="text-xs leading-5 text-gray-600">
                    <template v-if="field.fileFilter?.length">
                        <span class="uppercase">
                            {{ field.fileFilter.map(extension => extension.replace('.', '')).join(', ') }}
                        </span>
                    </template>
                    <template v-if="field.maxFileSize">
                        {{ ' '+__('sharp::form.upload.help_text.max_file_size', { size: filesizeLabel(field.maxFileSize * 1024 * 1024) }) }}
                    </template>
                </p>
            </div>
            <template v-if="isDraggingOver">
                <div class="absolute inset-0 flex flex-col justify-center items-center pointer-events-none">
                    <ArrowDownOnSquareIcon class="w-8 h-8 text-gray-400 mb-1" />
                    <div class="text-sm leading-6 text-gray-600">
                        Drop your file here
                    </div>
                </div>
            </template>
        </div>
    </template>

    <EditModal
        v-model:visible="showEditModal"
        :value="value"
        :thumbnail="value?.thumbnail ?? uppyFile?.preview"
        :field="field"
        @submit="onTransformSubmit"
    />
</template>
