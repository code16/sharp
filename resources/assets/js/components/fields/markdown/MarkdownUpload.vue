<template>
        <sharp-vue-clip v-show="show"
                        :options="options" :value="value"
                        :on-removed-file="_=>onRemoved(marker)"
                        :on-added-file="_=>onAddedFile(marker)"
                        @success="data=>onSuccess(marker,data)"
                        class="SharpMarkdownUpload"
                        ref="vueclip">
        </sharp-vue-clip>
</template>

<script>
    import Vue from 'vue';
    import SharpVueClip from '../upload/VueClip';
    import Messages from '../../../messages';

    export default Vue.extend({
        props: {
            value:Object,
            maxFileSize:Number,

            onSuccess:Function,
            onRemoved:Function,
            onAdded:Function,

            marker:Object
        },
        components: {
            SharpVueClip
        },
        data() {
            return {
                show:false
            }
        },
        computed: {
            options() {
                return {
                    url:'http://jsonplaceholder.typicode.com/posts',
                    uploadMultiple:false,
                    acceptedFiles: {
                        extensions: ['image/*'],
                        message: Messages.uploadFileBadExtension
                    },
                    maxFilesize: {
                        limit: this.maxFileSize,
                        message: Messages.uploadFileTooBig
                    },
                }
            },
            dropzone() {
                return this.$refs.vueclip.uploader._uploader;
            },
            fileInput() {
                return this.dropzone.hiddenFileInput;
            }
        },
        methods: {
            onAddedFile() {
                this.show = true;
                this.$nextTick(_=>{
                    this.onAdded();
                })
            },
            checkCancelled() {
                if(!this.show)
                    this.onRemoved(this.marker);
                document.body.onfocus = null;
            },
            inputClick() {
                this.fileInput.click();
                document.body.onfocus = _ => {
                    setTimeout(this.checkCancelled, 100);
                };
            },
        }
    });
</script>