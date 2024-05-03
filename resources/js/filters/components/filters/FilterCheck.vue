<script setup lang="ts">
    import { CheckFilterData } from "@/types";
    import { Button } from "@/components/ui/button";
    import { Checkbox } from "@/components/ui/checkbox";

    defineProps<{
        value: CheckFilterData['value'],
        filter: Omit<CheckFilterData, 'value'>,
        disabled?: boolean,
    }>();

    defineEmits(['input']);
</script>

<template>
    <Button as="div" class="relative h-8 border-dashed" variant="outline" size="sm">
        <Checkbox class="after:absolute after:inset-0" :checked="value" @update:checked="$emit('input', $event)" />
        <span class="ml-2" data-filter-label>
            {{ filter.label }}
        </span>
    </Button>
</template>
