<script setup lang="ts">
    import { CheckFilterData } from "@/types";
    import { Button } from "@/components/ui/button";
    import { Checkbox } from "@/components/ui/checkbox";
    import { Label } from "@/components/ui/label";

    const props = defineProps<{
        value: CheckFilterData['value'],
        filter: Omit<CheckFilterData, 'value'>,
        disabled?: boolean,
    }>();
    defineEmits(['input']);
</script>

<template>
    <Button as="div" class="relative h-8 border-dashed" variant="outline" size="sm">
        <Label class="flex items-center gap-2">
            <span class="absolute inset-0"></span>
            <Checkbox :checked="value" @update:checked="$emit('input', $event)" />
            {{ filter.label }}
        </Label>
    </Button>
</template>
