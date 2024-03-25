<script setup lang="ts">
    import { EmbedData, FormEditorFieldData, FormEditorToolbarButton } from "@/types";
    import { Editor } from "@tiptap/vue-3";
    import LinkDropdown from "./LinkDropdown.vue";
    import TableDropdown from "./TableDropdown.vue";
    import { Button, Dropdown, DropdownItem } from "@/components/ui";
    import { buttons } from './config';
    import { computed } from "vue";
    import { config } from "@/utils/config";
    import { __ } from "@/utils/i18n";
    import { ContentEmbedManager } from "@/content/ContentEmbedManager";
    import { FormFieldProps } from "@/form/types";

    const props = defineProps<FormFieldProps<FormEditorFieldData> & {
        editor: Editor,
    }>();

    const toolbarGroups = computed<FormEditorToolbarButton[][]>(() =>
        props.field.toolbar.reduce((res, btn) => {
            if(btn === '|') {
                return [...res, []];
            }
            res[res.length - 1].push(btn);
            return res;
        }, [[]])
    );
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
                        <template v-else-if="button.startsWith('embed:')">
                            <Button
                                variant="light"
                                :active="editor.isActive(button)"
                                :disabled="field.readOnly"
                                :title="field.embeds[button.replace('embed:', '')]?.label"
                                @click="editor.commands.insertEmbed(field.embeds[button.replace('embed:', '')])"
                                :data-test="button"
                            >
                                <i :class="field.embeds[button.replace('embed:', '')].icon"></i>
                            </Button>
                        </template>
                        <template v-else :key="button">
                            <Button
                                variant="light"
                                :active="buttons[button].isActive(editor)"
                                :disabled="field.readOnly"
                                :title="buttons[button].label()"
                                @click="button === 'upload' || button === 'upload-image' ? $emit('upload') : buttons[button].command(editor)"
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
            <template v-if="Object.values(props.field.embeds ?? {}).length > 0">
                <div class="btn-group">
                    <Dropdown
                        class="editor__dropdown"
                        variant="light"
                        small
                        v-bind="$attrs"
                        ref="dropdown"
                    >
                        <template v-slot:text>
                            {{ __('sharp::form.editor.dropdown.embeds') }}
                        </template>

                        <template v-slot:default>
                            <template v-for="embed in props.field.embeds">
                                <DropdownItem @click="editor.chain().insertEmbed(embed).run()">
                                    {{ embed.label }}
                                </DropdownItem>
                            </template>
                        </template>
                    </Dropdown>
                </div>
            </template>
        </div>
    </div>
</template>
