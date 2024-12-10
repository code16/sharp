<script setup lang="ts">
    defineProps<{
        content: string
    }>();
</script>

<template>
    <div class="my-4 first:mt-0 last:mb-0 bg-background border rounded-md p-4 max-h-[200px] overflow-auto">
        <pre>{{ content }}</pre>
    </div>
</template>
