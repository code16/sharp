<template>
    <div class="editor__toolbar">
        <div class="row row-cols-auto g-2">
            <template v-for="group in toolbarGroups">
                <div class="btn-group">
                    <template v-for="button in group">
                        <template v-if="button === 'link'">
                            <LinkDropdown
                                :id="id"
                                :active="isActive(button)"
                                :title="buttonTitle(button)"
                                :editor="editor"
                                :disabled="disabled"
                                @submit="handleLinkSubmitted"
                                @remove="handleRemoveLinkClicked"
                            >
                                <i :class="getIcon(button)"></i>
                            </LinkDropdown>
                        </template>
                        <template v-else-if="button === 'table'">
                            <TableDropdown
                                :active="isActive(button)"
                                :disabled="disabled"
                                :editor="editor"
                            >
                                <i :class="getIcon(button)"></i>
                            </TableDropdown>
                        </template>
                        <template v-else>
                            <Button
                                variant="light"
                                :active="isActive(button)"
                                :disabled="disabled"
                                :title="buttonTitle(button)"
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
    </div>
</template>

<script>
    import { Button, Dropdown } from "sharp-ui";
    import { buttons } from './config';
    import LinkDropdown from "./LinkDropdown";
    import TableDropdown from "./TableDropdown";

    export default {
        components: {
            TableDropdown,
            LinkDropdown,
            Button,
            Dropdown,
        },
        props: {
            id: String,
            editor: Object,
            toolbar: Array,
            disabled: Boolean,
        },
        computed: {
            toolbarGroups() {
                return this.toolbar
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
                return buttons[button]?.isActive?.(this.editor);
            },
            buttonTitle(button) {
                return buttons[button]?.label;
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
