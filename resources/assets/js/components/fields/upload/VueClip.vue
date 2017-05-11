<template>
    <div class="form-control">
        <form class="SharpUpload dropzone">
            <div v-show="!file">
                <button type="button" class="dz-message btn btn-primary">Importer...</button>
            </div>
            <template v-if="file">
                <div class="SharpUpload__container">
                    <div class="SharpUpload__thumbnail">
                        <img v-if="!!imageSrc" :src="imageSrc" :width="options.thumbnailWidth" :height="options.thumbnailHeight">
                    </div>
                    <div class="SharpUpload__infos">
                        <h3>{{ file.name }}</h3>
                        <div>{{ size }}</div>
                        <div class="progress" v-show="showProgressBar">
                            <div class="progress-bar" role="progressbar" :style="{width:`${progress}%`}" :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>

                <button type="button" class="close" aria-label="Close" @click="remove()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </template>
            <div ref="clip-preview-template" class="clip-preview-template" style="display: none;"><div></div></div>
        </form>
    </div>
</template>

<script>
    import VueClip from 'vue-clip/src/components/Clip/index';
    import File from 'vue-clip/src/File';

    export default {
        name: 'SharpVueClip',

        extends: VueClip,

        inject: ['value'],

        data() {
            return {
                showProgressBar: false
            }
        },
        watch: {
            'file.status'(status) {
                typeof this[status] === 'function' && this[status]();
            }
        },
        computed: {
            file() {
                return this.files[0];
            },
            imageSrc() {
                return this.file.thumbnail || this.file.dataUrl;
            },
            size() {
                let size = parseFloat((this.file.size/1024).toFixed(2));
                return `${size.toLocaleString()} MB`;
            },
            progress() {
                return Math.floor(this.file.progress);
            }
        },
        methods: {
            // status callbacks
            added() {
                this.showProgressBar = true;
            },
            error() {
                this.showProgressBar = false
            },
            success() {
                setTimeout(()=>this.showProgressBar = false, 1000);

                //let data = JSON.parse(this.file.xhrResponse.responseText);
                let data = {
                    name:"_imageid_.jpg"
                };

                this.$parent.$emit('input', {
                    uploaded:true,
                    ...data
                });
            },

            // actions
            remove() {
                this.removeFile(this.file);
                this.files.splice(0,1);

                this.$parent.$emit('input', null);
            }
        },
        created() {
            if(!this.value)
                return;

            this.files.push(new File({
                ...this.value,
                upload: {}
            }));
            this.file.thumbnail = this.value.thumbnail;
        }
    }
</script>