<template>
    <Modal
        :visible="visible"
        :title="l('modals.cropper.title')"
        no-close-on-backdrop
        v-on="$listeners"
        @ok="handleOkClicked"
        @show="handleShow"
        dialog-class="modal-dialog-scrollable"
        content-class="h-100"
        size="xl"
        ref="modal"
    >
        <template v-if="ready">
            <vue-cropper
                class="SharpUpload__modal-vue-cropper h-100"
                v-bind="cropperOptions"
                :src="imageSrc"
                alt="Source image"
                ref="cropper"
            />
        </template>
        <template v-else>
            <div class="d-flex align-items-center justify-content-center" style="height: 300px">
                <Loading />
            </div>
        </template>

        <template v-slot:footer-prepend>
            <div class="row align-items-center">
                <div class="col-auto">
                    <Button text @click="handleRotateClicked(-90)">
                        <i class="fas fa-undo"></i>
                    </Button>
                    <Button class="me-auto" text @click="handleRotateClicked(90)">
                        <i class="fas fa-redo"></i>
                    </Button>
                </div>
                <div class="col d-none d-lg-block">
                    <div class="text-muted fs-7 lh-sm">
                        {{ l('form.upload.edit_modal.description') }}
                    </div>
                </div>
            </div>
        </template>
    </Modal>
</template>

<script>
    import VueCropper from 'vue-cropperjs';
    import { lang } from "sharp";
    import { Modal, Loading, Button } from '@sharp/ui';
    import { postResolveFiles } from '@sharp/files';

    import { rotate, rotateTo } from "./util/rotate";
    import { getCropDataFromFilters } from "./util/filters";

    export default {
        components: {
            Modal,
            Loading,
            VueCropper,
            Button,
        },
        inject: {
            $form: {
                default: null,
            },
        },
        props: {
            value: Object,
            visible: Boolean,
            src: String,
            ratioX: Number,
            ratioY: Number,
        },
        data() {
            return {
                ready: false,
                cropData: null,
                originalImg: null,
            }
        },
        watch: {
            'value': 'handleValueChanged',
        },
        computed: {
            imageSrc() {
                return this.originalImg || this.src;
            },
            /**
             * @returns {import('cropperjs/types/index').Cropper.Options}
             */
            cropperOptions() {
                return {
                    viewMode: 2,
                    dragMode: 'move',
                    aspectRatio: this.ratioX / this.ratioY,
                    autoCropArea: 1,
                    guides: false,
                    background: true,
                    rotatable: true,
                    restore: false, // reset crop area on resize because it's buggy
                    data: this.cropData,
                    ready: this.handleCropperReady,
                }
            },
        },
        methods: {
            l: lang,
            handleRotateClicked(degree) {
                rotate(this.$refs.cropper.cropper, degree);
            },
            handleValueChanged() {
                if(!this.value) {
                    this.cropData = null;
                    this.originalImg = null;
                }
            },
            handleOkClicked() {
                const cropper = this.$refs.cropper;
                const cropData = cropper.getData(true);

                this.cropData = cropData;
                this.$emit('submit', cropper);
            },
            handleShow() {
                this.init();
            },
            async initOriginalThumbnail() {
                if(this.originalImg) {
                    return;
                }
                const files = await postResolveFiles({
                    entityKey: this.$form.entityKey,
                    instanceId: this.$form.instanceId,
                    files: [
                        {
                            path: this.value.path,
                            disk: this.value.disk,
                        }
                    ],
                    thumbnailWidth: 1200,
                    thumbnailHeight: 1000,
                });

                this.originalImg = files[0]?.thumbnail;

                if(!this.originalImg) {
                    return Promise.reject('Sharp Upload: original thumbnail not found in POST /api/files request');
                }

                await new Promise(resolve => {
                    const image = new Image();
                    image.src = this.originalImg;
                    image.onload = () => {
                        this.cropData = getCropDataFromFilters({
                            filters: this.value.filters,
                            imageWidth: image.naturalWidth,
                            imageHeight: image.naturalHeight,
                        });
                        resolve();
                    }
                    image.onerror = () => {
                        this.originalImg = null;
                        resolve();
                    }
                });
            },
            async init() {
                this.ready = false;
                if(this.value?.path) {
                    await this.initOriginalThumbnail();
                }
                this.ready = true;
            },
            handleCropperReady() {
                /**
                 * @type import('cropperjs/types/index').Cropper
                 */
                const cropper = this.$refs.cropper.cropper;
                if(this.cropData?.rotate) {
                    rotateTo(cropper, this.cropData.rotate);
                    cropper.setData(this.cropData);
                }
            },
        },
    }
</script>
