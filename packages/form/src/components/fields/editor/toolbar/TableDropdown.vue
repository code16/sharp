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
            <template v-if="active">

            </template>
            <template v-else>
                <b-dropdown-item @click="handleInsertClicked">
                    Insert table
                </b-dropdown-item>
            </template>
        </template>
    </Dropdown>
</template>

<script>
    import { BDropdownItem } from 'bootstrap-vue';
    import { Button, Dropdown } from "sharp-ui";

    export default {
        components: {
            Button,
            Dropdown,
            BDropdownItem,
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
                this.editor.commands.insertTable();
                setTimeout(() => {
                    this.editor.commands.focus();
                }, 0);
            },
        }
    }
</script>
