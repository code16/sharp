<template>
    <BubbleMenu :editor="editor" :tippy-options="tippyOptions">
        <div class="card shadow border-0">
            <div class="card-body" style="padding: .75rem">
                <MenuBar :editor="editor" :toolbar="toolbar" bubble-menu />
            </div>
        </div>
    </BubbleMenu>
</template>

<script>
    import { BubbleMenu } from '@tiptap/vue-2';
    import MenuBar from "./MenuBar";
    import { getNavbarHeight } from "sharp-ui";

    export default {
        components: {
            MenuBar,
            BubbleMenu,
        },
        props: {
            editor: Object,
            toolbar: Array,
        },
        computed: {
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
