<template>
    <div class="SharpUpload">
        <template v-if="!files.length">
            <button type="button" class="dz-message btn btn-primary">Importer...</button>
        </template>
        <template v-else>
            <div v-for="file in props.files">
                <img :src="file.dataUrl" />
                {{ file.name }} {{ file.status }}
            </div>
        </template>
        <div ref="clip-preview-template" class="clip-preview-template" style="display: none;"><div></div></div>
    </div>
</template>

<script>
    import VueClip from 'vue-clip/src/components/Clip/index';

    export default {
        name: 'SharpVueClip',

        extends: VueClip,

        props: {
            fieldKey: String,
        },
        data() {
            return {

            }
        },
        computed: {

        },
        methods: {

        },
    }
</script>