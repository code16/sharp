<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { nextTick, ref, watch } from "vue";
    import { getCropDataFromFilters } from "./util/filters";
    import { api } from "@/api/api";
    import { FormUploadFieldData } from "@/types";
    import Cropper from "cropperjs";
    import { rotate, rotateTo } from "./util/rotate";
    import { Button } from '@/components/ui/button';
    import { Modal, Loading } from '@/components/ui';
    import { useParentForm } from "@/form/useParentForm";
    import { ArrowUturnRightIcon } from "@heroicons/vue/20/solid";
    import { ArrowUturnLeftIcon } from "@heroicons/vue/20/solid";
    import { route } from "@/utils/url";

    const props = defineProps<{
        field: FormUploadFieldData,
        value: FormUploadFieldData['value'],
        thumbnail?: string,
    }>();

    const emit = defineEmits(['submit']);
    const ready = ref(false);
    const originalImg = ref();
    const cropper = ref<Cropper>();
    const cropperData = ref<Partial<Cropper.Data>>();
    const cropperImg = ref();
    const form = useParentForm();

    watch(() => props.value, () => {
        cropperData.value = null;
        originalImg.value = null;
    });

    async function loadOriginalImg() {
        if(originalImg.value) {
            return;
        }
        const data = await api.post(route('code16.sharp.api.form.upload.thumbnail.show', {
            entityKey: form.entityKey,
            instanceId: form.instanceId,
            path: props.value.path,
            disk: props.value.disk,
            width: 1200,
            height: 1000,
        }))
            .then(response => response.data) as { thumbnail: string|null };

        originalImg.value = data.thumbnail;

        if(!originalImg.value) {
            return Promise.reject('Sharp Upload: original thumbnail not found in POST /api/files request');
        }

        await new Promise<void>(resolve => {
            const image = new Image();
            image.src =  originalImg.value;
            image.onload = () => {
                cropperData.value = getCropDataFromFilters({
                    filters: props.value.filters,
                    imageWidth: image.naturalWidth,
                    imageHeight: image.naturalHeight,
                });
                resolve();
            }
            image.onerror = () => {
                originalImg.value = null;
                resolve();
            }
        });
    }

    function onRotate(degree) {
        rotate(cropper.value, degree);
    }

    async function onShow() {
        ready.value = false;
        // if the value does not have 'uploaded: true' we now it exists so we can fetch a larger thumbnail
        if(props.value && !props.value.uploaded) {
            await loadOriginalImg();
        }
        ready.value = true;
        await nextTick();
        cropper.value = new Cropper(cropperImg.value, {
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
            data: cropperData.value,
            ready: () => {
                if(cropperData.value?.rotate) {
                    rotateTo(cropper.value, cropperData.value.rotate);
                    cropper.value.setData(cropperData.value);
                }
            },
        });
    }

    function onHidden() {
        cropper.value.destroy();
    }

    function onOk() {
        cropperData.value = cropper.value.getData(true);
        emit('submit', cropper.value);
    }
</script>

<template>
    <Modal
        :title="__('sharp::modals.cropper.title')"
        no-close-on-backdrop
        full-height
        max-width="4xl"
        @ok="onOk"
        @show="onShow"
        @hidden="onHidden"
    >
        <template v-if="ready">
            <div class="h-full">
                <img :src="originalImg ?? thumbnail" alt="" ref="cropperImg">
            </div>
        </template>
        <template v-else>
            <div class="flex align-items-center justify-content-center" style="height: 300px">
                <Loading />
            </div>
        </template>

        <template v-slot:footer-prepend>
            <div class="flex gap-4">
                <div class="flex gap-4">
                    <Button variant="outline" @click="onRotate(-90)">
                        <ArrowUturnLeftIcon class="w-4 h-4" />
                    </Button>
                    <Button variant="outline" @click="onRotate(90)">
                        <ArrowUturnRightIcon class="w-4 h-4" />
                    </Button>
                </div>
                <div class="text-gray-600 text-sm">
                    {{ __('sharp::form.upload.edit_modal.description') }}
                </div>
            </div>
        </template>
    </Modal>
</template>
