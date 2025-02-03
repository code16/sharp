<script setup lang="ts">
    import { FilterData } from "@/types";
    import CheckFilter from "./filters/CheckFilter.vue";
    import DateRangeFilter from "./filters/DateRangeFilter.vue";
    import SelectFilter from "./filters/SelectFilter.vue";
    import type { Component } from "vue";
    import { FilterProps } from "@/filters/types";

    const props = defineProps<FilterProps<FilterData, any>>();
    const components: Record<FilterData['type'], Component> = {
        'check': CheckFilter,
        'daterange': DateRangeFilter,
        'select': SelectFilter,
    };
</script>

<template>
    <component
        :is="components[filter.type]"
        v-bind="props"
    />
</template>
