<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { ref, watch } from "vue";
    import { getCropDataFromFilters } from "./util/filters";
    import { api } from "@/api";
    import { FormUploadFieldData } from "@/types";
    import Cropper from "cropperjs";
    import { rotate, rotateTo } from "./util/rotate";
    import { Modal, Loading, Button } from '@/components/ui';
    import { useParentForm } from "../../../useParentForm";
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

    watch(cropperImg, () => {
        // console.log(cropperImg.value);
        if(cropperImg.value) {
            cropper.value = new Cropper(cropperImg.value, {
                viewMode: 2,
                dragMode: 'move',
                aspectRatio: props.field.ratioX / props.field.ratioY,
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
            // console.log(cropper.value);
        } else {
            cropper.value.destroy();
        }
    });

    watch(() => props.value, () => {
        cropperData.value = null;
        originalImg.value = null;
    });

    async function loadOriginalImg() {
        if(originalImg.value) {
            return;
        }
        const files = await api.post(route('code16.sharp.api.files.show', {
            entityKey: form.entityKey,
            instanceId: form.instanceId,
        }), {
            files: [
                {
                    path: props.value.path,
                    disk: props.value.disk,
                }
            ],
            thumbnail_width: 1200,
            thumbnail_height: 1000,
        })
            .then(response => response.data.files);

        originalImg.value = files[0]?.thumbnail;

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
        console.log('onShow');
        if(props.value?.path) {
            await loadOriginalImg();
        }
        ready.value = true;
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
                    <Button text @click="onRotate(-90)">
                        <ArrowUturnLeftIcon class="w-4 h-4" />
                    </Button>
                    <Button text @click="onRotate(90)">
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
