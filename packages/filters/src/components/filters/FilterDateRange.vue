<script setup lang="ts">
    import { DateRange } from '@sharp/form';
    import FilterControl from '../FilterControl.vue';
    import { DateRangeFilterData } from "@/types";

    defineProps<{
        value: DateRangeFilterData['value'],
        filter: Omit<DateRangeFilterData, 'value'>,
        disabled?: boolean,
    }>();
</script>

<template>
    <div class="SharpFilterDateRange">
        <FilterControl :label="filter.label" @label-click="$refs.range.focus()">
            <DateRange
                class="SharpFilterDateRange__field"
                :value="value"
                :display-format="filter.displayFormat"
                :monday-first="filter.mondayFirst"
                :clearable="!filter.required"
                :read-only="disabled"
                :update-on-input="false"
                small
                @input="$emit('input', $event)"
                ref="range"
            />
        </FilterControl>
    </div>
</template>
