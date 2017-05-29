<template>
    <sharp-vue-clip v-show="show"
        :options="options"
        :value="value"
        :on-added-file="_=>onAddedFile()"
        @success="data=>onSuccess(data)"
        class="SharpMarkdownUpload"
        ref="vueclip"
    >
        <template slot="removeButton">
            <button type="button" class="close" aria-label="Close" @click="_=>onRemoved()">
                <span aria-hidden="true">&times;</span>
            </button>
        </template>
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
                show:false,
                removed:false
            }
        },
        computed: {
            options() {
                return {
                    url:UPLOAD_URL,
                    uploadMultiple:false,
                    acceptedFiles: {
                        extensions: ['image/*'],
                        message: Messages.uploadFileBadExtension
                    },
                    maxFilesize: {
                        limit: this.maxFileSize,
                        message: Messages.uploadFileTooBig
                    }
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
                    this.onRemoved();
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