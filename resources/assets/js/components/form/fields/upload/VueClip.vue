<template>
    <div class="SharpUpload" :class="{'SharpUpload--empty':!file}">
        <div class="SharpModule__inner">
            <div class="SharpModule__content">
                <form v-show="!file" class="dropzone">
                    <button type="button" class="dz-message SharpButton SharpButton--secondary SharpUpload__upload-button" :disabled="readOnly">Importer...</button>
                </form>
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
                            <div>
                                <template v-if="!!originalImageSrc">
                                    <button type="button" class="SharpButton SharpButton--secondary" @click="onEditButtonClick">Modifier</button>
                                </template>
                                <button type="button" class="SharpButton SharpButton--secondary SharpButton--danger" @click="remove()">Supprimer</button>
                            </div>
                        </div>
                    </div>
                </template>
                <div ref="clip-preview-template" class="clip-preview-template" style="display: none;">
                    <div></div>
                </div>
            </div>
        </div>
        <template v-if="!!originalImageSrc">
            <sharp-modal v-model="showEditModal" @ok="onEditModalOk" @shown="onEditModalShown" :close-on-backdrop="false">
                <vue-cropper ref="cropper" class="SharpUpload__modal-vue-cropper"
                             :view-mode="2" drag-mode="crop"  :aspect-ratio="ratioX/ratioY"
                             :auto-crop-area="1" :zoomable="false" :guides="false"
                             :background="true" :rotatable="true" :src="originalImageSrc"
                             :ready="onCropperReady"
                             alt="Source image">
                </vue-cropper>
                <div>
                    <button class="SharpButton SharpButton--primary" @click="rotate(90)"><i class="fa fa-rotate-right"></i></button>
                    <button class="SharpButton SharpButton--primary" @click="rotate(-90)"><i class="fa fa-rotate-left"></i></button>
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

    export default {
        name: 'SharpVueClip',

        extends: VueClip,

        components: {
            [Modal.name]: Modal
        },

        inject : ['actionsBus'],

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
                this.$emit('removed');
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