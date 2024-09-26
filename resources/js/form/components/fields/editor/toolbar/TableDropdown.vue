<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Editor } from "@tiptap/vue-3";
    import { Dropdown, DropdownItem, DropdownSeparator } from "@/components/ui";

    defineProps<{
        editor: Editor,
        active: boolean,
        disabled: boolean,
    }>()
</script>

<template>
    <Dropdown
        class="editor__dropdown editor__dropdown--table"
        variant="light"
        :active="active"
        :disabled="disabled"
        v-bind="$attrs"
        ref="dropdown"
    >
        <template v-slot:text>
            <slot />
        </template>

        <template v-slot:default>
            <DropdownItem
                :disabled="editor.isActive('table')"
                @click="editor.chain().focus().insertTable().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.insert_table') }}
            </DropdownItem>
            <DropdownSeparator />
            <DropdownItem
                :disabled="!editor.can().toggleHeaderCell()"
                @click="editor.chain().focus().toggleHeaderCell().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.toggle_header_cell') }}
            </DropdownItem>
            <DropdownSeparator />
            <DropdownItem
                :disabled="!editor.can().addRowBefore()"
                @click="editor.chain().focus().addRowBefore().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.insert_row_above') }}
            </DropdownItem>
            <DropdownItem
                :disabled="!editor.can().addRowAfter()"
                @click="editor.chain().focus().addRowAfter().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.insert_row_below') }}
            </DropdownItem>
            <DropdownItem
                :disabled="!editor.can().deleteRow()"
                @click="editor.chain().focus().deleteRow().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.remove_row') }}
            </DropdownItem>
            <DropdownSeparator />
            <DropdownItem
                :disabled="!editor.can().addColumnBefore()"
                @click="editor.chain().focus().addColumnBefore().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.insert_col_left') }}
            </DropdownItem>
            <DropdownItem
                :disabled="!editor.can().addColumnAfter()"
                @click="editor.chain().focus().addColumnAfter().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.insert_col_right') }}
            </DropdownItem>
            <DropdownItem
                :disabled="!editor.can().deleteColumn()"
                @click="editor.chain().focus().deleteColumn().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.remove_col') }}
            </DropdownItem>
            <DropdownSeparator />
            <DropdownItem
                :disabled="!editor.can().deleteTable()"
                @click="editor.chain().focus().deleteTable().run()"
            >
                {{ __('sharp::form.editor.dropdown.table.remove_table') }}
            </DropdownItem>
        </template>
    </Dropdown>
</template>
