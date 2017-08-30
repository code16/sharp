<template>
    <div class="SharpUpload" :class="{'SharpUpload--empty':!file, 'SharpUpload--disabled':readOnly}">
        <div class="SharpUpload__inner">
            <div class="SharpUpload__content">
                <form v-show="!file" class="dropzone">
                    <button type="button" class="dz-message SharpButton SharpButton--secondary SharpUpload__upload-button" :disabled="readOnly">
                        {{ l('form.upload.browse_button') }}
                    </button>
                </form>
                <template v-if="file">
                    <div class="SharpUpload__container">
                        <div class="SharpUpload__thumbnail" v-if="!!imageSrc">
                            <img :src="imageSrc" @load="$emit('image-updated')">
                        </div>
                        <div class="SharpUpload__infos">
                            <div>
                                <label class="SharpUpload__info">{{ fileName }}</label>
                                <div class="SharpUpload__info">{{ size }}</div>
                                <div class="progress" v-show="showProgressBar">
                                    <div class="progress-bar" role="progressbar" :style="{width:`${progress}%`}"
                                         :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div>
                                <template v-if="!!originalImageSrc">
                                    <button type="button" class="SharpButton SharpButton--sm SharpButton--secondary" @click="onEditButtonClick" :disabled="readOnly">
                                        {{ l('form.upload.edit_button') }}
                                    </button>
                                </template>
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
            <sharp-modal v-model="showEditModal" @ok="onEditModalOk" @shown="onEditModalShown" @hidden="$emit('inactive')" :close-on-backdrop="false"
                         :title="l('modals.cropper.title')">
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
    </div>
</template>

<script>
    import VueClip from '../../../vendor/vue-clip/components/Clip/index';
    import File from '../../../vendor/vue-clip/File';
    import Modal from '../../../Modal';
    import VueCropper from 'vue-cropperjs';
    import rotateResize from './rotate';

    import { Localization } from '../../../../mixins';

    export default {
        name: 'SharpVueClip',

        extends: VueClip,

        components: {
            [Modal.name]: Modal
        },

        inject : [ 'actionsBus' ],

        mixins: [ Localization ],

        props: {
            ratioX: Number,
            ratioY: Number,
            value: Object,

            readOnly: Boolean
        },

        data() {
            return {
                showProgressBar: false,
                showEditModal: false,
                croppedImg: null,
                resized: false,
                croppable: false
            }
        },
        watch: {
            'file.status'(status) {
                (status in this.statusFunction) && this.statusFunction[status]();
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
                    return '--';
                }
                let size = (parseFloat((this.file.size).toFixed(2))/1024)/1024;
                let res = '';
                if(size<0.1) { res+='<'; size=0.1 }
                res += size.toLocaleString();
                return `${res} MB`;
            },
            progress() {
                return Math.floor(this.file.progress);
            },
            statusFunction() {
                return { error:this.onStatusError, success:this.onStatusSuccess, added:this.onStatusAdded }
            },

            fileName() {
                let splitted = this.file.name.split('/');
                return splitted.length ? splitted[splitted.length-1] : '';
            }
        },
        methods: {
            // status callbacks
            onStatusAdded() {
                this.showProgressBar = true;
                this.$emit('reset');

                this.actionsBus.$emit('disable-submit');
            },
            onStatusError() {
                this.showProgressBar = false;
                let msg = this.file.errorMessage;
                this.remove();
                this.$emit('error', msg);

                this.actionsBus.$emit('enable-submit');
            },
            onStatusSuccess() {
                setTimeout(() => this.showProgressBar = false, 1000);
                let data = {};
                try {
                    data = JSON.parse(this.file.xhrResponse.responseText);
                }
                catch(e) { console.log(e); }

                data.uploaded = true;
                this.$emit('success', data);

                this.$parent.$emit('input',data);
                this.actionsBus.$emit('enable-submit');

                this.croppable = true;
                this.$nextTick(_=>{
                    this.isCropperReady() && this.onCropperReady();
                });
            },

            // actions
            remove() {
                this.removeFile(this.file);
                this.files.splice(0, 1);

                this.resetEdit();

                this.$parent.$emit('input', null);
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
                    this.$parent.$emit('input', data);
                    this.$emit('updated', data);
                }
            },

            updateCroppedImage() {
                if(this.croppable) {
                    this.croppedImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
                    //this.$nextTick(() => this.$emit('cropped'));
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
        },
        beforeDestroy() {
            this.uploader._uploader.destroy();
        },
        mounted() {
            console.log(this.uploader._uploader);
        }
    }
</script>