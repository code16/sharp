<template>
    <sharp-vue-clip
            v-show="show"
            :pending-key="pendingKey"
            :download-id="downloadId"
            :options="options"
            :value="value"
            :ratioX="ratioX"
            :ratioY="ratioY"
            :modifiers="modifiers"
            :on-added-file="handleAdded"
            @success="$emit('success',$event)"
            @removed="$emit('remove')"
            @updated="$emit('update', $event)"
            @active="$emit('active')"
            @inactive="$emit('inactive')"
            @image-updated="$emit('refresh')"
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
    import { UploadModifiers } from '../upload/modifiers';

    export default Vue.extend({

        mixins: [ UploadXSRF, UploadModifiers ],

        inject: [ 'xsrfToken'],

        props: {
            downloadId: String,
            pendingKey: String,

            id: Number,
            value: Object,

            maxImageSize: Number,
            ratioX: Number,
            ratioY: Number,
        },
        components: {
            SharpVueClip
        },
        data() {
            return {
                show: this.value,
                marker: null
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
                        limit: this.maxImageSize,
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