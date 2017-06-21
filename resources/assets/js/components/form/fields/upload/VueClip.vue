<template>
    <div class="form-control">
        <form class="SharpUpload dropzone">
            <div v-show="!file">
                <button type="button" class="dz-message SharpButton SharpButton--primary">Importer...</button>
            </div>
            <template v-if="file">
                <div class="SharpUpload__container">
                    <div class="SharpUpload__thumbnail" v-if="!!imageSrc">
                        <img :src="imageSrc">
                    </div>
                    <div class="SharpUpload__infos">
                        <div>
                            <label class="form-control-label">{{ file.name }}</label>
                            <div>{{ size }}</div>
                            <div class="progress" v-show="showProgressBar">
                                <div class="progress-bar" role="progressbar" :style="{width:`${progress}%`}"
                                     :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div v-if="!!originalImageSrc">
                            <button type="button" class="SharpButton SharpButton--secondary" @click="onEditButtonClick">Modifier</button>
                        </div>
                    </div>
                </div>
                <slot name="removeButton"></slot>
                <template v-if="!$slots.removeButton">
                    <button class="SharpUpload__close-button" type="button" @click="remove()">
                        <svg class="SharpUpload__close-icon" aria-label="close" width="10" height="10" viewBox="0 0 10 10" fill-rule="evenodd">
                            <path d="M9.8 8.6L8.4 10 5 6.4 1.4 10 0 8.6 3.6 5 .1 1.4 1.5 0 5 3.6 8.6 0 10 1.4 6.4 5z"></path>
                        </svg>
                    </button>
                </template>
            </template>
            <div ref="clip-preview-template" class="clip-preview-template" style="display: none;">
                <div></div>
            </div>
        </form>
        <template v-if="!!originalImageSrc">
            <b-modal v-model="showEditModal" @ok="onEditModalOk" @shown="onEditModalShown" :close-on-backdrop="false">
                <vue-cropper ref="cropper" class="SharpUpload__modal-vue-cropper"
                             :view-mode="2" drag-mode="crop"  :aspect-ratio="ratioX/ratioY"
                             :auto-crop-area="1" :zoomable="false" :guides="false"
                             :background="true" :rotatable="true" :src="originalImageSrc"
                             :ready="onCropperReady"
                             alt="Source image">
                </vue-cropper>
                <button class="SharpButton SharpButton--primary" @click="rotate(90)"><i class="fa fa-rotate-right"></i></button>
                <button class="SharpButton SharpButton--primary" @click="rotate(-90)"><i class="fa fa-rotate-left"></i></button>
            </b-modal>
        </template>
    </div>
</template>

<script>
    import VueClip from '../../../vendor/vue-clip/components/Clip/index';
    import File from '../../../vendor/vue-clip/File';

    import bModal from '../../../vendor/bootstrap-vue/components/modal';
    import VueCropper from 'vue-cropperjs';

    import rotateResize from './rotate';

    export default {
        name: 'SharpVueClip',

        extends: VueClip,

        components: {
            bModal
        },

        inject : ['actionsBus'],

        props: {
            ratioX: Number,
            ratioY: Number,
            value: Object
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
                this.$emit('success', data);

                data.uploaded = true;
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
            },

            resetEdit() {
                this.croppedImg = null;
                this.resized = false;
            },

            onEditButtonClick() {
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
                if(this.croppable) {
                    this.$parent.$emit('input', {
                        ...this.value,
                        cropData: this.getCropData(),
                    });
                }
            },

            updateCroppedImage() {
                if(this.croppable) {
                    this.croppedImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
                }
            },

            getCropData() {
                return this.$refs.cropper.getData(true);
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
    }
</script>