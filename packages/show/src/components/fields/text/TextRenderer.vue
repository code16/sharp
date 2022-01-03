<template>
    <component :is="component" />
</template>

<script>
    import { postResolveFiles, defaultFileThumbnailHeight, defaultFileThumbnailWidth } from "sharp-files";
    import File from "./nodes/File";
    import Html from "./nodes/Html";

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
                    template: `<div>${this.formattedContent}</div>`,
                    components: {
                        'x-sharp-file': File,
                        'x-sharp-image': File,
                        'html-content': Html,
                    },
                }
            },
            formattedContent() {
                const dom = document.createElement('template');
                dom.innerHTML = this.content;
                dom.content.querySelectorAll('[data-html-content]').forEach(htmlNode => {
                    const component = document.createElement('html-content');
                    component.setAttribute('content', htmlNode.innerHTML.trim());
                    dom.content.insertBefore(component, htmlNode);
                    dom.content.removeChild(htmlNode);
                });
                return dom.innerHTML;
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
