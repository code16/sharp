<template>
    <SharpDropdown class="SharpForm__locale-dropdown">
        <template slot="text">
            <template v-if="locale">{{ locale }}</template>
            <template v-else>-</template>
        </template>
        <template v-for="locale in locales">
            <SharpDropdownItem @click="handleChanged(locale)" :key="locale">
                {{ locale }}
            </SharpDropdownItem>
        </template>
    </SharpDropdown>
</template>

<script>
    import { SharpDropdown, SharpDropdownItem } from "../ui";

    export default {
        components: {
            SharpDropdown,
            SharpDropdownItem,
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