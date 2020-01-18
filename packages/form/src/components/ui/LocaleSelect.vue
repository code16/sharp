<template>
    <Dropdown class="SharpForm__locale-dropdown">
        <template slot="text">
            <template v-if="locale">{{ locale }}</template>
            <template v-else>-</template>
        </template>
        <template v-for="locale in locales">
            <DropdownItem @click="handleChanged(locale)" :key="locale">
                {{ locale }}
            </DropdownItem>
        </template>
    </Dropdown>
</template>

<script>
    import { Dropdown, DropdownItem } from "sharp/components";

    export default {
        components: {
            Dropdown,
            DropdownItem,
        },
        props: {
            locale: String,
            locales: Array,
        },
        methods: {
            handleChanged(locale) {
                this.$emit('change', locale);
            }
        },
    }
</script>