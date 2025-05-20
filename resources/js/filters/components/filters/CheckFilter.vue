<script setup lang="ts">
    import { CheckFilterData } from "@/types";
    import { Button } from "@/components/ui/button";
    import { Checkbox } from "@/components/ui/checkbox";
    import { Label } from "@/components/ui/label";
    import { FilterEmits, FilterProps } from "@/filters/types";

    const props = defineProps<FilterProps<CheckFilterData>>();
    const emit = defineEmits<FilterEmits<CheckFilterData>>()
</script>

<template>
    <Button as="label" class="flex items-center gap-2 relative h-8 border" variant="outline" :aria-disabled="disabled" size="sm">
        <Checkbox
            :model-value="props.value"
            :disabled="disabled"
            @update:model-value="emit('input', $event as boolean)"
        />
        {{ props.filter.label }}
    </Button>
</template>
