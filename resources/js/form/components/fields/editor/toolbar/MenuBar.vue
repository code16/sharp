<script setup lang="ts">
    import { FormEditorFieldData } from "@/types";
    import { FormFieldProps } from "@/form/components/types";
    import { Editor } from "@tiptap/vue-3";
    import { computed } from "vue";
    import { config } from "@/utils/config";
    import { __ } from "@/utils/i18n";

    const props = defineProps<FormFieldProps<FormEditorFieldData> & {
        editor: Editor,
    }>();

    const options = computed(() => {
        const hasList = props.field.toolbar?.some(button => button === 'bullet-list' || button === 'ordered-list');
        if(props.field.markdown && !config('sharp.markdown_editor.tight_lists_only') && hasList) {
            return [
                {
                    command: () => props.editor.chain().toggleTight().run(),
                    disabled: !props.editor.can().toggleTight(),
                    label: __('sharp::form.editor.dropdown.options.toggle_tight_list'),
                }
            ];
        }
    });

    const toolbarGroups = computed(() =>
        props.field.toolbar.reduce((res, btn) => {
            if(btn === '|') {
                return [...res, []];
            }
            res[res.length - 1].push(btn);
            return res;
        }, [[]])
    );

    const customEmbeds = computed(() => {
        const { upload, ...customEmbeds } = props.field.embeds ?? {};
        return Object.values(customEmbeds);
    });
</script>

<template>
    <div class="editor__toolbar">
        <div class="row row-cols-auto g-2">
            <template v-for="group in toolbarGroups">
                <div class="btn-group">
                    <template v-for="button in group">
                        <template v-if="button === 'link'">
                            <LinkDropdown
                                :id="fieldErrorKey"
                                :active="isActive(button)"
                                :title="buttonTitle(button)"
                                :editor="editor"
                                :disabled="field.readOnly"
                                @submit="handleLinkSubmitted"
                                @remove="handleRemoveLinkClicked"
                            >
                                <i :class="getIcon(button)" data-test="link"></i>
                            </LinkDropdown>
                        </template>
                        <template v-else-if="button === 'table'">
                            <TableDropdown
                                :active="isActive(button)"
                                :disabled="field.readOnly"
                                :editor="editor"
                            >
                                <i :class="getIcon(button)" data-test="table"></i>
                            </TableDropdown>
                        </template>
                        <template v-else :key="button">
                            <Button
                                variant="light"
                                :active="isActive(button)"
                                :disabled="field.readOnly"
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
            <template v-if="field.embeds && customEmbeds.length > 0">
                <div class="btn-group">
                    <EmbedDropdown :embeds="customEmbeds" :editor="editor" />
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import { Button, Dropdown } from "@/components/ui";
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
        // props: {
        //     id: String,
        //     editor: Object,
        //     toolbar: Array,
        //     disabled: Boolean,
        //     options: Array,
        //     embeds: Object,
        // },
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
