<template>
    <div class="SharpUpload" :class="[{'SharpUpload--empty':!file, 'SharpUpload--disabled':readOnly}, modifiersClasses]">
        <template v-if="file">
            <div class="card card-body" :class="{ 'border-danger': hasError }">
                <div class="SharpUpload__container" :class="{ 'row': showThumbnail }">
                    <template v-if="showThumbnail">
                        <div class="SharpUpload__thumbnail" :class="[modifiers.compacted?'col-4 col-sm-3 col-xl-2':'col-4 col-md-4']">
                            <img :src="imageSrc" @load="handleImageLoaded">
                        </div>
                    </template>

                    <div class="SharpUpload__infos" :class="{[modifiers.compacted?'col-8 col-sm-9 col-xl-10':'col-8 col-md-8']:showThumbnail}">
                        <div class="mb-3">
                            <label class="SharpUpload__filename text-truncate d-block">{{ fileName }}</label>
                            <div class="SharpUpload__info mt-2">
                                <div class="row g-2">
                                    <template v-if="size">
                                        <div class="col-auto">{{ size }}</div>
                                    </template>
                                    <template v-if="canDownload">
                                        <div class="col-auto">
                                            <a class="SharpUpload__download-link" :href="downloadUrl" :download="fileName">
                                                <i class="fas fa-download"></i>
                                                {{ l('form.upload.download_link') }}
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <transition name="SharpUpload__progress">
                                <template v-if="inProgress">
                                    <div class="SharpUpload__progress mt-2">
                                        <div class="SharpUpload__progress-bar" role="progressbar" :style="{width:`${progress}%`}"
                                            :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </template>
                            </transition>
                        </div>
                        <template v-if="!readOnly">
                            <div>
                                <template v-if="hasEdit && !hasError">
                                    <Button outline small @click="onEditButtonClick">
                                        {{ l('form.upload.edit_button') }}
                                    </Button>
                                </template>
                                <Button class="SharpUpload__remove-button" variant="danger" outline small @click="handleRemoveClicked">
                                    {{ l('form.upload.remove_button') }}
                                </Button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
        <template v-else>
            <Button class="dz-message" text block :disabled="readOnly" type="button" @click="handleClick">
                {{ l('form.upload.browse_button') }}
            </Button>
        </template>
        <div ref="clip-preview-template" class="clip-preview-template" style="display: none;">
            <div></div>
        </div>

        <Modal :visible.sync="showEditModal"
            :title="l('modals.cropper.title')"
            no-close-on-backdrop
            @ok="onEditModalOk"
            @hidden="onEditModalHidden"
            ref="modal"
        >
            <vue-cropper
                class="SharpUpload__modal-vue-cropper"
                :view-mode="2"
                drag-mode="crop"
                :aspect-ratio="ratioX/ratioY"
                :auto-crop-area="1"
                :zoomable="false"
                :guides="false"
                :background="true"
                :rotatable="true"
                :src="originalImageSrc"
                :data="cropData"
                alt="Source image"
                ref="modalCropper"
            />
            <div class="mt-3">
                <Button @click="rotate(-90)"><i class="fas fa-undo"></i></Button>
                <Button @click="rotate(90)"><i class="fas fa-redo"></i></Button>
            </div>
        </Modal>

        <template v-if="hasInitialCrop">
            <vue-cropper
                class="d-none"
                :aspect-ratio="ratioX/ratioY"
                :auto-crop-area="1"
                :src="originalImageSrc"
                :ready="onCropperReady"
                ref="cropper"
            />
        </template>

        <a style="display: none" ref="dlLink"></a>
    </div>
</template>

<script>
    import VueClip from 'vue-clip/src/components/Clip';
    import VueCropper from 'vue-cropperjs';

    import { Modal, Button } from 'sharp-ui';
    import { Localization } from 'sharp/mixins';
    import { filesizeLabel } from 'sharp';

    import { VueClipModifiers } from './modifiers';
    import rotateResize from './rotate';
    import { downloadFileUrl } from "../../../api";

    export default {
        name: 'SharpVueClip',

        extends: VueClip,

        components: {
            Modal,
            VueCropper,
            Button,
        },

        inject : [ '$form' ],

        mixins: [ Localization, VueClipModifiers ],

        props: {
            downloadId: String,
            pendingKey: String,
            ratioX: Number,
            ratioY: Number,
            value: Object,
            croppable: {
                type: Boolean,
                default: true,
            },
            croppableFileTypes: Array,

            readOnly: Boolean,
            root: Boolean,
        },

        data() {
            return {
                showEditModal: false,
                croppedImg: null,
                allowCrop: false,
                cropData: null,

                isNew: !this.value,
                canDownload: !!this.value,
            }
        },
        watch: {
            value(value) {
                if(!value) {
                    this.files = [];
                }
            },
            'file.status'(status) {
                (status in this.statusFunction) && this[this.statusFunction[status]]();
            },
        },
        computed: {
            file() {
                return this.files[0];
            },
            originalImageSrc() {
                return this.file?.thumbnail || this.file?.dataUrl;
            },
            imageSrc() {
                return this.croppedImg || this.originalImageSrc;
            },
            size() {
                return this.file.size != null
                    ? filesizeLabel(this.file.size)
                    : null;
            },
            operationFinished() {
                return {
                    crop: this.hasInitialCrop ? !!this.croppedImg : null
                }
            },
            operations() {
                return Object.keys(this.operationFinished);
            },
            activeOperationsCount() {
                return this.operations.filter(op => this.operationFinished[op] !== null).length;
            },
            operationFinishedCount() {
                return this.operations.filter(op => this.operationFinished[op]).length;
            },
            progress() {
                let curProgress = this.file ? this.file.progress : 100;

                let delta = this.activeOperationsCount - this.operationFinishedCount;
                let factor = (1-delta*.05);

                return Math.floor(curProgress) * factor;
            },
            inProgress() {
                if(this.file?.status === 'exist' || this.hasError) {
                    return false;
                }
                return this.progress < 100;
            },
            statusFunction() {
                return {
                    error:'onStatusError', success:'onStatusSuccess', added:'onStatusAdded'
                }
            },
            hasError() {
                return this.file?.status === 'error';
            },
            fileName() {
                let splitted = this.file.name.split('/');
                return splitted.length ? splitted[splitted.length-1] : '';
            },
            fileExtension() {
                let extension = this.fileName.split('.').pop();
                return extension ? `.${extension}` : null;
            },
            downloadUrl() {
                return downloadFileUrl({
                    entityKey: this.$form.entityKey,
                    instanceId: this.$form.instanceId,
                    fieldKey: this.downloadId,
                    fileName: this.fileName,
                });
            },
            showThumbnail() {
                return this.imageSrc;
            },
            isCroppable() {
                if(!this.croppable || !this.originalImageSrc) {
                    return false;
                }
                if(this.file?.type && !this.file.type.match(/^image\//)) {
                    return false;
                }
                return !this.croppableFileTypes || this.croppableFileTypes.includes(this.fileExtension);
            },
            hasInitialCrop() {
                return this.isCroppable && !!this.ratioX && !!this.ratioY;
            },
            hasEdit() {
                return this.isCroppable && !this.inProgress;
            },
        },
        methods: {
            setPending(value) {
                this.$form?.setUploading(this.pendingKey, value);
            },
            // status callbacks
            onStatusAdded() {
                this.$emit('reset');

                this.setPending(true);
            },
            async onStatusError() {
                const msg = this.file.errorMessage;
                await this.$nextTick();
                this.$emit('error', msg);
            },
            onStatusSuccess() {
                let data = {};
                try {
                    data = JSON.parse(this.file.xhrResponse.responseText);
                }
                catch(e) { console.log(e); }

                data.uploaded = true;
                this.$emit('success', data);
                this.$emit('input',data);

                this.setPending(false);

                this.allowCrop = true;
                this.$nextTick(() => {
                    this.isCropperReady() && this.onCropperReady();
                });
            },

            // actions
            remove() {
                this.canDownload = false;
                this.removeFile(this.file);
                this.files.splice(0, 1);

                this.setPending(false);

                this.resetEdit();

                this.$emit('input', null);
                this.$emit('reset');
            },

            resetEdit() {
                this.croppedImg = null;
                this.cropData = null;
            },

            onEditButtonClick() {
                this.$emit('active');
                this.showEditModal = true;
                this.allowCrop = true;
            },

            handleImageLoaded() {
                if(this.isNew) {
                    this.$emit('image-updated');
                }
            },

            handleRemoveClicked() {
                this.remove();
                this.$emit('removed');
            },

            handleClick() {
                const dropzone = this.uploader._uploader;
                dropzone.hiddenFileInput.click();
            },

            onEditModalHidden() {
                this.$emit('inactive');
            },

            onEditModalOk() {
                this.updateCroppedImage(this.$refs.modalCropper);
                this.updateCropData(this.$refs.modalCropper);
            },

            isCropperReady() {
                return this.$refs.cropper && this.$refs.cropper.cropper.ready;
            },

            onCropperReady() {
                if(this.hasInitialCrop) {
                    this.updateCroppedImage(this.$refs.cropper);
                    this.updateCropData(this.$refs.cropper);
                }
            },

            updateCropData(cropper) {
                let cropData = cropper.getData(true);
                let imgData = cropper.getImageData();

                let rw=imgData.naturalWidth, rh=imgData.naturalHeight;

                if(Math.abs(cropData.rotate)%180) {
                    rw = imgData.naturalHeight;
                    rh = imgData.naturalWidth;
                }

                let relativeData = {
                    width: cropData.width / rw,
                    height: cropData.height / rh,
                    x: cropData.x / rw,
                    y: cropData.y / rh,
                    rotate: cropData.rotate * -1 // counterclockwise
                };

                this.cropData = { ...cropData };

                if(this.allowCrop) {
                    let data = {
                        ...this.value,
                        cropData: relativeData,
                    };
                    this.$emit('input', data);
                    this.$emit('updated', data);
                }
            },

            updateCroppedImage(cropper) {
                if(this.allowCrop) {
                    this.isNew = true;
                    this.croppedImg = cropper.getCroppedCanvas().toDataURL();
                }
            },

            rotate(degree) {
                rotateResize(this.$refs.modalCropper.cropper, degree);
            },
        },
        created() {
            this.options.thumbnailWidth = null;
            this.options.thumbnailHeight = null;
            this.options.maxFiles = 1;

            if (!this.value || this.value.file)
                return;

            this.addedFile({ ...this.value, upload: {} });
            this.file.thumbnail = this.value.thumbnail;
            this.file.status = 'exist';
        },
        mounted() {
            const dropzone = this.uploader._uploader;
            dropzone.disable();
            dropzone.listeners = dropzone.listeners
                .filter(listener => !listener.events.click);
            dropzone.enable();

            if(this.value?.file) {
                dropzone.addFile(this.value.file);
                this.$emit('input', {});
            }
        },
        beforeDestroy() {
            this.setPending(false);
            this.uploader._uploader.destroy();
        },
    }
</script>
