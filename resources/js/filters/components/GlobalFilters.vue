<script setup lang="ts">
    import { FilterData, GlobalFiltersData } from "@/types";
    import { router } from "@inertiajs/vue3";
    import { useFilters } from "../useFilters";
    import { route } from "@/utils/url";
    import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";

    const props = defineProps<{
        globalFilters: GlobalFiltersData,
    }>();

    const filters = useFilters(props.globalFilters.config.filters);

    function onChanged(filter: FilterData, value: FilterData['value']) {
        router.post(
            route('code16.sharp.filters.update', { filterKey: filter.key }),
            { value },
            { preserveState: false, preserveScroll: false }
        );
    }
</script>

<template>
    <div class="grid gap-2">
        <template v-for="filter in filters.rootFilters" :key="filter.key">
            <template v-if="filter.type === 'select'">
                <Select
                    :model-value="String(filters.currentValues[filter.key])"
                    @update:model-value="onChanged(filter, $event)"
                >
                    <SelectTrigger>
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <template v-for="value in filter.values">
                            <SelectItem :value="String(value.id)">
                                {{ value.label ?? value.id }}
                            </SelectItem>
                        </template>
                    </SelectContent>
                </Select>
            </template>
        </template>
    </div>
</template>
