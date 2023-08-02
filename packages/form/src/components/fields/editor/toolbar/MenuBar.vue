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
                                <i :class="getIcon(button)" data-test="link"></i>
                            </LinkDropdown>
                        </template>
                        <template v-else-if="button === 'table'">
                            <TableDropdown
                                :active="isActive(button)"
                                :disabled="disabled"
                                :editor="editor"
                            >
                                <i :class="getIcon(button)" data-test="table"></i>
                            </TableDropdown>
                        </template>
                        <template v-else :key="button">
                            <Button
                                variant="light"
                                :active="isActive(button)"
                                :disabled="disabled"
                                :title="buttonTitle(button)"
                                @click="handleClicked(button)"
                                :data-test="button"
                            >
                                <i :class="getIcon(button)"></i>
                                <template v-if="button === 'small'">
                                    <i class="fas fa-font fa-xs" style="margin-top: .25em"></i>
                                </template>
                            </Button>
                        </template>
                    </template>
                </div>
            </template>
            <template v-if="options && options.length > 0">
                <div class="btn-group">
                    <OptionsDropdown :options="options" :editor="editor" />
                </div>
            </template>
            <template v-if="customEmbeds && customEmbeds.length > 0">
                <div class="btn-group">
                    <EmbedDropdown :embeds="customEmbeds" :editor="editor" />
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import { Button, Dropdown } from "@sharp/ui";
    import { buttons } from './config';
    import LinkDropdown from "./LinkDropdown.vue";
    import TableDropdown from "./TableDropdown.vue";
    import OptionsDropdown from "./OptionsDropdown.vue";
    import EmbedDropdown from "./EmbedDropdown.vue";

    export default {
        components: {
            EmbedDropdown,
            TableDropdown,
            LinkDropdown,
            OptionsDropdown,
            Button,
            Dropdown,
        },
        props: {
            id: String,
            editor: Object,
            toolbar: Array,
            disabled: Boolean,
            options: Array,
            embeds: Object,
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
            customEmbeds() {
                const { upload, ...customEmbeds } = this.embeds ?? {};
                return Object.values(customEmbeds);
            },
        },
        methods: {
            getIcon(button) {
                return buttons()[button]?.icon;
            },
            isActive(button) {
                return buttons()[button]?.isActive?.(this.editor);
            },
            buttonTitle(button) {
                return buttons()[button]?.label;
            },
            handleClicked(button) {
                buttons()[button]?.command(this.editor);
            },
            handleLinkSubmitted({ href, label }) {
                buttons().link.command(this.editor, { href, label });
            },
            handleRemoveLinkClicked() {
                this.editor.chain().focus().unsetLink().run();
            },
        },
    }
</script>
