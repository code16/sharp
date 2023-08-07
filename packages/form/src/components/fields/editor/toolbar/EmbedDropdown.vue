<script setup lang="ts">
    import { __ } from "@/utils/i18n";
</script>

<template>
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

        <template v-slot:default="{ hide }">
            <template v-for="embed in embeds">
                <DropdownItem @click="handleClicked(embed)">
                    {{ embed.label }}
                </DropdownItem>
            </template>
        </template>
    </Dropdown>
</template>

<script lang="ts">
    import { Dropdown, DropdownItem } from "@sharp/ui";

    export default {
        components: {
            Dropdown,
            DropdownItem,
        },
        props: {
            embeds: Array,
            editor: Object,
        },
        methods: {
            handleClicked(embed) {
                this.editor.chain()
                    .focus()
                    .insertEmbed({ embedKey: embed.key })
                    .run();
            },
        },
    }
</script>
