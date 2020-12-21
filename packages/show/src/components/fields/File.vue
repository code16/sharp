<template>
    <FieldLayout class="ShowFileField" :class="classes" :label="label">
        <div class="row mx-n2">
            <div class="col-3 px-2 align-self-center ShowFileField__thumbnail-col" :class="thumbnailColClasses">
                <template v-if="hasThumbnail">
                    <div class="ShowFileField__thumbnail-container">
                        <img
                            class="ShowFileField__thumbnail"
                            :src="thumbnailUrl"
                            :style="thumbnailStyle"
                            alt=""
                            ref="thumbnail"
                            @load="handleThumbnailLoaded"
                        >
                    </div>
                </template>
                <template v-else>
                    <div class="ShowFileField__placeholder text-center">
                        <i class="fa far" :class="iconClass"></i>
                    </div>
                </template>
            </div>
            <div class="col px-2 mt-1" style="min-width: 0">
                <div class="ShowFileField__name text-truncate mb-2">
                    {{ fileName }}
                </div>
                <div class="ShowFileField__info">
                    <div class="row mx-n2 h-100">
                        <div class="col-auto px-2">
                            <div class="ShowFileField__size text-muted">
                                {{ sizeLabel }}
                            </div>
                        </div>
                        <div class="col-auto px-2">
                            <div class="text-muted">
                                <i class="fa fas fa-download"></i>
                                <a :href="downloadUrl" :download="fileName" style="color:inherit">
                                    {{ lang('show.file.download') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </FieldLayout>
</template>

<script>
    import { mapGetters } from 'vuex';
    import debounce from 'lodash/debounce';
    import { getClassNameForExtension } from 'font-awesome-filetypes';
    import { lang, filesizeLabel } from 'sharp';
    import { Button } from "sharp-ui";
    import { downloadFileUrl } from "../../api";
    import { syncVisibility } from "../../util/fields/visiblity";
    import FieldLayout from "../FieldLayout";

    export default {
        components: {
            Button,
            FieldLayout,
        },
        props: {
            fieldConfigIdentifier: String,
            value: Object,
            label: String,
            collapsed: {
                type: Boolean,
                default: true,
            },
            list: Boolean,
        },
        data() {
            return {
                thumbnailWidth: 0,
            }
        },
        computed: {
            ...mapGetters('show', [
                'entityKey',
                'instanceId',
            ]),
            classes() {
                return {
                    'ShowFileField--has-label': !!this.label,
                }
            },
            thumbnailColClasses() {
                return {
                    'ShowFileField__thumbnail-col--collapsed': !this.hasThumbnail && this.collapsed,
                }
            },
            downloadUrl() {
                return downloadFileUrl({
                    entityKey: this.entityKey,
                    instanceId: this.instanceId,
                    fieldKey: this.fieldConfigIdentifier,
                    fileName: this.fileName,
                })
            },
            name() {
                return this.value ? this.value.name : null;
            },
            fileName() {
                const parts = (this.name || '').split('/');
                return parts[parts.length - 1];
            },
            hasThumbnail() {
                return !!this.value?.thumbnail;
            },
            thumbnailUrl() {
                return this.value ? this.value.thumbnail : null;
            },
            thumbnailStyle() {
                return {
                    'max-height': this.thumbnailWidth
                        ? `${this.thumbnailWidth}px`
                        : null,
                }
            },
            size() {
                return this.value ? this.value.size : null;
            },
            sizeLabel() {
                return this.size
                    ? filesizeLabel(this.size)
                    : null;
            },
            iconClass() {
                const extension = this.fileName.split('.').pop();
                return getClassNameForExtension(extension);
            },
            isVisible() {
                return !!this.value;
            },
        },
        methods: {
            lang,
            async layout() {
                if(this.$refs.thumbnail) {
                    this.thumbnailWidth = this.$refs.thumbnail.parentElement.offsetWidth;
                }
            },
            handleThumbnailLoaded() {
                this.layout();
            }
        },
        created() {
            syncVisibility(this, () => this.isVisible);
        },
        mounted() {
            this.layout();
            this.handleResize = debounce(this.layout, 150);
            window.addEventListener('resize', this.handleResize);
        },
        beforeDestroy() {
            window.removeEventListener('resize', this.handleResize);
        },
    }
</script>