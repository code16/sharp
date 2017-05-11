<template>
    <sharp-vue-clip :options="options" :value="value"></sharp-vue-clip>
</template>

<script>
    import SharpVueClip from './VueClip';

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
                        message: "L'extension est invalide"
                    }
                }
                if (this.maxFileSize) {
                    opt.maxFilesize = {
                        limit: this.maxFileSize,
                        message: "Le fichier sélectionné est trop grand"
                    }
                }
                if (this.thumbnail) {
                    let [width, height] = this.thumbnail.split('x');
                    opt.thumbnailWidth = width;
                    opt.thumbnailHeight = height;
                }

                return opt;
            }
        }
    }
</script>