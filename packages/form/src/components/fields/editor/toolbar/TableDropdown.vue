<script setup lang="ts">
    import { __ } from "@/utils/i18n";
</script>

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
                {{ __('sharp::form.editor.dropdown.table.insert_table') }}
            </DropdownItem>
            <DropdownSeparator />
            <DropdownItem
                :disabled="!editor.can().addRowBefore()"
                @click="command(() => editor.chain().addRowBefore().run())"
            >
                {{ __('sharp::form.editor.dropdown.table.insert_row_above') }}
            </DropdownItem>
            <DropdownItem
                :disabled="!editor.can().addRowAfter()"
                @click="command(() => editor.chain().addRowAfter().run())"
            >
                {{ __('sharp::form.editor.dropdown.table.insert_row_below') }}
            </DropdownItem>
            <DropdownItem
                :disabled="!editor.can().deleteRow()"
                @click="command(() => editor.chain().deleteRow().run())"
            >
                {{ __('sharp::form.editor.dropdown.table.remove_row') }}
            </DropdownItem>
            <DropdownSeparator />
            <DropdownItem
                :disabled="!editor.can().addColumnBefore()"
                @click="command(() => editor.chain().addColumnBefore().run())"
            >
                {{ __('sharp::form.editor.dropdown.table.insert_col_left') }}
            </DropdownItem>
            <DropdownItem
                :disabled="!editor.can().addColumnAfter()"
                @click="command(() => editor.chain().addColumnAfter().run())"
            >
                {{ __('sharp::form.editor.dropdown.table.insert_col_right') }}
            </DropdownItem>
            <DropdownItem
                :disabled="!editor.can().deleteColumn()"
                @click="command(() => editor.chain().deleteColumn().run())"
            >
                {{ __('sharp::form.editor.dropdown.table.remove_col') }}
            </DropdownItem>
            <DropdownSeparator />
            <DropdownItem
                :disabled="!editor.can().deleteTable()"
                @click="command(() => editor.chain().deleteTable().run())"
            >
                {{ __('sharp::form.editor.dropdown.table.remove_table') }}
            </DropdownItem>
        </template>
    </Dropdown>
</template>

<script lang="ts">
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
