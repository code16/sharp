<script setup lang="ts">
    import { ref } from "vue";

    defineProps<{
        hasError?: boolean,
        modelValue?: string,
    }>();

    const input = ref();

    defineEmits(['update:modelValue']);

    defineExpose({
        focus: () => input.value.focus(),
    });
</script>

<template>
    <input
        class="block w-full rounded-md border-0 py-1.5 shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6"
        :class="{
            'text-red-900 ring-red-300 placeholder:text-red-300 focus:ring-red-500': hasError,
            'text-gray-900 ring-gray-300 placeholder:text-gray-400 focus:ring-primary-600': !hasError,
        }"
        :aria-invalid="hasError ? 'true' : 'false'"
        :value="modelValue"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        ref="input"
    >
</template>
