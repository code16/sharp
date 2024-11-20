<script setup lang="ts">
    import { DateRangeFilterData } from "@/types";
    import { Badge } from "@/components/ui/badge";
    import { FilterProps } from "@/filters/types";

    const props = defineProps<FilterProps<DateRangeFilterData>>();
</script>

<template>
    <Badge
        v-if="'start' in props.value"
        variant="secondary"
        class="rounded-sm px-1 font-normal"
    >
        <template v-if="props.value.end">
            {{ new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(new Date(props.value.start)) }}
            -
            {{ new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(new Date(props.value.end)) }}
        </template>

        <template v-else>
            {{ new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(new Date(props.value.start)) }}
        </template>
    </Badge>
</template>
