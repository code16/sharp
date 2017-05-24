<template>
    <div class="form-control">
        <form class="SharpUpload dropzone">
            <div v-show="!file">
                <button type="button" class="dz-message btn btn-primary">Importer...</button>
            </div>
            <template v-if="file">
                <div class="SharpUpload__container">
                    <div class="SharpUpload__thumbnail">
                        <img v-if="!!imageSrc" :src="imageSrc">
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
                            <button type="button" class="btn btn-secondary" @click="showEditModal=true">Modifier</button>
                        </div>
                    </div>
                </div>
                <slot name="removeButton"></slot>
                <template v-if="!$slots.removeButton">
                    <button type="button" class="close" aria-label="Close" @click="remove()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </template>
            </template>
            <div ref="clip-preview-template" class="clip-preview-template" style="display: none;">
                <div></div>
            </div>
        </form>
        <template v-if="!!originalImageSrc">
            <b-modal v-model="showEditModal" @ok="imageEditOk" @shown="editModalShown" :close-on-backdrop="false">
                <vue-cropper ref="cropper" class="SharpUpload__modal-vue-cropper"
                             :view-mode="2" drag-mode="crop"  :aspect-ratio="ratioX/ratioY"
                             :auto-crop-area="1" :zoomable="false" :guides="false"
                             :background="true" :rotatable="true" :src="originalImageSrc" alt="Source image">
                </vue-cropper>
                <button class="btn btn-primary" @click="rotate(90)"><i class="fa fa-rotate-right"></i></button>
                <button class="btn btn-primary" @click="rotate(-90)"><i class="fa fa-rotate-left"></i></button>
            </b-modal>
        </template>
    </div>
</template>

<script>
    import VueClip from '../../vendor/vue-clip/components/Clip';
    import File from '../../vendor/vue-clip/File';

    import bModal from '../../vendor/bootstrap-vue/components/modal';
    import VueCropper from 'vue-cropperjs';

    import rotateResize from './rotate';

    export default {
        name: 'SharpVueClip',

        extends: VueClip,

        components: {
            bModal
        },

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
            }
        },
        watch: {
            'file.status'(status) {
                typeof this[status] === 'function' && this[status]();
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
                let prepend;
                let size = (parseFloat((this.file.size).toFixed(2))/1024)/1024;
                if(size < 0.1) {
                    prepend='<';
                    size = 0.1;
                }
                return `${prepend}${size.toLocaleString()} MB`;
            },
            progress() {
                return Math.floor(this.file.progress);
            },
        },
        methods: {
            // status callbacks
            added() {
                this.showProgressBar = true
            },
            error() {
                this.showProgressBar = false
            },
            success() {
                setTimeout(() => this.showProgressBar = false, 1000);

                //let data = JSON.parse(this.file.xhrResponse.responseText);
                let xhr = this.file.xhrResponse;
                let data = {
                    name: "_imageid_.jpg"
                };


                this.$emit('success', data);

                if(this.ratioX && this.ratioY) {
                    this.$nextTick(()=>{
                        this.updateCroppedImage();
                        this.$parent.$emit('input', {
                            uploaded: true,
                            cropData: this.getCropData(),
                            ...data
                        });
                    });
                }
                else {
                    this.$parent.$emit('input', {
                        uploaded: true,
                        ...data
                    });
                }
            },

            // actions
            remove() {
                this.removeFile(this.file);
                this.files.splice(0, 1);

                this.resetEdit();

                this.$parent.$emit('input', null);
            },

            resetEdit() {
                this.croppedImg = null;
                this.resized = false;
            },

            imageEditOk() {
                this.updateCroppedImage();
                this.$parent.$emit('input', {
                    ...this.value,
                    cropData: this.getCropData(),
                });
            },

            updateCroppedImage() {
                this.croppedImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
            },

            getCropData() {
                return this.$refs.cropper.getData(true);
            },

            rotate(degree) {
                rotateResize(this.$refs.cropper.cropper, degree);
            },

            editModalShown() {
                if(!this.resized) {
                    this.$nextTick(()=>{
                        let cropper = this.$refs.cropper.cropper;

                        cropper.resize();
                        cropper.reset();
                        this.resized=true;
                    });
                }
            }
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
        mounted() {
            console.log(this.$refs.cropper);
        }
    }
</script>