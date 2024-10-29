<script setup lang="ts">
    import { FormUploadFieldData } from "@/types";
    import Uppy from '@uppy/core';
    import type { UppyFile } from "@uppy/core";
    import ThumbnailGenerator from '@uppy/thumbnail-generator';
    import XHRUpload from '@uppy/xhr-upload';
    import DropTarget from '@uppy/drop-target';
    import Cropper from 'cropperjs';
    import {
        computed,
        onMounted,
        onUnmounted,
        ref,
        watch
    } from "vue";
    import { getErrorMessage, handleErrorAlert } from "@/api/api";
    import { getFiltersFromCropData } from "./util/filters";
    import { Button } from '@/components/ui/button';
    import { route } from "@/utils/url";

    import { __ } from "@/utils/i18n";
    import { filesizeLabel } from "@/utils/file";
    import EditModal from "./EditModal.vue";
    import { useParentForm } from "@/form/useParentForm";
    import { getCsrfToken } from "@/utils/request";

    import { FormFieldProps } from "@/form/types";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import { Input } from "@/components/ui/input";
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem, DropdownMenuSeparator,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import { MoreHorizontal } from "lucide-vue-next";

    const props = defineProps<FormFieldProps<FormUploadFieldData> & { asEditorEmbed?: boolean }>();

    defineOptions({
        inheritAttrs: false,
    });

    const emit = defineEmits<{
        (e: 'input', value: typeof props['value']): void
        (e: 'error', message: string, file: Blob | File): void
        (e: 'success', file: typeof props['value']): void
        (e: 'clear'): void
        (e: 'thumbnail', preview: string): void
        (e: 'uploading', uploading: boolean): void
        (e: 'remove'): void
        (e: 'transform', value: typeof props['value']): void
        (e: 'edit', event: CustomEvent): void
    }>();
    const form = useParentForm();
    const extension = computed(() => props.value?.name?.match(/\.[0-9a-z]+$/i)[0]);
    const showEditModal = ref(false);
    const isTransformable = computed(() => {
        return props.field.imageTransformable
            && (!props.field.imageTransformableFileTypes || props.field.imageTransformableFileTypes?.includes(extension.value))
            && props.value?.mime_type?.startsWith('image/');
    });
    const transformedImg = ref<string>();
    const uppyFile = ref<UppyFile>();
    const uppy = new Uppy({
        id: props.fieldErrorKey,
        restrictions: {
            maxFileSize: (props.field.maxFileSize ?? 0) * 1024 * 1024,
            maxNumberOfFiles: 1,
            allowedFileTypes: props.field.allowedExtensions
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
        meta: {
            'validation_rule[]': props.field.validationRule,
        },
    })
        .use(ThumbnailGenerator, { thumbnailWidth: 300, thumbnailHeight: 300 })
        .use(XHRUpload, {
            endpoint: route('code16.sharp.api.form.upload'),
            fieldName: 'file',
            headers: {
                'accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
        })
        .on('file-added', (file) => {
            emit('clear');
            uppyFile.value = uppy.getFile(file.id);
            console.log('file-added', JSON.parse(JSON.stringify(uppyFile.value)));
        })
        .on('restriction-failed', (file, error) => {
            emit('error', `${error.message} (${file.name})`, file.data);
        })
        .on('thumbnail:generated', async (file, preview) => {
            const { field } = props;
            emit('thumbnail', preview);
            uppyFile.value = uppy.getFile(file.id);
            console.log('thumbnail:generated', JSON.parse(JSON.stringify(uppyFile.value)));

            if(isTransformable.value && field.imageCropRatio) {
                const cropper = await new Promise<Cropper>((resolve) => {
                    const container = document.createElement('div');
                    const image = document.createElement('img');
                    image.src = preview;
                    container.appendChild(image);
                    return new Cropper(image, {
                        aspectRatio: field.imageCropRatio[0] / field.imageCropRatio[1],
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
        })
        .on('upload-success', (file, response) => {
            emit('input', {
                ...response.body,
                thumbnail: transformedImg?.value ?? uppyFile.value.preview,
                mime_type: file.type,
                size: file.size,
            });
            emit('success', {
                ...response.body,
                thumbnail: transformedImg?.value ?? uppyFile.value.preview,
                mime_type: file.type,
                size: file.size,
            });
            uppyFile.value = uppy.getFile(file.id);
            console.log('upload-success', JSON.parse(JSON.stringify(uppyFile.value)));
        })
        .on('upload-error', (file, error, response) => {
            if(response) {
                if(response.status === 422) {
                    emit('error', response.body.errors.file?.join(', '), file.data);
                } else {
                    const message = getErrorMessage({ data: response.body, status: response.status });
                    handleErrorAlert({ data: response.body, status: response.status, method: 'post' });
                    emit('error', message, file.data);
                }
            } else {
                emit('error', error.message, file.data);
            }
        })
        .on('complete', () => {
            emit('uploading', false);
        });

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

    async function onImageTransform(cropper: Cropper) {
        const cropData = cropper.getData(true);
        const imageData = cropper.getImageData();
        const blob = await new Promise<Blob>(resolve => cropper.getCroppedCanvas().toBlob(resolve));

        if(transformedImg.value) {
            URL.revokeObjectURL(transformedImg.value);
        }

        transformedImg.value = URL.createObjectURL(blob);

        const value: typeof props['value'] = {
            ...props.value,
            transformed: true,
            thumbnail: transformedImg.value,
            filters: {
                ...props.value?.filters,
                ...getFiltersFromCropData({
                    cropData,
                    imageWidth: imageData.naturalWidth,
                    imageHeight: imageData.naturalHeight,
                }),
            }
        };

        emit('transform', value);
        emit('input', value);
    }

    function onEdit() {
        const event = new CustomEvent('edit', { cancelable: true });
        emit('edit', event);

        if(!event.defaultPrevented) {
            showEditModal.value = true;
        }
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

    function onInputChange(e: Event) {
        const target = e.target as HTMLInputElement;
        if(target.files?.length) {
            uppy.addFile({
                name: target.files[0].name,
                type: target.files[0].type,
                data: target.files[0],
            });
            target.value = null;
        }
    }

    onMounted(() => {
        if(props.value?.nativeFile && !props.value?.uploaded) {
            uppy.addFile({
                name: props.value.nativeFile.name,
                type: props.value.nativeFile.type,
                data: props.value.nativeFile,
            });
            emit('input', null);
        }
    });

    onUnmounted(() => {
        uppy.close({ reason: 'unmount' });
        uppy.emit('cancel-all', { reason: 'user' });
        emit('uploading', false);
    });

    const menuOpened = ref(false);
</script>

<template>
    <FormFieldLayout v-bind="props">
        <template #default="{ id, ariaDescribedBy }">
            <template v-if="value?.path || value?.uploaded || uppyFile">
                <div :class="{ 'bg-background border border-input rounded-md p-4': !asEditorEmbed }">
                    <div class="flex">
                        <template v-if="transformedImg ?? value?.thumbnail  ?? uppyFile?.preview">
                            <img class="mr-4 object-contain"
                                width="150"
                                :src="transformedImg ?? value?.thumbnail ?? uppyFile.preview"
                                alt=""
                            >
                        </template>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium truncate">
                                <template v-if="value?.path">
                                    <a class="hover:underline underline-offset-4"
                                        :href="route('code16.sharp.download.show', {
                                            entityKey: form.entityKey,
                                            instanceId: form.instanceId,
                                            disk: value.disk,
                                            path: value.path,
                                        })"
                                        :download="value?.name?.split('/').at(-1)">
                                        {{ value?.name?.split('/').at(-1) }}
                                    </a>
                                </template>
                                <template v-else>
                                    {{ value?.name?.split('/').at(-1) ?? uppyFile?.name }}
                                </template>
                            </div>
                            <div class="flex gap-2 mt-2">
                                <template v-if="value?.size ?? uppyFile?.size">
                                    <div class="text-xs text-muted-foreground">
                                        {{ filesizeLabel(value?.size ?? uppyFile.size) }}
                                    </div>
                                </template>
                            </div>
<!--                        <template v-if="!field.readOnly">-->
<!--                            <div class="flex gap-2 mt-2">-->
<!--                                <template v-if="value && (!uppyFile || !uppyFile.progress.uploadStarted || uppyFile.progress.uploadComplete) && isTransformable && !hasError">-->
<!--                                    <Button class="h-8" variant="outline" size="sm" @click="onEdit">-->
<!--                                        {{ __('sharp::form.upload.edit_button') }}-->
<!--                                    </Button>-->
<!--                                </template>-->
<!--                                <Button class="h-8" variant="outline" size="sm" @click="onRemove">-->
<!--                                    {{ __('sharp::form.upload.remove_button') }}-->
<!--                                </Button>-->
<!--                                <template v-if="value?.path">-->
<!--                                    <Button-->
<!--                                        class="h-8 underline -ml-2"-->
<!--                                        size="sm"-->
<!--                                        variant="link"-->
<!--                                        :href="route('code16.sharp.download.show', {-->
<!--                                            entityKey: form.entityKey,-->
<!--                                            instanceId: form.instanceId,-->
<!--                                            disk: value.disk,-->
<!--                                            path: value.path,-->
<!--                                        })"-->
<!--                                        :download="value?.name?.split('/').at(-1)"-->
<!--                                    >-->
<!--                                        {{ __('sharp::form.upload.download_link') }}-->
<!--                                    </Button>-->
<!--                                </template>-->
<!--                            </div>-->
<!--                        </template>-->
                            <template v-if="uppyFile?.progress.percentage < 100 && !hasError">
                                <div class="mt-2">
                                    <div class="bg-primary h-0.5 transition-all" :style="{ width: `${uppyFile.progress.percentage}%` }" role="progressbar">
                                    </div>
                                </div>
                            </template>
                        </div>
                        <DropdownMenu :modal="false">
                            <DropdownMenuTrigger as-child>
                                <Button class="self-center" variant="ghost" size="icon">
                                    <MoreHorizontal class="w-4 h-4" />
                                </Button>
<!--                            <Button class="self-center" size="sm" variant="outline">-->
<!--                                {{ __('sharp::form.upload.edit_button') }}-->
<!--                            </Button>-->
                            </DropdownMenuTrigger>
                            <DropdownMenuContent>
                                <template v-if="value?.path">
                                    <DropdownMenuItem
                                        as="a"
                                        :download="value.name ?? ''"
                                        :href="route('code16.sharp.download.show', {
                                            entityKey: form.entityKey,
                                            instanceId: form.instanceId,
                                            disk: value.disk,
                                            path: value.path,
                                        })"
                                    >
                                        {{ __('sharp::form.upload.download_link') }}
                                    </DropdownMenuItem>
                                </template>
                                <template v-if="value && (!uppyFile || !uppyFile.progress.uploadStarted || uppyFile.progress.uploadComplete) && isTransformable && !hasError">
                                    <DropdownMenuItem @click="onEdit">
                                        {{ __('sharp::form.upload.edit_button') }}
                                    </DropdownMenuItem>
                                </template>
                                <DropdownMenuSeparator class="first:hidden" />
                                <DropdownMenuItem class="text-destructive" @click="onRemove">
                                    {{ __('sharp::form.upload.remove_button') }}
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>
            </template>
            <template v-else>
                <Input
                    :id="id"
                    type="file"
                    :accept="field.allowedExtensions?.join(',')"
                    :aria-describedby="ariaDescribedBy"
                    @change="onInputChange"
                />

<!--            <FileInput :uppy="uppy" :props="{ pretty: false }" ref="inputContainer" />-->
<!--            <div class="relative flex justify-center rounded-lg border border-dashed px-6 py-10"-->
<!--                :class="[-->
<!--                    isDraggingOver ? 'border-primary-600' :-->
<!--                    hasError ? 'border-red-600' :-->
<!--                    'border-gray-900/25'-->
<!--                ]"-->
<!--                ref="dropTarget"-->
<!--            >-->
<!--                <div class="text-center" :class="{ 'invisible': isDraggingOver }">-->
<!--                    <div class="text-sm leading-6 text-gray-600">-->
<!--                        <UploadDropText>-->
<!--                            <template #link="{ text }">-->
<!--                                <label class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">-->
<!--                                    <span>{{ text }}</span>-->
<!--                                    <FileInput class="sr-only" :uppy="uppy" :props="{ pretty: false }" ref="inputContainer" />-->
<!--                                </label>-->
<!--                            </template>-->
<!--                        </UploadDropText>-->
<!--                    </div>-->
<!--                    <p class="text-xs leading-5 text-gray-600">-->
<!--                        <template v-if="field.allowedExtensions?.length">-->
<!--                            <span class="uppercase">-->
<!--                                {{ field.allowedExtensions.map(extension => extension.replace('.', '')).join(', ') }}-->
<!--                            </span>-->
<!--                        </template>-->
<!--                        <template v-if="field.maxFileSize">-->
<!--                            {{ ' '+__('sharp::form.upload.help_text.max_file_size', { size: filesizeLabel(field.maxFileSize * 1024 * 1024) }) }}-->
<!--                        </template>-->
<!--                    </p>-->
<!--                </div>-->
<!--                <template v-if="isDraggingOver">-->
<!--                    <div class="absolute inset-0 flex flex-col justify-center items-center pointer-events-none">-->
<!--                        <ArrowDownOnSquareIcon class="w-8 h-8 text-gray-400 mb-1" />-->
<!--                        <div class="text-sm leading-6 text-gray-600">-->
<!--                            Drop your file here-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </template>-->
<!--            </div>-->
            </template>
        </template>

        <template v-if="!(value?.path || value?.uploaded || uppyFile)" #help-message>
            <template v-if="field.allowedExtensions?.length">
                <span class="">
                    {{ field.allowedExtensions.join(', ') }}
                </span>
            </template>
            <template v-if="field.maxFileSize">
                {{ ' '+__('sharp::form.upload.help_text.max_file_size', { size: filesizeLabel(field.maxFileSize * 1024 * 1024) }) }}
            </template>
        </template>
    </FormFieldLayout>

    <EditModal
        v-model:visible="showEditModal"
        :value="value"
        :thumbnail="value?.thumbnail ?? uppyFile?.preview"
        :field="field"
        @submit="onTransformSubmit"
    />
</template>

<style>
    .uppy-DragDrop-container {
        @apply flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10;
    }
</style>
