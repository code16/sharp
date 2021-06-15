<template>
    <VueClip
        :value="value"
        :pending-key="uniqueIdentifier"
        :download-id="fieldConfigIdentifier"
        :options="options"
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
    import { defaultUploadOptions, getUploadOptions, maxFileSizeMessage } from "../../../util/upload";

    export default {
        name: 'SharpUpload',
        components: {
            VueClip
        },

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
                return getUploadOptions({
                    fileFilter: this.fileFilter,
                    maxFileSize: this.maxFileSize,
                });
            }
        },
    };
</script>
