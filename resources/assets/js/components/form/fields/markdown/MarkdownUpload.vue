<template>
    <sharp-vue-clip
            v-show="show"
            :options="options"
            :value="value"
            :on-added-file="handleAdded"
            @success="$emit('success',$event)"
            @removed="$emit('remove')"
            @updated="$emit('update', $event)"
            @active="$emit('active')"
            @inactive="$emit('inactive')"
            class="SharpMarkdownUpload"
            ref="vueclip">
    </sharp-vue-clip>
</template>

<script>
    import Vue from 'vue';
    import SharpVueClip from '../upload/VueClip';
    import Messages from '../../../../messages';

    import { UPLOAD_URL } from '../../../../consts';
    import { UploadXSRF } from '../../../../mixins';

    export default Vue.extend({
        mixins: [ UploadXSRF ],
        props: {
            id: Number,
            value: Object,
            maxFileSize: Number,

            marker: Object,

            xsrfToken: String
        },
        components: {
            SharpVueClip
        },
        data() {
            return {
                show: this.value
            }
        },
        computed: {
            options() {
                return this.patchXsrf({
                    url: UPLOAD_URL,
                    uploadMultiple: false,
                    acceptedFiles: {
                        extensions: ['image/*'],
                        message: Messages.uploadFileBadExtension
                    },
                    maxFilesize: {
                        limit: this.maxFileSize,
                        message: Messages.uploadFileTooBig
                    }
                });
            },
            dropzone() {
                return this.$refs.vueclip.uploader._uploader;
            },
            fileInput() {
                return this.dropzone.hiddenFileInput;
            }
        },
        methods: {
            handleAdded() {
                this.show = true;
                this.$nextTick(() => this.$emit('added'));
            },
            checkCancelled() {
                if (!this.show)
                    this.$emit('remove');
                document.body.onfocus = null;
            },
            inputClick() {
                this.fileInput.click();
                document.body.onfocus = () => {
                    setTimeout(this.checkCancelled, 100);
                };
            },
        }
    });
</script>