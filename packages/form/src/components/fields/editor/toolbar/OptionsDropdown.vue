<template>
    <Dropdown
        class="editor__dropdown editor__dropdown--list"
        variant="light"
        small
        :disabled="disabled"
        v-bind="$attrs"
        ref="dropdown"
    >
        <template v-slot:text>
            Options
        </template>

        <template v-slot:default="{ hide }">
            <template v-for="option in options">
                <DropdownItem
                    :disabled="option.disabled"
                    @click="runCommand(option)"
                >
                    {{ option.label }}
                </DropdownItem>
            </template>
        </template>
    </Dropdown>
</template>

<script>
    import { Button, Dropdown, DropdownItem } from "sharp-ui";

    export default {
        components: {
            Button,
            Dropdown,
            DropdownItem,
        },
        props: {
            id: String,
            editor: Object,
            options: Array,
        },
        data() {
            return {
            }
        },
        computed: {
            disabled() {
                return this.options.every(option => option.disabled);
            },
        },
        methods: {
            runCommand(option) {
                option.command();
                setTimeout(() => {
                    this.editor.chain()
                        .focus()
                        .run();
                }, 0);
            }
        }
    }
</script>
