<script setup lang="ts">
    import { FormUploadFieldData } from "@/types";
    import Uppy from '@uppy/core';
    import type { UppyFile } from "@uppy/core";
    import ThumbnailGenerator from '@uppy/thumbnail-generator';
    import XHRUpload from '@uppy/xhr-upload';
    import DropTarget from '@uppy/drop-target';
    import Cropper from 'cropperjs';
    import {
        computed, nextTick,
        onMounted,
        onUnmounted,
        ref, useTemplateRef,
        watch
    } from "vue";
    import { api, getErrorMessage, handleErrorAlert } from "@/api/api";
    import { getCropDataFromFilters, getFiltersFromCropData } from "./util/filters";
    import { Button } from '@/components/ui/button';
    import { route } from "@/utils/url";
    import { __ } from "@/utils/i18n";
    import { filesizeLabel } from "@/utils/file";
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
    import { MoreHorizontal, RotateCcw, RotateCw } from "lucide-vue-next";
    import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
    import FileIcon from "@/components/FileIcon.vue";
    import {
        Dialog,
        DialogDescription,
        DialogFooter,
        DialogHeader, DialogScrollContent,
        DialogTitle
    } from "@/components/ui/dialog";
    import { rotate, rotateTo } from "@/form/components/fields/upload/util/rotate";

    const props = defineProps<FormFieldProps<FormUploadFieldData> & { asEditorEmbed?: boolean, legend?: string, dropdownEditLabel?: string }>();

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
    const transformedImg = ref<string>();
    const uppyFile = ref<UppyFile>();
    const isEditable = computed(() => {
        return props.value && canTransform(props.value.name, props.value.mime_type) && !props.hasError
            || !!props.dropdownEditLabel;
    });
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
            emit('error', error.message, file.data);
        })
        .on('thumbnail:generated', async (file, preview) => {
            const { field } = props;
            emit('thumbnail', preview);
            uppyFile.value = uppy.getFile(file.id);
            console.log('thumbnail:generated', JSON.parse(JSON.stringify(uppyFile.value)));

            if(canTransform(file.name, file.type) && field.imageCropRatio) {
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

    function canTransform(fileName: string | null, mimeType: string | null) {
        const extension = fileName?.match(/\.[0-9a-z]+$/i)[0];
        return props.field.imageTransformable
            && (!props.field.imageTransformableFileTypes || props.field.imageTransformableFileTypes?.includes(extension))
            && mimeType?.startsWith('image/');
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

    const editModalOpen = ref(false);
    const editModalImageEl = useTemplateRef<HTMLImageElement>('editModalImageEl');
    const editModalImageUrl = ref<string>();
    const editModalCropper = ref<Cropper>();
    const editModalCropperData = ref<Partial<Cropper.Data>>();
    let onWindowResize;
    async function onEdit() {
        const event = new CustomEvent('edit', { cancelable: true });
        emit('edit', event);

        if(event.defaultPrevented) {
            return;
        }

        if(!editModalImageUrl.value) {
            if(props.value?.path) {
                const data = await api.post(route('code16.sharp.api.form.upload.thumbnail.show', {
                    entityKey: form.entityKey,
                    instanceId: form.instanceId,
                    path: props.value.path,
                    disk: props.value.disk,
                    width: 1200,
                    height: 1000,
                }))
                    .then(response => response.data) as { thumbnail: string|null };

                if(!data.thumbnail) {
                    return Promise.reject('Sharp Upload: original thumbnail not found in POST /api/files request');
                }

                editModalImageUrl.value = data.thumbnail;

                await new Promise<void>(resolve => {
                    const image = new Image();
                    image.src = data.thumbnail;
                    image.onload = () => {
                        editModalCropperData.value = getCropDataFromFilters({
                            filters: props.value.filters,
                            imageWidth: image.naturalWidth,
                            imageHeight: image.naturalHeight,
                        });
                        resolve();
                    }
                    image.onerror = () => {
                        editModalImageUrl.value = props.value.thumbnail;
                        resolve();
                    }
                });
            } else if(uppyFile.value) {
                editModalImageUrl.value = await new Promise(resolve => {
                    new Uppy()
                        .use(ThumbnailGenerator, { thumbnailWidth: 1200, thumbnailHeight: 1000 })
                        .on('thumbnail:generated', (file, preview) => {
                            resolve(preview);
                        })
                        .addFile({ ...uppyFile.value, preview: null });
                });
            }
        }

        editModalOpen.value = true;

        await nextTick();

        let currentCropData: Cropper.Data;
        editModalCropper.value = new Cropper(editModalImageEl.value, {
            viewMode: 2,
            dragMode: 'move',
            aspectRatio: props.field.imageCropRatio
                ? props.field.imageCropRatio[0] / props.field.imageCropRatio[1]
                : null,
            autoCropArea: 1,
            guides: false,
            background: true,
            rotatable: true,
            restore: false, // reset crop area on resize because it's buggy
            data: editModalCropperData.value,
            ready: () => {
                if(editModalCropperData.value?.rotate) {
                    rotateTo(editModalCropper.value, editModalCropperData.value.rotate);
                    editModalCropper.value.setData(editModalCropperData.value);
                }
                editModalCropper.value.setData(editModalCropperData.value);
                currentCropData = editModalCropper.value.getData();
            },
            cropend: () => {
                currentCropData = editModalCropper.value.getData();
            },
            cropmove: () => {
                currentCropData = editModalCropper.value.getData();
            },
            zoom: () => {
                currentCropData = editModalCropper.value.getData();
            }
        });

        window.removeEventListener('resize', onWindowResize);
        window.addEventListener('resize', onWindowResize = () => {
            if(editModalOpen.value) {
                setTimeout(() => {
                    editModalCropper.value.setData(currentCropData)
                });
            }
        });
    }

    onUnmounted(() => {
        window.removeEventListener('resize', onWindowResize);
    })

    function onEditModalSubmit() {
        editModalCropperData.value = editModalCropper.value.getData(true);
        editModalOpen.value = false;
        onImageTransform(editModalCropper.value);
    }

    function onRemove() {
        emit('input', null);
        emit('uploading', false);
        emit('remove');
        if(uppyFile.value) {
            uppy.removeFile(uppyFile.value.id);
            uppyFile.value = null;
            transformedImg.value = null;
            editModalImageUrl.value = null;
        }
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
</script>

<template>
    <FormFieldLayout v-bind="props">
        <template #default="{ id, ariaDescribedBy }">
            <template v-if="value?.path || value?.uploaded || uppyFile">
                <div :class="{ 'bg-background border border-input rounded-md p-4': !asEditorEmbed }">
                    <div class="flex gap-4">
                        <template v-if="transformedImg ?? value?.thumbnail ?? uppyFile?.preview">
                            <div class="self-center group/img relative flex flex-col justify-center rounded-md overflow-hidden">
                                <img class="rounded-md max-h-[150px] max-w-[150px]"
                                    :class="uppyFile && !transformedImg && field.imageCropRatio ? 'object-cover aspect-[--ratio]' : ''"
                                    :style="{
                                        '--ratio': field.imageCropRatio ? `${field.imageCropRatio[0]} / ${field.imageCropRatio[1]}` : null
                                    }"
                                    :src="transformedImg ?? value?.thumbnail ?? uppyFile.preview"
                                    alt=""
                                >
                                <template v-if="isEditable">
                                    <button class="absolute flex justify-center items-center gap-2 inset-0 bg-black/50 transition text-white text-xs font-medium opacity-0 group-hover/img:opacity-100" tabindex="-1" @click="onEdit">
                                        {{ __('sharp::form.upload.edit_button') }}
                                    </button>
                                </template>
                            </div>
                        </template>
                        <template v-else>
                            <FileIcon class="self-center size-4" :mime-type="value?.mime_type || uppyFile?.type" />
                        </template>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium truncate">
                                <template v-if="value?.path">
                                    <TooltipProvider>
                                        <Tooltip :delay-duration="0" disable-hoverable-content>
                                            <TooltipTrigger as-child>
                                                <a class="text-foreground underline underline-offset-4 decoration-foreground/20 hover:decoration-foreground"
                                                    :href="route('code16.sharp.download.show', {
                                                        entityKey: form.entityKey,
                                                        instanceId: form.instanceId,
                                                        disk: value.disk,
                                                        path: value.path,
                                                    })"
                                                    :download="value?.name"
                                                >
                                                    {{ value?.name }}
                                                </a>
                                            </TooltipTrigger>

                                            <TooltipContent class="pointer-events-none" :side-offset="10">
                                                {{ __('sharp::form.upload.download_tooltip') }}
                                            </TooltipContent>
                                        </Tooltip>
                                    </TooltipProvider>
                                </template>
                                <template v-else>
                                    {{ value?.name ?? uppyFile?.name }}
                                </template>
                            </div>
                            <template v-if="value?.size ?? uppyFile?.size">
                                <div class="mt-2 text-xs text-muted-foreground">
                                    {{ filesizeLabel(value?.size ?? uppyFile.size) }}
                                </div>
                            </template>
                            <template v-if="legend">
                                <div class="mt-2 text-xs">{{ legend }}</div>
                            </template>
                            <template v-if="uppyFile?.progress.percentage < 100 && !hasError">
                                <div class="mt-2">
                                    <div class="bg-primary h-0.5 transition-all" :style="{ width: `${uppyFile.progress.percentage}%` }" role="progressbar">
                                    </div>
                                </div>
                            </template>
                        </div>
                        <DropdownMenu :modal="false">
                            <DropdownMenuTrigger as-child>
                                <Button class="shrink-0 self-center" variant="ghost" size="icon">
                                    <MoreHorizontal class="w-4 h-4" />
                                </Button>
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
                                <template v-if="isEditable">
                                    <DropdownMenuItem @click="onEdit">
                                        {{ props.dropdownEditLabel ?? __('sharp::form.upload.edit_button') }}
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
                    :class="isDraggingOver ? 'border-primary' : ''"
                    type="file"
                    :accept="field.allowedExtensions?.join(',')"
                    :aria-describedby="ariaDescribedBy"
                    @change="onInputChange"
                    @dragover="isDraggingOver = true"
                    @dragleave="isDraggingOver = false"
                    @drop="isDraggingOver = false"
                />
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

    <Dialog v-model:open="editModalOpen">
        <DialogScrollContent class="flex flex-col max-w-6xl min-h-[500px] h-[calc(100vh-4rem)] transition-none">
            <DialogHeader>
                <DialogTitle>
                    {{ __('sharp::form.upload.edit_modal.title') }}
                </DialogTitle>
                <DialogDescription>
                    {{ __('sharp::form.upload.edit_modal.description') }}
                </DialogDescription>
            </DialogHeader>
            <div class="flex-1 min-h-0">
                <img :src="editModalImageUrl" alt="" ref="editModalImageEl">
            </div>
            <DialogFooter class="flex-col">
                <div class="flex gap-2 mr-auto">
                    <Button variant="secondary" @click="rotate(editModalCropper, -90)">
                        <RotateCcw class="size-4" />
                    </Button>
                    <Button variant="secondary" @click="rotate(editModalCropper, 90)">
                        <RotateCw class="size-4" />
                    </Button>
                </div>
                <Button @click="onEditModalSubmit">
                    {{ __('sharp::form.upload.edit_modal.ok_button') }}
                </Button>
            </DialogFooter>
        </DialogScrollContent>
    </Dialog>
</template>
