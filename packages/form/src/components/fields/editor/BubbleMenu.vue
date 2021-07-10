<template>
    <div style="visibility: hidden">
        <div class="card shadow border-0">
            <div class="card-body" style="padding: .75rem">
                <MenuBar
                    :id="toolbarId"
                    :editor="editor"
                    :toolbar="toolbar"
                    bubble-menu
                />
            </div>
        </div>
    </div>
</template>

<script>
    import MenuBar from "./toolbar/MenuBar";
    import { getNavbarHeight } from "sharp-ui";
    import { BubbleMenuPlugin } from "./plugins/bubble-menu-plugin";


    export default {
        components: {
            MenuBar,
        },
        props: {
            id: String,
            editor: Object,
            toolbar: Array,
            ignoredExtensions: Array,
        },
        watch: {
            editor: {
                immediate: true,
                async handler(editor) {
                    if (!editor) {
                        return
                    }
                    await this.$nextTick();
                    editor.registerPlugin(BubbleMenuPlugin({
                        editor,
                        element: this.$el,
                        tippyOptions: this.tippyOptions,
                        ignoredExtensions: this.ignoredExtensions,
                    }));
                }
            }
        },
        computed: {
            toolbarId() {
                return `${this.id}-bubble`;
            },
            tippyOptions() {
                return {
                    popperOptions: {
                        modifiers: [
                            {
                                name: 'flip',
                                options: {
                                    padding: {
                                        top: getNavbarHeight() + 10,
                                    },
                                },
                            },
                        ]
                    }
                }
            },
        }
    }
</script>
