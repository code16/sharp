<script setup lang="ts">

    defineProps<{
        id: string,
        checked: boolean,
        disabled?: boolean,
    }>();
</script>

<template>
    <div class="relative flex items-start">
        <div class="flex h-6 items-center">
            <input
                class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600"
                type="checkbox"
                :id="id"
                :checked="checked"
                :disabled="disabled"
                @change="$emit('change', ($event.target as HTMLInputElement).checked)"
            >
        </div>
        <div class="ml-3 text-sm leading-6">
            <label :for="id" class="font-medium text-gray-900">
                <slot />
            </label>
        </div>
    </div>
</template>
