<template>
    <div class="SharpFilter">
        <component
            :is="filterComp"
            :value="value"
            v-bind="filter"
            @input="handleInput"
        />
    </div>
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
        },

        computed: {
            filterComp() {
                return filterByType(this.filter.type);
            },
            label() {
                return this.field.label;
            },
        },

        methods: {
            handleInput(value) {
                this.$emit('input', value);
            },
        }
    }
</script>