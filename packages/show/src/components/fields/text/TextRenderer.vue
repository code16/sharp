<template>
    <component :is="component" />
</template>

<script>
    import { postResolveFiles, defaultFileThumbnailHeight, defaultFileThumbnailWidth } from "sharp-files";
    import Media from "./nodes/Media";

    export default {
        props: {
            content: String,
        },
        provide() {
            return {
                state: this.state,
            }
        },
        data() {
            return {
                state: {
                    files: [],
                },
            }
        },
        watch: {
            content: 'handleContentChanged',
        },
        computed: {
            component() {
                return {
                    template: `<div>${this.content}</div>`,
                    components: {
                        'x-sharp-media': Media,
                    },
                }
            },
        },
        methods: {
            async init() {
                const files = this.state.files;
                if(files.length > 0) {
                    this.state.files = await postResolveFiles({
                        entityKey: this.$store.getters['show/entityKey'],
                        instanceId: this.$store.getters['show/instanceId'],
                        files,
                        thumbnailWidth: defaultFileThumbnailWidth,
                        thumbnailHeight: defaultFileThumbnailHeight,
                    });
                }
            },
            async handleContentChanged() {
                this.state.files = [];
                await this.$nextTick();
                await this.init();
            },
        },
        mounted() {
            this.init();
        },
    }
</script>
