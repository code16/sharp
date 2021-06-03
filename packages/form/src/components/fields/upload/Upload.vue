<template>
    <VueClip
        :value="value"
        :pending-key="uniqueIdentifier"
        :download-id="fieldConfigIdentifier"
        :options="options"
        :modifiers="modifiers"
        :root="root"
        v-bind="$props"
        @input="$emit('input', $event)"
        @error="$emit('error', $event)"
        @reset="$emit('clear')"
    />
</template>

<script>
    import { UPLOAD_URL, lang } from 'sharp';
    import VueClip from './VueClip';
    import { UploadModifiers } from './modifiers';
    import { defaultUploadOptions, maxFileSizeMessage } from "../../../util/upload";

    export default {
        name: 'SharpUpload',
        components: {
            VueClip
        },

        mixins: [ UploadModifiers ],

        props: {
            ...VueClip.props,

            value: Object,
            uniqueIdentifier: String,
            fieldConfigIdentifier: String,

            fileFilter: Array,
            maxFileSize: Number,
            root: Boolean,
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
            }
        },
    };
</script>
