<script setup lang="ts">
    import { FilterData } from "@/types";
    import FilterCheck from "./filters/FilterCheck.vue";
    import FilterDateRange from "./filters/FilterDateRange.vue";
    import FilterSelect from "./filters/FilterSelect.vue";
    import type { Component } from "vue";
    import { FilterProps } from "@/filters/types";

    const props = defineProps<FilterProps<FilterData, any>>();
    const components: Record<FilterData['type'], Component> = {
        'check': FilterCheck,
        'daterange': FilterDateRange,
        'select': FilterSelect,
    };
</script>

<template>
    <component
        :is="components[filter.type]"
        v-bind="props"
    />
</template>
