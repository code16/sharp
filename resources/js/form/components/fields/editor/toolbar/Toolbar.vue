<script setup lang="ts">
    import { FormEditorFieldData, FormEditorToolbarButton } from "@/types";
    import { Editor } from "@tiptap/vue-3";
    import LinkDropdown from "./LinkDropdown.vue";
    import TableDropdown from "./TableDropdown.vue";
    import { buttons } from './config';
    import { computed } from "vue";
    import { __ } from "@/utils/i18n";
    import { FormFieldProps } from "@/form/types";
    import Icon from "@/components/ui/Icon.vue";
    import { Toggle } from "@/components/ui/toggle";
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import { Button } from "@/components/ui/button";
    import { Separator } from "@/components/ui/separator";

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
    <div class="flex gap-x-1.5 gap-y-0 flex-wrap">
        <template v-for="group in toolbarGroups">
            <div class="flex flex-wrap gap-x-1">
                <template v-for="button in group">
                    <template v-if="button === 'link'">
                        <LinkDropdown v-bind="props" />
                    </template>
                    <template v-else-if="button === 'table'">
                        <TableDropdown v-bind="props" />
                    </template>
                    <template v-else-if="button.startsWith('embed:')">
                        <Toggle
                            size="sm"
                            :model-value="props.editor.isActive(button)"
                            :disabled="props.field.readOnly"
                            :title="props.field.embeds[button.replace('embed:', '')]?.label"
                            @click="$emit('embed', props.field.embeds[button.replace('embed:', '')])"
                        >
                            <Icon :icon="field.embeds[button.replace('embed:', '')]?.icon" class="size-4" />
                        </Toggle>
                    </template>
                    <template v-else :key="button">
                        <Toggle
                            size="sm"
                            :model-value="buttons[button].isActive(editor)"
                            :disabled="field.readOnly"
                            :title="buttons[button].label()"
                            @click="button === 'upload' || button === 'upload-image'
                                ? $emit('upload')
                                : buttons[button].command(editor)"
                            :data-test="button"
                        >
                            <component :is="buttons[button].icon" class="size-4" />
                        </Toggle>
                    </template>
                </template>
            </div>
            <Separator orientation="vertical" class="h-4 self-center last:hidden" />
        </template>
        <template v-if="Object.values(props.field.embeds ?? {}).length > 0">
            <DropdownMenu :modal="false">
                <DropdownMenuTrigger as-child>
                    <Button class="px-3" variant="ghost" :disabled="props.field.readOnly">
                        {{ __('sharp::form.editor.dropdown.embeds') }}
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent>
                    <template v-for="embed in props.field.embeds">
                        <DropdownMenuItem @click="$emit('embed', embed)">
                            {{ embed.label }}
                        </DropdownMenuItem>
                    </template>
                </DropdownMenuContent>
            </DropdownMenu>
        </template>
    </div>
</template>
