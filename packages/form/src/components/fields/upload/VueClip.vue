<template>
    <div class="SharpUpload" :class="classes">
        <div> <!-- keep content div to allow dropzone events (vue-clip) -->
            <template v-if="file">
                <div class="card card-body SharpUpload__card" :class="{ 'border-danger': hasError }">
                    <div :class="{ 'row': showThumbnail }">
                        <template v-if="showThumbnail">
                            <div class="SharpUpload__thumbnail" :class="[compactThumbnail?'col-4 col-sm-3 col-xl-2':'col-4 col-md-4']">
                                <img :src="imageSrc" alt="">
                            </div>
                        </template>

                        <div class="SharpUpload__infos" :class="{[compactThumbnail?'col-8 col-sm-9 col-xl-10':'col-8 col-md-8']:showThumbnail}">
                            <div class="mb-3">
                                <label class="SharpUpload__filename text-truncate d-block">{{ fileName }}</label>
                                <div class="SharpUpload__info mt-2">
                                    <div class="row g-2">
                                        <template v-if="size">
                                            <div class="col-auto">{{ size }}</div>
                                        </template>
                                        <template v-if="hasDownload">
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
                                        <Button outline small @click="handleEditButtonClick">
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
                <Button class="SharpUpload__browse dz-message" text block :disabled="readOnly" type="button" @click="handleClick">
                    {{ l('form.upload.browse_button') }}
                </Button>
            </template>
            <div ref="clip-preview-template" class="clip-preview-template" style="display: none;">
                <div></div>
            </div>
        </div>

        <EditModal
            :value="value"
            :visible.sync="showEditModal"
            :src="originalImageSrc"
            :transform-original="transformKeepOriginal"
            :ratio-x="ratioX"
            :ratio-y="ratioY"
            @submit="handleEditSubmitted"
            ref="modal"
        />

        <template v-if="hasInitialTransform">
            <vue-cropper
                class="d-none"
                :src="originalImageSrc"
                :aspect-ratio="ratioX/ratioY"
                :auto-crop-area="1"
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

    import { filesizeLabel, getErrorMessage, handleErrorAlert, logError } from 'sharp';
    import { Localization } from 'sharp/mixins';
    import { Button } from 'sharp-ui';
    import { downloadFileUrl } from "sharp-files";
    import { getFiltersFromCropData } from "./util/filters";
    import { getImageBlobUrl } from "./util/image";
    import EditModal from "./EditModal";

    export default {
        name: 'SharpVueClip',

        extends: VueClip,

        components: {
            EditModal,
            VueCropper,
            Button,
        },

        inject: {
            $form: {
                default: null,
            },
        },

        mixins: [ Localization ],

        props: {
            ratioX: Number,
            ratioY: Number,
            value: Object,
            transformable: {
                type: Boolean,
                default: true,
            },
            transformableFileTypes: Array,
            transformKeepOriginal: Boolean,

            readOnly: Boolean,
            root: Boolean,
            compactThumbnail: Boolean,
            focused: Boolean,
            invalid: Boolean,
            uniqueIdentifier: String,
            fieldConfigIdentifier: String,
            persistThumbnails: Boolean,
        },

        data() {
            return {
                showEditModal: false,
                transformedImg: null,
            }
        },
        watch: {
            value: 'init',
            'file.status'(status) {
                (status in this.statusFunction) && this[this.statusFunction[status]]();
            },
        },
        computed: {
            classes() {
                return {
                    'SharpUpload--empty': !this.file,
                    'SharpUpload--disabled': this.readOnly,
                    'SharpUpload--compacted': this.compactThumbnail,
                    'SharpUpload--focused': this.focused,
                }
            },
            file() {
                return this.files[0];
            },
            originalImageSrc() {
                return this.file?.thumbnail || this.file?.blobUrl;
            },
            imageSrc() {
                return this.transformedImg || this.originalImageSrc;
            },
            size() {
                return this.file.size != null
                    ? filesizeLabel(this.file.size)
                    : null;
            },
            operationFinished() {
                return {
                    transform: this.hasInitialTransform ? !!this.transformedImg : null
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
                return this.file?.status === 'error' || this.invalid;
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
                    entityKey: this.$form?.entityKey,
                    instanceId: this.$form?.instanceId,
                    disk: this.value.disk,
                    path: this.value.path,
                });
            },
            showThumbnail() {
                return !!this.imageSrc;
            },
            isTransformable() {
                if(!this.transformable || !this.originalImageSrc) {
                    return false;
                }
                if(this.file?.type && !this.file.type.match(/^image\//)) {
                    return false;
                }
                return !this.transformableFileTypes || this.transformableFileTypes.includes(this.fileExtension);
            },
            hasInitialTransform() {
                if(this.file?.status === 'exist' && !this.value.uploaded) {
                    return false;
                }
                return this.isTransformable && !!this.ratioX && !!this.ratioY;
            },
            hasEdit() {
                return this.isTransformable && !this.inProgress;
            },
            hasDownload() {
                return this.file?.status === 'exist' && !!this.value.path;
            },
        },
        methods: {
            setPending(value) {
                this.$form?.setUploading(this.uniqueIdentifier, value);
            },
            // status callbacks
            onStatusAdded() {
                const htmlFile = this.file._file;

                this.$emit('add', htmlFile);
                this.setPending(true);

                getImageBlobUrl(htmlFile)
                    .then(blobUrl => {
                        if(blobUrl) {
                            this.$set(this.file, 'blobUrl', blobUrl);
                            this.$emit('thumbnail', blobUrl);
                        }
                    })
                    .catch(e => {
                        console.error(e);
                    });
            },
            async onStatusError() {
                const xhr = this.file.xhrResponse;
                const msg = this.file.errorMessage;
                this.setPending(false);
                await this.$nextTick();
                if(!xhr?.statusCode) {
                    this.$emit('error', msg, this.file._file);
                } else {
                   this.handleUploadError(xhr);
                }
            },
            handleUploadError(xhr) {
                const data = JSON.parse(xhr.responseText);
                const status = xhr.statusCode;
                if(status === 422) {
                    const message = Object.values(data.errors ?? {}).join(', ');
                    this.$emit('error', message);
                } else {
                    const message = getErrorMessage({ data, status });
                    handleErrorAlert({ data, status });
                    this.$emit('error', message);
                }
            },
            onStatusSuccess() {
                let data = {};
                try {
                    data = JSON.parse(this.file.xhrResponse.responseText);
                }
                catch(e) { console.log(e); }

                const value = {
                    ...this.value,
                    ...data,
                    uploaded: true,
                }
                this.$emit('success', {
                    ...value,
                    size: this.file.size,
                });
                this.$emit('input', value);

                this.setPending(false);
            },

            // actions
            remove(emits = true) {
                this.removeFile(this.file);
                this.files.splice(0, 1);

                this.setPending(false);

                this.resetThumbnails();
                this.transformedImg = null;

                if(emits) {
                    this.$emit('input', null);
                    this.$emit('reset');
                }
            },

            handleEditButtonClick() {
                this.showEditModal = true;
            },

            handleRemoveClicked() {
                this.remove();
                this.$emit('removed');
            },

            handleClick() {
                const dropzone = this.uploader._uploader;
                dropzone.hiddenFileInput.click();
            },

            handleDrop(e) {
                if(this.file && e.dataTransfer?.files?.length > 0) {
                    this.remove(false);
                }
            },

            handleEditSubmitted(cropper) {
                this.updateTransformedImage(cropper);
                this.updateFilters(cropper);
            },

            onCropperReady() {
                if(this.hasInitialTransform) {
                    this.updateTransformedImage(this.$refs.cropper);
                    this.updateFilters(this.$refs.cropper);
                }
            },

            updateFilters(cropper) {
                const cropData = cropper.getData(true);
                const imageData = cropper.getImageData();

                const value = {
                    ...this.value,
                    transformed: true,
                    filters: {
                        ...this.value?.filters,
                        ...getFiltersFromCropData({
                            cropData,
                            imageWidth: imageData.naturalWidth,
                            imageHeight: imageData.naturalHeight,
                        }),
                    }
                }

                this.$emit('input', value);
                this.$emit('updated', value);
            },

            updateTransformedImage(cropper) {
                this.resetTransformedImage();
                cropper.getCroppedCanvas({ width: 300, height: 300 }).toBlob(blob => {
                    this.transformedImg = URL.createObjectURL(blob);
                });
            },

            resetTransformedImage() {
                if(this.transformedImg) {
                    URL.revokeObjectURL(this.transformedImg);
                }
            },

            resetThumbnails() {
                if(this.file?.blobUrl && !this.persistThumbnails) {
                    URL.revokeObjectURL(this.file.blobUrl)
                }
                this.resetTransformedImage();
            },

            validateValue() {
                if(!this.value.name) {
                    return logError(`Upload field '${this.downloadId}' has an invalid value: expects to have a "name", given :`, JSON.parse(JSON.stringify(this.value)));
                }
                return true;
            },

            init() {
                if(this.value) {
                    if(this.file) {
                        Object.assign(this.file, this.value);
                    } else {
                        this.addedFile({ ...this.value, upload: {} });
                    }
                    this.$set(this.file, 'thumbnail', this.value.thumbnail ?? null);
                } else {
                    this.files = [];
                }
            },
        },
        created() {
            this.options.thumbnailWidth = null;
            this.options.thumbnailHeight = null;
            this.options.maxFiles = 1;

            if(!this.value || this.value.file) {
                return;
            }

            if(!this.validateValue()) {
                return;
            }

            this.init();
            this.file.status = 'exist';
        },
        mounted() {
            const dropzone = this.uploader._uploader;
            dropzone.disable();
            dropzone.listeners = dropzone.listeners
                .filter(listener => !listener.events.click);
            dropzone.enable();

            dropzone.on('drop', this.handleDrop);
            dropzone.on('thumbnail', this.onThumbnail);

            if(this.value?.file) {
                dropzone.addFile(this.value.file);
                this.$emit('input', {});
            }
        },
        beforeDestroy() {
            this.resetThumbnails();
            this.setPending(false);
            this.uploader._uploader.destroy();
        },
    }
</script>
