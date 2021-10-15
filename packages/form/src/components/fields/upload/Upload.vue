<template>
    <VueClip
        :value="value"
        :options="options"
        :root="root"
        v-bind="$props"
        @input="$emit('input', $event)"
        @error="$emit('error', $event)"
        @add="$emit('clear')"
    />
</template>

<script>
    import VueClip from './VueClip';
    import { getUploadOptions } from "../../../util/upload";

    export default {
        name: 'SharpUpload',
        components: {
            VueClip
        },

        props: {
            ...VueClip.props,

            value: Object,

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
