<template>
    <div class="SharpFilterDateRange">
        <FilterControl :label="label" @label-click="handleClicked">
            <DateRange
                class="SharpFilterDateRange__field"
                :value="value"
                :display-format="displayFormat"
                :monday-first="mondayFirst"
                :clearable="!required"
                :read-only="disabled"
                :update-on-input="false"
                small
                @input="handleInput"
                ref="range"
            />
        </FilterControl>
    </div>
</template>

<script>
    import { DateRange } from '@sharp/form';
    import FilterControl from '../FilterControl.vue';

    export default {
        components: {
            DateRange,
            FilterControl,
        },

        props: {
            value: {
                required: true,
            },
            required: Boolean,
            displayFormat: String,
            mondayFirst: Boolean,
            disabled: Boolean,
            label: String,
        },

        methods: {
            handleClicked() {
                this.$refs.range.focus();
            },
            handleInput(range) {
                this.$emit('input', range);
            },
        }
    }
</script>
