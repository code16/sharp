<script setup lang="ts">
    import { FormNumberFieldData } from "@/types";
    import TextField from './Text.vue';

    defineProps<{
        field: FormNumberFieldData,
        value: FormNumberFieldData['value'],
    }>();
</script>

<template>
    <TextField
        :class="{
            '[appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none': !field.showControls,
        }"
        :value="value"
        :step="field.step"
        :min="field.min"
        :max="field.max"
        type="number"
    />
</template>
