<template>
    <VueClip
        :pending-key="uniqueIdentifier"
        :download-id="fieldConfigIdentifier"
        :options="options"
        :value="value"
        :ratioX="ratioX"
        :ratioY="ratioY"
        :croppable-file-types="croppableFileTypes"
        :read-only="readOnly"
        :modifiers="modifiers"
        @input="$emit('input',$event)"
        @error="$field.$emit('error',$event)"
        @reset="$field.$emit('clear')"
    />
</template>

<script>
    import { UPLOAD_URL, lang } from 'sharp';
    import VueClip from './VueClip';
    import { UploadModifiers } from './modifiers';
    import { defaultUploadOptions } from "../../../util/upload";

    export default {
        name: 'SharpUpload',
        components: {
            VueClip
        },

        mixins: [ UploadModifiers ],
        inject: ['$field'],

        props: {
            uniqueIdentifier: String,
            fieldConfigIdentifier: String,
            value: Object,

            fileFilter: Array,
            maxFileSize: Number,
            thumbnail: String,
            croppableFileTypes: Array,

            ratioX:Number,
            ratioY:Number,

            readOnly: Boolean
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
                        message: lang('form.upload.message.file_too_big')
                    }
                }
                return opt;
            }
        },
    };
</script>