<template>
    <div class="ShowFileField">
        <div class="row mx-n2">
            <div class="col-3 px-2" style="max-width: 150px">
                <template v-if="thumbnailUrl">
                    <img class="ShowFileField__thumbnail mw-100 h-auto" :src="thumbnailUrl" alt="">
                </template>
            </div>
            <div class="col px-2">
                <div class="ShowFileField__label text-truncate mb-2">
                    {{ fileLabel }}
                </div>
                <div class="ShowFileField__size text-muted">
                    {{ sizeLabel }}
                </div>
            </div>
            <div class="col-auto px-2 align-self-center">
                <Button type="primary" outline small :href="downloadUrl">
                    {{ lang('show.file.download') }}
                </Button>
            </div>
        </div>
    </div>
</template>

<script>
    import { Button } from "sharp-ui";
    import { lang, filesizeLabel } from 'sharp';
    import { mapGetters } from 'vuex';
    import { downloadFileUrl } from "../../api";

    export default {
        components: {
            Button,
        },
        props: {
            fieldConfigIdentifier: String,
            value: Object,
            label: String,
        },
        computed: {
            ...mapGetters('show', [
                'entityKey',
                'instanceId',
            ]),
            downloadUrl() {
                return downloadFileUrl({
                    entityKey: this.entityKey,
                    instanceId: this.instanceId,
                    fieldKey: this.fieldConfigIdentifier,
                    fileName: this.fileName,
                })
            },
            fileName() {
                return this.value ? this.value.name : null;
            },
            fileLabel() {
                const parts = (this.fileName || '').split('/');
                return parts[parts.length - 1];
            },
            thumbnailUrl() {
                return this.value ? this.value.thumbnail : null;
            },
            size() {
                return this.value ? this.value.size : null;
            },
            sizeLabel() {
                return this.size
                    ? filesizeLabel(this.size)
                    : null;
            },
        },
        methods: {
            lang,
        }
    }
</script>