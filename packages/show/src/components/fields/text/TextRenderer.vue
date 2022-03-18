<template>
    <component :is="component" />
</template>

<script>
    import { postResolveFiles, defaultFileThumbnailHeight, defaultFileThumbnailWidth } from "sharp-files";
    import { postResolveEmbeds } from "sharp-embeds";
    import { createEmbedComponent } from "./nodes/embed";
    import File from "./nodes/File";
    import Html from "./nodes/Html";

    export default {
        props: {
            content: String,
            embeds: Object,
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
                    embeds: {},
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
                        ...Object.values(this.embeds ?? {}).reduce((res, embedOptions) => ({
                            ...res,
                            [embedOptions.tag]: createEmbedComponent(embedOptions),
                        }), {}),
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
                const entityKey = this.$store.getters['show/entityKey'];
                const instanceId = this.$store.getters['show/instanceId'];
                const files = this.state.files;

                if(files.length > 0) {
                    this.state.files = await postResolveFiles({
                        entityKey,
                        instanceId,
                        files,
                        thumbnailWidth: defaultFileThumbnailWidth,
                        thumbnailHeight: defaultFileThumbnailHeight,
                    });
                }

                Object.entries(this.state.embeds)
                    .filter(([embedKey, embeds]) => embeds.length > 0)
                    .forEach(async ([embedKey, embeds]) => {
                        this.state.embeds = {
                            ...this.state.embeds,
                            [embedKey]: await postResolveEmbeds({
                                entityKey,
                                instanceId,
                                embedKey,
                                embeds,
                            }),
                        }
                    });
            },
            initState() {
                this.state.files = [];
                this.state.embeds = Object.fromEntries(
                    Object.entries(this.embeds ?? {}).map(([embedKey]) => [
                        embedKey,
                        []
                    ])
                );
            },
            async handleContentChanged() {
                this.initState();
                await this.$nextTick();
                await this.init();
            },
        },
        created() {
            this.initState()
        },
        mounted() {
            this.init();
        },
    }
</script>
