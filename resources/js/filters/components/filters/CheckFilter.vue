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
    <Button as="div" class="relative h-8 border" variant="outline" size="sm">
        <Label class="flex items-center gap-2">
            <span class="absolute inset-0"></span>
            <Checkbox
                :model-value="props.value"
                @update:model-value="emit('input', $event as boolean)"
            />
            {{ props.filter.label }}
        </Label>
    </Button>
</template>
