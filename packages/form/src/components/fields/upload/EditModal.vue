<template>
    <Modal
        :visible="visible"
        :title="l('modals.cropper.title')"
        no-close-on-backdrop
        v-on="$listeners"
        @ok="handleOkClicked"
        @show="handleShow"
        ref="modal"
    >
        <template v-if="ready">
            <vue-cropper
                class="SharpUpload__modal-vue-cropper"
                :view-mode="0"
                drag-mode="crop"
                :aspect-ratio="ratioX / ratioY"
                :auto-crop-area="1"
                :zoomable="false"
                :guides="false"
                :background="true"
                :rotatable="true"
                :src="imageSrc"
                :data="cropData"
                :ready="handleCropperReady"
                alt="Source image"
                ref="cropper"
            />
        </template>
        <template v-else>
            <div class="d-flex align-items-center justify-content-center" style="height: 300px">
                <Loading />
            </div>
        </template>

        <div class="mt-3">
            <Button variant="primary" @click="handleRotateClicked(-90)">
                <i class="fas fa-undo"></i>
            </Button>
            <Button variant="primary" @click="handleRotateClicked(90)">
                <i class="fas fa-redo"></i>
            </Button>
        </div>
    </Modal>
</template>

<script>
    import VueCropper from 'vue-cropperjs';
    import { lang } from "sharp";
    import { Modal, Loading, Button } from 'sharp-ui';
    import { postResolveFiles } from 'sharp-files';

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
            transformOriginal: Boolean,
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
                    thumbnailWidth: 800,
                    thumbnailHeight: 600,
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
                if(this.transformOriginal && this.value?.path) {
                    await this.initOriginalThumbnail();
                }
                this.ready = true;
            },
            handleCropperReady() {
                if(this.cropData?.rotate) {
                    const cropper = this.$refs.cropper.cropper;
                    rotateTo(cropper, this.cropData.rotate);
                    cropper.setData(this.cropData);
                }
            },
        },
    }
</script>
