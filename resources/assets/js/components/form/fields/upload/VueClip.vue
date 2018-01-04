<template>
    <div class="SharpUpload" :class="[{'SharpUpload--empty':!file, 'SharpUpload--disabled':readOnly}, modifiersClasses]">
        <div class="SharpUpload__inner">
            <div class="SharpUpload__content">
                <form v-show="!file" class="dropzone">
                    <button type="button" class="dz-message SharpButton SharpButton--secondary SharpUpload__upload-button" :disabled="readOnly">
                        {{ l('form.upload.browse_button') }}
                    </button>
                </form>
                <template v-if="file">
                    <div class="SharpUpload__container" :class="{ row:showThumbnail }">
                        <div v-if="showThumbnail" class="SharpUpload__thumbnail" :class="[modifiers.compacted?'col-4 col-sm-3 col-xl-2':'col-4 col-md-4']">
                            <img :src="imageSrc" @load="handleImageLoaded">
                        </div>
                        <div class="SharpUpload__infos" :class="{[modifiers.compacted?'col-8 col-sm-9 col-xl-10':'col-8 col-md-8']:showThumbnail}">
                            <div class="mb-3 text-truncate">
                                <label class="SharpUpload__filename">{{ fileName }}</label>
                                <div class="SharpUpload__info mt-2">
                                    <span v-show="size" class="mr-2">{{ size }}</span>
                                    <a v-show="canDownload" class="SharpUpload__download-link" @click.prevent="download" href="">
                                        {{ l('form.upload.download_link') }}
                                    </a>
                                </div>
                                <transition name="SharpUpload__progress">
                                    <div class="SharpUpload__progress mt-2" v-show="inProgress">
                                        <div class="SharpUpload__progress-bar" role="progressbar" :style="{width:`${progress}%`}"
                                             :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </transition>
                            </div>
                            <div v-show="!readOnly">
                                    <button v-show="!!originalImageSrc && !inProgress" type="button" class="SharpButton SharpButton--sm SharpButton--secondary" @click="onEditButtonClick">
                                        {{ l('form.upload.edit_button') }}
                                    </button>
                                <button type="button" class="SharpButton SharpButton--sm SharpButton--secondary SharpButton--danger SharpUpload__remove-button"
                                        @click="remove()" :disabled="readOnly">
                                    {{ l('form.upload.remove_button') }}
                                </button>
                            </div>
                        </div>
                        <button class="SharpUpload__close-button" type="button" @click="remove()" v-show="!readOnly">
                            <svg class="SharpUpload__close-icon"
                                 aria-label="close" width="10" height="10" viewBox="0 0 10 10" fill-rule="evenodd">
                                <path d="M9.8 8.6L8.4 10 5 6.4 1.4 10 0 8.6 3.6 5 .1 1.4 1.5 0 5 3.6 8.6 0 10 1.4 6.4 5z"></path>
                            </svg>
                        </button>
                    </div>
                </template>
                <div ref="clip-preview-template" class="clip-preview-template" style="display: none;">
                    <div></div>
                </div>
            </div>
        </div>
        <template v-if="!!originalImageSrc">
            <sharp-modal v-model="showEditModal" @ok="onEditModalOk" @shown="onEditModalShown" @hidden="onEditModalHidden" no-close-on-backdrop
                         :title="l('modals.cropper.title')" ref="modal">
                <vue-cropper ref="cropper"
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
                             :ready="onCropperReady"
                             alt="Source image">
                </vue-cropper>
                <div>
                    <button class="SharpButton SharpButton--primary" @click="rotate(-90)"><i class="fa fa-rotate-left"></i></button>
                    <button class="SharpButton SharpButton--primary" @click="rotate(90)"><i class="fa fa-rotate-right"></i></button>
                </div>
            </sharp-modal>
        </template>
        <a style="display: none" ref="dlLink"></a>
    </div>
</template>

<script>
    import VueClip from '../../../vendor/vue-clip/components/Clip/index';
    import File from '../../../vendor/vue-clip/File';
    import Modal from '../../../Modal';
    import VueCropper from 'vue-cropperjs';
    import rotateResize from './rotate';

    import { Localization } from '../../../../mixins';
    import { VueClipModifiers } from './modifiers';

    import axios from 'axios';

    export default {
        name: 'SharpVueClip',

        extends: VueClip,

        components: {
            [Modal.name]: Modal,
            VueCropper
        },

        inject : [ 'actionsBus', 'axiosInstance' ,'$form', '$field' ],

        mixins: [ Localization, VueClipModifiers ],

        props: {
            downloadId: String,
            pendingKey: String,
            ratioX: Number,
            ratioY: Number,
            value: Object,

            readOnly: Boolean
        },

        data() {
            return {
                showEditModal: false,
                croppedImg: null,
                resized: false,
                croppable: false,

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
                return this.file && (this.file.thumbnail || this.file.dataUrl);
            },
            imageSrc() {
                return this.croppedImg || this.originalImageSrc;
            },
            size() {
                if(this.file.size == null) {
                    return '';
                }
                let size = (parseFloat((this.file.size).toFixed(2))/1024)/1024;
                let res = '';
                if(size<0.1) { res+='<'; size=0.1 }
                res += size.toLocaleString();
                return `${res} MB`;
            },
            hasCrop() {
                return !!(this.ratioX && this.ratioY);
            },
            operationFinished() {
                return {
                    crop: this.hasCrop ? !!this.croppedImg : null
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
                return (this.file && this.file.status !== 'exist') && this.progress < 100;
            },
            statusFunction() {
                return {
                    error:'onStatusError', success:'onStatusSuccess', added:'onStatusAdded'
                }
            },
            fileName() {
                let splitted = this.file.name.split('/');
                return splitted.length ? splitted[splitted.length-1] : '';
            },
            downloadLink() {
                return `${this.$form.downloadLinkBase}/${this.downloadId}`;
            },
            showThumbnail() {
                return this.imageSrc;
            }
        },
        methods: {
            setPending(value) {
                this.actionsBus.$emit('setPendingJob', {
                    key: this.pendingKey,
                    origin: 'upload',
                    value
                });
            },
            // status callbacks
            onStatusAdded() {
                this.$emit('reset');

                this.setPending(true);
            },
            onStatusError() {
                let msg = this.file.errorMessage;
                this.remove();
                this.$emit('error', msg);

                this.setPending(false)
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

                this.croppable = true;
                this.$nextTick(_=>{
                    this.isCropperReady() && this.onCropperReady();
                });
            },

            async download() {
                if(!this.value.uploaded) {
                    let { data } = await this.axiosInstance.post(this.downloadLink, { fileName: this.value.name }, { responseType: 'blob' });
                    //console.log(data);
                    let $link = this.$refs.dlLink;
                    $link.href = URL.createObjectURL(data);
                    $link.download = this.fileName;
                    $link.click();
                }
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
                this.$emit('removed');
            },

            resetEdit() {
                this.croppedImg = null;
                this.resized = false;
            },

            onEditButtonClick() {
                this.$emit('active');
                this.showEditModal = true;
                this.croppable = true;
            },

            handleImageLoaded() {
                if(this.isNew) {
                    this.$emit('image-updated');
                }
            },

            onEditModalShown() {
                if(!this.resized) {
                    this.$nextTick(()=>{
                        let cropper = this.$refs.cropper.cropper;

                        cropper.resize();
                        cropper.reset();
                        this.resized=true;
                    });
                }
            },

            onEditModalHidden() {
                this.$emit('inactive');
                setTimeout(()=>this.$refs.cropper.cropper.reset(), 300);
            },

            onEditModalOk() {
                this.updateCroppedImage();
                this.updateCropData();
            },

            isCropperReady() {
                return this.$refs.cropper && this.$refs.cropper.cropper.ready;
            },

            onCropperReady() {
                if(this.ratioX && this.ratioY) {
                    this.updateCroppedImage();
                    this.updateCropData();
                }
            },

            updateCropData() {
                let cropData = this.getCropData();
                let imgData = this.getImageData();

                let rw=imgData.naturalWidth, rh=imgData.naturalHeight;

                if(Math.abs(cropData.rotate)%180) {
                    rw = imgData.naturalHeight;
                    rh = imgData.naturalWidth;
                }
                //console.log('img', rw, rh, imgData.naturalWidth, imgData.naturalHeight);
                //console.log('crop', cropData.width, cropData.height);
                let relativeData = {
                    width: cropData.width / rw,
                    height: cropData.height / rh,
                    x: cropData.x / rw,
                    y: cropData.y / rh,
                    rotate: cropData.rotate * -1 // counterclockwise
                };

                if(this.croppable) {
                    let data = {
                        ...this.value,
                        cropData: relativeData,
                    };
                    this.$emit('input', data);
                    this.$emit('updated', data);
                }
            },

            updateCroppedImage() {
                if(this.croppable) {
                    this.isNew = true;
                    this.croppedImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
                }
            },

            getCropData() {
                return this.$refs.cropper.getData(true);
            },

            getImageData() {
                return this.$refs.cropper.getImageData();
            },

            rotate(degree) {
                rotateResize(this.$refs.cropper.cropper, degree);
            },
        },
        created() {
            this.options.thumbnailWidth = null;
            this.options.thumbnailHeight = null;

            if (!this.value)
                return;

            this.files.push(new File({
                ...this.value,
                upload: {}
            }));
            this.file.thumbnail = this.value.thumbnail;
            this.file.status = 'exist';
        },
        beforeDestroy() {
            this.uploader._uploader.destroy();
        },
    }
</script>