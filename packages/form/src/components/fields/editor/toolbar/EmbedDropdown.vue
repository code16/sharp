<template>
    <Dropdown
        class="editor__dropdown"
        variant="light"
        small
        v-bind="$attrs"
        ref="dropdown"
    >
        <template v-slot:text>
            {{ lang('form.editor.dropdown.embeds') }}
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

<script>
    import { lang } from 'sharp';
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
            lang,
            handleClicked(embed) {
                this.editor.chain()
                    .focus()
                    .insertEmbed({ embedKey: embed.key })
                    .run();
            },
        },
    }
</script>
