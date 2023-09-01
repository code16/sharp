<script setup lang="ts">
    import Layout from "@/Layouts/Layout.vue";
    import { __ } from "@/utils/i18n";
</script>

<template>
    <Layout>
        <h1 class="text-xl">
            {{ __('sharp::menu.no-dashboard-message') }}
        </h1>
    </Layout>
</template>
