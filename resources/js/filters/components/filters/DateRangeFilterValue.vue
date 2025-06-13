<script setup lang="ts">
    import { DateRangeFilterData, DateRangePresetData } from "@/types";
    import { Badge } from "@/components/ui/badge";
    import { FilterProps } from "@/filters/types";

    const props = defineProps<FilterProps<DateRangeFilterData> & { preset: DateRangePresetData|null }>();
</script>

<template>
    <Badge
        v-if="'start' in props.value"
        variant="secondary"
        class=" rounded-sm px-1 font-normal"
    >
        <template v-if="preset">
            <span class="mr-2">
                {{ preset.label }}:
            </span>
        </template>

        <template v-if="props.value.end">
            {{ props.value.formatted.start }} <span class="mx-1.5">-</span> {{ props.value.formatted.end }}
        </template>

        <template v-else>
            {{ props.value.formatted.start }}
        </template>
    </Badge>
</template>
