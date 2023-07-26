<template>
    <Dropdown
        class="editor__dropdown editor__dropdown--table"
        variant="light"
        :active="active"
        v-bind="$attrs"
        ref="dropdown"
    >
        <template v-slot:text>
            <slot />
        </template>

        <template v-slot:default="{ hide }">
            <DropdownItem
                :disabled="editor.isActive('table')"
                @click="handleInsertClicked"
            >
                {{ lang('form.editor.dropdown.table.insert_table', 'Insert table') }}
            </DropdownItem>
            <DropdownSeparator />
            <DropdownItem
                :disabled="!editor.can().addRowBefore()"
                @click="command(() => editor.chain().addRowBefore().run())"
            >
                {{ lang('form.editor.dropdown.table.insert_row_above', 'Insert row above') }}
            </DropdownItem>
            <DropdownItem
                :disabled="!editor.can().addRowAfter()"
                @click="command(() => editor.chain().addRowAfter().run())"
            >
                {{ lang('form.editor.dropdown.table.insert_row_below', 'Insert row below') }}
            </DropdownItem>
            <DropdownItem
                :disabled="!editor.can().deleteRow()"
                @click="command(() => editor.chain().deleteRow().run())"
            >
                {{ lang('form.editor.dropdown.table.remove_row', 'Remove row') }}
            </DropdownItem>
            <DropdownSeparator />
            <DropdownItem
                :disabled="!editor.can().addColumnBefore()"
                @click="command(() => editor.chain().addColumnBefore().run())"
            >
                {{ lang('form.editor.dropdown.table.insert_col_left', 'Insert column to the left') }}
            </DropdownItem>
            <DropdownItem
                :disabled="!editor.can().addColumnAfter()"
                @click="command(() => editor.chain().addColumnAfter().run())"
            >
                {{ lang('form.editor.dropdown.table.insert_col_right', 'Insert column to the right') }}
            </DropdownItem>
            <DropdownItem
                :disabled="!editor.can().deleteColumn()"
                @click="command(() => editor.chain().deleteColumn().run())"
            >
                {{ lang('form.editor.dropdown.table.remove_col', 'Remove column') }}
            </DropdownItem>
            <DropdownSeparator />
            <DropdownItem
                :disabled="!editor.can().deleteTable()"
                @click="command(() => editor.chain().deleteTable().run())"
            >
                {{ lang('form.editor.dropdown.table.remove_table', 'Remove table') }}
            </DropdownItem>
        </template>
    </Dropdown>
</template>

<script>
    import { lang } from 'sharp';
    import { Button, Dropdown, DropdownItem, DropdownSeparator } from "@sharp/ui";

    export default {
        components: {
            Button,
            Dropdown,
            DropdownItem,
            DropdownSeparator,
        },
        props: {
            id: String,
            active: Boolean,
            editor: Object,
        },
        data() {
            return {
            }
        },
        methods: {
            lang,
            handleInsertClicked() {
                this.command(() => {
                    this.editor.chain()
                        .focus()
                        .insertTable()
                        .run();
                });
            },
            command(run) {
                run();
                setTimeout(() => {
                    this.editor.chain()
                        .focus()
                        .run();
                }, 0);
            }
        }
    }
</script>
