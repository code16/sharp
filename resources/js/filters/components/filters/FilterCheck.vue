<script setup lang="ts">
    import { CheckFilterData } from "@/types";

    defineProps<{
        value: CheckFilterData['value'],
        filter: Omit<CheckFilterData, 'value'>,
        disabled?: boolean,
    }>();
</script>

<template>
    <div class="SharpFilterCheck form-check mt-1 mx-2">
        <input
            :id="`filter-${filter.key}`"
            class="form-check-input"
            type="checkbox"
            :name="filter.key"
            :checked="value"
            :disabled="disabled"
            @change="$emit('input', ($event.target as HTMLInputElement).checked)"
        >
        <label class="ui-font fs-8" :for="`filter-${filter.key}`">
            {{ filter.label }}
        </label>
    </div>
</template>
