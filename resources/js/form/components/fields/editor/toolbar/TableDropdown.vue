<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Editor } from "@tiptap/vue-3";
    import { FormFieldProps } from "@/form/types";
    import { FormEditorFieldData } from "@/types";
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem, DropdownMenuSeparator,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import { Toggle } from "@/components/ui/toggle";
    import { TableIcon } from "lucide-vue-next";

    const props = defineProps<FormFieldProps<FormEditorFieldData> & {
        editor: Editor,
    }>()
</script>

<template>
    <DropdownMenu :modal="false">
        <DropdownMenuTrigger as-child>
            <Toggle
                :model-value="props.editor.isActive('table')"
                :disabled="props.field.readOnly"
                :title="__('sharp::form.editor.toolbar.table.title')"
            >
                <TableIcon class="size-4" />
            </Toggle>
        </DropdownMenuTrigger>
        <DropdownMenuContent>
            <DropdownMenuItem
                :disabled="props.editor.isActive('table')"
                @click="props.editor.chain().focus().insertTable().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.insert_table') }}
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem
                :disabled="!props.editor.can().toggleHeaderCell()"
                @click="props.editor.chain().focus().toggleHeaderCell().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.toggle_header_cell') }}
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem
                :disabled="!props.editor.can().addRowBefore()"
                @click="props.editor.chain().focus().addRowBefore().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.insert_row_above') }}
            </DropdownMenuItem>
            <DropdownMenuItem
                :disabled="!props.editor.can().addRowAfter()"
                @click="props.editor.chain().focus().addRowAfter().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.insert_row_below') }}
            </DropdownMenuItem>
            <DropdownMenuItem
                :disabled="!props.editor.can().deleteRow()"
                @click="props.editor.chain().focus().deleteRow().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.remove_row') }}
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem
                :disabled="!props.editor.can().addColumnBefore()"
                @click="props.editor.chain().focus().addColumnBefore().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.insert_col_left') }}
            </DropdownMenuItem>
            <DropdownMenuItem
                :disabled="!props.editor.can().addColumnAfter()"
                @click="props.editor.chain().focus().addColumnAfter().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.insert_col_right') }}
            </DropdownMenuItem>
            <DropdownMenuItem
                :disabled="!props.editor.can().deleteColumn()"
                @click="props.editor.chain().focus().deleteColumn().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.remove_col') }}
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem
                :disabled="!props.editor.can().deleteTable()"
                @click="props.editor.chain().focus().deleteTable().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.remove_table') }}
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
