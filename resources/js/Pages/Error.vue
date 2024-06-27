<script setup lang="ts">
    import Layout from "@/Layouts/Layout.vue";

    defineProps<{
        status: number,
        message: string,
    }>()
</script>

<template>
    <Layout>
        <h1 class="text-xl mb-2">
            Error {{ status }}
        </h1>
        <p>
            {{ message }}
        </p>
    </Layout>
</template>
