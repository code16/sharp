<script setup lang="ts">
    import { FilterData, GlobalFiltersData, SelectFilterData } from "@/types";
    import { router } from "@inertiajs/vue3";
    import { ref } from "vue";
    import { useFilters } from "../useFilters";
    import FilterSelect from './filters/FilterSelect.vue';

    const props = defineProps<{
        globalFilters: GlobalFiltersData,
    }>();

    const filters = useFilters(props.globalFilters.filters);
    const open = ref(false);

    function onChanged(filter: FilterData, value: FilterData['value']) {
        router.post(
            route('code16.sharp.filters.update', { filterKey: filter.key }),
            { value }
        );
    }
</script>

<template>
    <div class="SharpGlobalFilters">
        <template v-if="open">
            <div class="absolute inset-0" style="z-index: 1">
            </div>
        </template>
        <template v-for="filter in filters.rootFilters" :key="filter.key">
            <FilterSelect
                v-if="filter.type === 'select'"
                :filter="filter"
                :value="filters.values[filter.key] as SelectFilterData['value']"
                global
                @input="onChanged(filter, $event)"
                @open="open = true"
                @close="open = false"
                style="z-index: 2"
            />
        </template>
    </div>
</template>
