<script setup lang="ts">
    import {FormEditorFieldData, FormEditorToolbarButton} from "@/types";
    import { FormFieldProps } from "@/form/components/types";
    import { Editor } from "@tiptap/vue-3";
    import LinkDropdown from "./LinkDropdown.vue";
    import TableDropdown from "./TableDropdown.vue";
    import OptionsDropdown from "./OptionsDropdown.vue";
    import EmbedDropdown from "./EmbedDropdown.vue";
    import { Button, Dropdown } from "@/components/ui";
    import { buttons } from './config';
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

    const toolbarGroups = computed<FormEditorToolbarButton[][]>(() =>
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
                                :active="buttons[button].isActive(editor)"
                                :title="buttons[button].label()"
                                :editor="editor"
                                :disabled="field.readOnly"
                                @submit="buttons[button].command(editor)"
                                @remove="editor.chain().focus().unsetLink().run()"
                            >
                                <i :class="buttons[button].icon" data-test="link"></i>
                            </LinkDropdown>
                        </template>
                        <template v-else-if="button === 'table'">
                            <TableDropdown
                                :active="buttons[button].isActive(editor)"
                                :disabled="field.readOnly"
                                :editor="editor"
                            >
                                <i :class="buttons[button].icon" data-test="table"></i>
                            </TableDropdown>
                        </template>
                        <template v-else :key="button">
                            <Button
                                variant="light"
                                :active="buttons[button].isActive(editor)"
                                :disabled="field.readOnly"
                                :title="buttons[button].label()"
                                @click="buttons[button].command(editor)"
                                :data-test="button"
                            >
                                <i :class="buttons[button].icon"></i>
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
