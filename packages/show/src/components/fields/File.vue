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
                        <i :class="iconClass"></i>
                    </div>
                </template>
            </div>
            <div class="col px-2" style="min-width: 0">
                <div class="ShowFileField__name text-truncate mb-2">
                    {{ fileName }}
                </div>
                <div class="ShowFileField__info">
                    <div class="row mx-n2 h-100">
                        <template v-if="sizeLabel">
                            <div class="col-auto px-2">
                                <div class="ShowFileField__size text-muted">
                                    {{ sizeLabel }}
                                </div>
                            </div>
                        </template>
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
    import { downloadFileUrl } from "sharp-files";
    import { syncVisibility } from "../../util/fields/visiblity";
    import FieldLayout from "../FieldLayout.vue";

    export default {
        components: {
            Button,
            FieldLayout,
        },
        props: {
            value: Object,
            label: String,
            collapsed: {
                type: Boolean,
                default: true,
            },
            root: {
                type: Boolean,
                default: true,
            },
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
                    'ShowFileField--has-placeholder': !this.hasThumbnail,
                    'ShowFileField--root': this.root,
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
                    disk: this.value?.disk,
                    path: this.value?.path,
                })
            },
            fileName() {
                return this.value?.name ?? '';
            },
            hasThumbnail() {
                return !!this.thumbnailUrl;
            },
            thumbnailUrl() {
                return this.value?.thumbnail;
            },
            thumbnailStyle() {
                return {
                    'max-height': this.thumbnailWidth
                        ? `${this.thumbnailWidth}px`
                        : null,
                }
            },
            sizeLabel() {
                return this.value?.size
                    ? filesizeLabel(this.value.size)
                    : null;
            },
            iconClass() {
                const extension = this.fileName.split('.').pop();
                const iconClass = getClassNameForExtension(extension);

                if(iconClass === 'fa-file-csv') {
                    return `fas ${iconClass}`;
                }
                return `far ${iconClass}`;
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
