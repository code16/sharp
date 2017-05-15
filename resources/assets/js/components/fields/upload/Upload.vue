<template>
    <sharp-vue-clip :options="options" :value="value"></sharp-vue-clip>
</template>

<script>
    import Vue from 'vue';
    import SharpVueClip from './VueClip';
    import Messages from '../../../messages';

    export default {
        name: 'SharpUpload',
        components: {
            SharpVueClip
        },

        props: {
            value: Object,

            url: String,
            fileFilter: Array,
            maxFileSize: Number,
            thumbnail: String
        },
        computed: {
            options() {
                let opt = {};

                opt.url = 'http://jsonplaceholder.typicode.com/posts';
                opt.uploadMultiple = false;
                if (this.fileFilter) {
                    opt.acceptedFiles = {
                        extensions: this.fileFilter,
                        message: Messages.uploadFileBadExtension
                    }
                }
                if (this.maxFileSize) {
                    opt.maxFilesize = {
                        limit: this.maxFileSize,
                        message: Messages.uploadFileTooBig
                    }
                }

                return opt;
            }
        }
    };
</script>