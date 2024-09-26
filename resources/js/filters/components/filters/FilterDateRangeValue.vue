<script setup lang="ts">
    import { DateRangeFilterData } from "@/types";
    import { Badge } from "@/components/ui/badge";

    const props = defineProps<{
        value: DateRangeFilterData['value'],
        filter: Omit<DateRangeFilterData, 'value'>,
        inline?: boolean,
    }>();

</script>

<template>
    <Badge
        variant="secondary"
        class="rounded-sm px-1 font-normal"
    >
        <template v-if="value.end">
            {{ new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(new Date(value.start)) }}
            -
            {{ new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(new Date(value.end)) }}
        </template>

        <template v-else>
            {{ new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(new Date(value.start)) }}
        </template>
    </Badge>
</template>
