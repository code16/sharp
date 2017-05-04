<template>
    <div class="SharpUpload">
        <dropzone :id="`dropzone_${fieldKey}`"
                  url="http://jsonplaceholder.typicode.com/posts"
                  ref="dropzone">
        </dropzone>
    </div>
</template>

<script>
    import Dropzone from 'vue2-dropzone/src/index';

    export default {
        name: 'SharpUpload',
        components: {
            Dropzone
        },
        props: {
            fieldKey: String,
            maxFileSize: Number,
            fileFilter: Array,
            thumbnail: String
        },
        data() {
            return {
                showButton:true,
                dz:null
            }
        },
        computed: {
            options() {
                let opt = {};

                opt.url = 'http://jsonplaceholder.typicode.com/posts';
                opt.uploadMultiple = false;
                if (this.fileFilter) {
                    opt.acceptedFiles = {
                        extensions: this.acceptedFiles,
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
        },
        methods: {
            init(upload) {
                this.dz = this.$refs.dropzone;
            }
        },
    }
</script>