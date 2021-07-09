<template>
    <VueClip
        v-show="show"
        class="SharpMarkdownUpload"
        :value="value"
        :pending-key="pendingKey"
        :download-id="downloadId"
        :options="options"
        :modifiers="modifiers"
        :on-added-file="handleAdded"
        v-bind="$props"
        @error="handleError"
        @success="$emit('success', $event)"
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
    import { UPLOAD_URL, lang, showAlert } from 'sharp';
    import VueClip from '../upload/VueClip';

    import { UploadModifiers } from '../upload/modifiers';
    import { defaultUploadOptions, maxFileSizeMessage } from "../../../util/upload";

    const removeKeys = ['Backspace', 'Enter'];
    const escapeKeys = ['ArrowLeft', 'ArrowUp', 'ArrowDown', 'ArrowRight', 'Escape', 'Tab'];

    export default Vue.extend({
        mixins: [ UploadModifiers ],

        components: {
            VueClip
        },

        props: {
            ...VueClip.props,

            downloadId: String,
            pendingKey: String,

            id: Number, // needed in markdown component
            value: Object,

            maxFileSize: Number,
            fileFilter: Array,
        },

        data() {
            return {
                show: this.value,
                marker: null
            }
        },
        computed: {
            options() {
                let opt = {
                    ...defaultUploadOptions,
                };

                opt.url = UPLOAD_URL;
                opt.uploadMultiple = false;

                if (this.fileFilter) {
                    opt.acceptedFiles = {
                        extensions: this.fileFilter,
                        message: lang('form.upload.message.bad_extension')
                    }
                }
                if (this.maxFileSize) {
                    opt.maxFilesize = {
                        limit: this.maxFileSize,
                        message: maxFileSizeMessage(this.maxFileSize),
                    }
                }
                return opt;
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
            handleError(message) {
                showAlert(message, {
                    isError: true,
                    title: lang(`modals.error.title`),
                });
                this.$emit('error', message);
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
