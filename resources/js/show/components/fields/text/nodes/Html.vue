<script setup lang="ts">
    defineProps<{
        content: string
    }>();
</script>

<template>
    <div class="card mb-3 html-content overflow-auto" data-html-content="true">
        <div class="card-body">
            <pre class="overflow-visible mb-0">{{ content }}</pre>
        </div>
    </div>
</template>
