<template>
    <component
        :is="filterComponent"
        :filter-key="filter.key"
        :value="value"
        :disabled="disabled"
        v-bind="filter"
        @input="handleInput"
    />
</template>

<script>
    import { filterByType } from "./filters";

    export default {
        name: 'SharpFilter',

        props: {
            filter: {
                type: Object,
                required: true,
            },
            value: [Object, Array, String, Number],
            disabled: Boolean,
        },

        computed: {
            filterComponent() {
                return filterByType(this.filter.type);
            },
        },

        methods: {
            handleInput(value) {
                this.$emit('input', value);
            },
        }
    }
</script>
