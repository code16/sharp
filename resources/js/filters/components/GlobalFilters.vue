<script setup lang="ts">
    import { FilterData, GlobalFiltersData } from "@/types";
    import { router } from "@inertiajs/vue3";
    import { useFilters } from "../useFilters";
    import { route } from "@/utils/url";
    import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
    import { useTemplateRef } from "vue";
    import { getCsrfToken } from "@/utils/request";

    const props = defineProps<{
        globalFilters: GlobalFiltersData,
    }>();

    const filters = useFilters(props.globalFilters.config.filters, props.globalFilters.filterValues);

    const form = useTemplateRef<HTMLFormElement>('form');
</script>

<template>
    <form :action="route('code16.sharp.filters.update')" method="post" ref="form">
        <input name="_token" :value="getCsrfToken()" type="hidden">
        <div class="grid gap-2">
            <template v-for="filter in filters.rootFilters" :key="filter.key">
                <template v-if="filter.type === 'select'">
                    <Select
                        :name="`filterValues[${filter.key}]`"
                        v-model="filters.currentValues[filter.key]"
                        @update:model-value="$nextTick(() => form.submit())"
                    >
                        <SelectTrigger class="h-8">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <template v-for="value in filter.values">
                                <SelectItem :value="value.id">
                                    {{ value.label ?? value.id }}
                                </SelectItem>
                            </template>
                        </SelectContent>
                    </Select>
                </template>
            </template>
        </div>
    </form>
</template>
