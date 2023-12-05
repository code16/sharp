<template>
    <input
        type="file"
        style="display: none"
        :accept="accept"
        @change="handleChanged"
        ref="input"
    >
</template>

<script>
    import { Upload } from "./upload";

    export default {
        props: {
            editor: Object,
        },
        computed: {
            uploadExtension() {
                return this.editor.options.extensions.find(extension => extension.name === Upload.name);
            },
            accept() {
                return this.uploadExtension?.options.fieldProps.fileFilter;
            },
        },
        methods: {
            handleChanged(e) {
                this.editor.chain()
                    .focus()
                    .insertUpload({
                        file: e.target.files[0],
                    })
                    .run();
            },
            handleUploadRequested() {
                this.$refs.input.value = '';
                setTimeout(() => {
                    this.$refs.input.click();
                });
            },
        },
        mounted() {
            this.editor.on('new-upload', this.handleUploadRequested);
        },
    }
</script>
