<template>
    <div class="editor__toolbar">
        <template v-for="group in toolbarGroups">
            <div class="btn-group">
                <template v-for="button in group">
                    <template v-if="button === 'link'">
                        <LinkDropdown
                            :id="id"
                            :active="isActive(button)"
                            :editor="editor"
                            :dropup="bubbleMenu"
                            :disabled="disabled"
                            @submit="handleLinkSubmitted"
                            @remove="handleRemoveLinkClicked"
                        >
                            <i :class="getIcon(button)"></i>
                        </LinkDropdown>
                    </template>
                    <template v-else>
                        <Button
                            variant="light"
                            :active="isActive(button)"
                            :disabled="disabled"
                            @click="handleClicked(button)"
                            :key="button"
                        >
                            <i :class="getIcon(button)"></i>
                        </Button>
                    </template>
                </template>
            </div>
        </template>
    </div>
</template>

<script>
    import { Button, Dropdown } from "sharp-ui";
    import { buttons } from './config';
    import LinkDropdown from "./LinkDropdown";

    export default {
        components: {
            LinkDropdown,
            Button,
            Dropdown,
        },
        props: {
            id: String,
            editor: Object,
            toolbar: Array,
            bubbleMenu: Boolean,
            disabled: Boolean,
        },
        computed: {
            toolbarGroups() {
                return this.toolbar
                    .filter(button => {
                        if(this.bubbleMenu) {
                            return buttons[button]?.bubbleMenu;
                        }
                        return true;
                    })
                    .reduce((res, btn) => {
                        if(btn === '|') {
                            return [...res, []];
                        }
                        res[res.length - 1].push(btn);
                        return res;
                    }, [[]]);
            },
        },
        methods: {
            getIcon(button) {
                return buttons[button]?.icon;
            },
            isActive(button) {
                return buttons[button]?.isActive(this.editor);
            },
            handleClicked(button) {
                buttons[button]?.command(this.editor);
            },
            handleLinkSubmitted({ href, label }) {
                buttons.link.command(this.editor, { href, label });
            },
            handleRemoveLinkClicked() {
                this.editor.chain().focus().unsetLink().run();
            },
        },
    }
</script>
