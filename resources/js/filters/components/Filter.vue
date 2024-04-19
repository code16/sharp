<script setup lang="ts">
    import { FilterData } from "@/types";
    import FilterCheck from "./filters/FilterCheck.vue";
    import FilterDateRange from "./filters/FilterDateRange.vue";
    import FilterSelect from "./filters/FilterSelect.vue";
    import type { Component } from "vue";

    defineProps<{
        filter: FilterData,
        value: any,
        disabled?: boolean
    }>();

    const components: Record<FilterData['type'], Component> = {
        'check': FilterCheck,
        'daterange': FilterDateRange,
        'select': FilterSelect,
    };
</script>

<template>
    <component
        :is="components[filter.type]"
        :filter="filter"
        :value="value"
        :disabled="disabled"
    />
</template>
