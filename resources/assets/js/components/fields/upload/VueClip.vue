<template>
    <form class="SharpUpload dropzone">
        <div v-show="!file">
            <button type="button" class="dz-message btn btn-primary">Importer...</button>
        </div>
        <template v-if="file">
            <img :src="file.dataUrl" />
            {{ file.name }} {{ file.status }}
            <button type="button" class="close" aria-label="Close" @click="remove()">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="progress" v-if="file.status == 'added'">
                <div class="progress-bar" role="progressbar" :style="{width:`${progress}%`}" :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </template>
        <div ref="clip-preview-template" class="clip-preview-template" style="display: none;"><div></div></div>
    </form>
</template>

<script>
    import VueClip from 'vue-clip/src/components/Clip/index';

    export default {
        name: 'SharpVueClip',

        extends: VueClip,

        props: {

        },
        data() {
            return {

            }
        },
        computed: {
            file() {
                return this.files[0];
            },
            progress() {
                return Math.floor(this.file.progress);
            }
        },
        methods: {
            remove() {
                this.removeFile(this.file);
                this.files.splice(0,1);
            }
        },
    }
</script>