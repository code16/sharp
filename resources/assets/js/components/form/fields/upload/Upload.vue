<template>
    <sharp-vue-clip :pending-key="uniqueIdentifier"
                    :download-id="fieldConfigIdentifier"
                    :options="options"
                    :value="value"
                    :ratioX="ratioX"
                    :ratioY="ratioY"
                    :read-only="readOnly"
                    :modifiers="modifiers"
                    @input="$emit('input',$event)"
                    @error="$field.$emit('error',$event)"
                    @reset="$field.$emit('clear')">
    </sharp-vue-clip>
</template>

<script>
    import Vue from 'vue';
    import SharpVueClip from './VueClip';
    import { UploadModifiers } from './modifiers';

    import { UPLOAD_URL } from '../../../../consts';
    import { UploadXSRF } from '../../../../mixins';
    import { lang } from '../../../../mixins/Localization';

    export default {
        name: 'SharpUpload',
        components: {
            SharpVueClip
        },

        mixins: [ UploadXSRF, UploadModifiers ],
        inject: [ '$field', 'xsrfToken' ],

        props: {
            uniqueIdentifier: String,
            fieldConfigIdentifier: String,
            value: Object,

            fileFilter: Array,
            maxFileSize: Number,
            thumbnail: String,

            ratioX:Number,
            ratioY:Number,

            readOnly: Boolean
        },
        computed: {
            options() {
                let opt = {};

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
                this.patchXsrf(opt);
                return opt;
            }
        },
        methods:{
        }
    };
</script>