<template>
    <VueClip
        v-show="show"
        class="SharpMarkdownUpload"
        :pending-key="pendingKey"
        :download-id="downloadId"
        :options="options"
        :value="value"
        :ratioX="ratioX"
        :ratioY="ratioY"
        :croppable-file-types="croppableFileTypes"
        :modifiers="modifiers"
        :on-added-file="handleAdded"
        @success="$emit('success',$event)"
        @removed="$emit('remove')"
        @updated="$emit('update', $event)"
        @active="$emit('active')"
        @inactive="$emit('inactive')"
        @image-updated="$emit('refresh')"
        ref="vueclip"
    />
</template>

<script>
    import Vue from 'vue';
    import { UPLOAD_URL, lang } from 'sharp';
    import VueClip from '../upload/VueClip';

    import { UploadModifiers } from '../upload/modifiers';
    import { defaultUploadOptions } from "../../../util/upload";

    const removeKeys = ['Backspace', 'Enter'];
    const escapeKeys = ['ArrowLeft', 'ArrowUp', 'ArrowDown', 'ArrowRight', 'Escape', 'Tab'];

    export default Vue.extend({
        mixins: [ UploadModifiers ],

        components: {
            VueClip
        },

        props: {
            downloadId: String,
            pendingKey: String,

            id: Number,
            value: Object,

            maxImageSize: Number,
            ratioX: Number,
            ratioY: Number,
            croppableFileTypes: Array,
        },

        data() {
            return {
                show: this.value,
                marker: null
            }
        },
        computed: {
            options() {
                return {
                    ...defaultUploadOptions,
                    url: UPLOAD_URL,
                    uploadMultiple: false,
                    acceptedFiles: {
                        extensions: ['image/*'],
                        message: lang('form.upload.message.bad_extension')
                    },
                    maxFilesize: {
                        limit: this.maxImageSize,
                        message: lang('form.upload.message.file_too_big')
                    },
                };
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
                this.$emit('added');
            },
            inputClick() {
                this.fileInput.click();
            },
        },
        mounted() {
            this.$on('delete-intent', () => {
                let removeButton = this.$el.querySelector('.SharpUpload__remove-button');
                removeButton.focus();
                removeButton.addEventListener('keydown', e => {
                    if(removeKeys.includes(e.key)) {
                        this.$emit('remove');
                    }
                    else if(escapeKeys.includes(e.key)) {
                        this.$emit('escape');
                    }
                })
            })
        }
    });
</script>