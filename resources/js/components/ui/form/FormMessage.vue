<script lang="ts" setup>
defineProps<{
    message: string|null,
}>()
</script>

<template>
    <p v-if="message" class="text-sm font-medium text-destructive">
        {{ message }}
    </p>
</template>
