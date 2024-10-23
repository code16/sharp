<script  lang="ts" setup>
    import { Dropdown, DropdownItem } from "@/components/ui";
    import { Editor } from "@tiptap/vue-3";

    defineProps<{
        editor: Editor,
        options: Array<{
            command: () => void,
            disabled: boolean,
            label: string,
        }>,
    }>();
</script>

<template>
    <Dropdown
        class="editor__dropdown"
        variant="light"
        small
        :disabled="options.every(option => option.disabled)"
    >
        <template v-slot:text>
            Options
        </template>

        <template v-slot:default>
            <template v-for="option in options">
                <DropdownItem
                    :disabled="option.disabled"
                    @click="option.command()"
                >
                    {{ option.label }}
                </DropdownItem>
            </template>
        </template>
    </Dropdown>
</template>
