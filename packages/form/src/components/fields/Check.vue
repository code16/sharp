<script setup lang="ts">
    import { FormCheckFieldData } from "@/types";

    defineProps<{
        id: string,
        field: FormCheckFieldData,
        value: FormCheckFieldData['value'],
    }>();
</script>

<template>
    <div class="relative flex items-start">
        <div class="flex h-6 items-center">
            <input
                class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600"
                type="checkbox"
                :id="id"
                :checked="value"
                :disabled="field.readOnly"
                @change="$emit('input', ($event.target as HTMLInputElement).checked)"
            >
        </div>
        <div class="ml-3 text-sm leading-6">
            <label for="comments" class="font-medium text-gray-900">
                {{ field.text }}
            </label>
        </div>
    </div>
</template>
