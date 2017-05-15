<template>
        <sharp-vue-clip v-show="show"
                        :options="options" :value="value"
                        :on-removed-file="onRemoved"
                        :on-added-file="onAddedFile"
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
            onAdded:Function
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
            }
        },
        mounted() {
            this.fileInput.click();
            console.log(this);
        }
    });
</script>