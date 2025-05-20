<script setup lang="ts">
    import Layout from "@/Layouts/Layout.vue";
    import { __ } from "@/utils/i18n";
</script>

<template>
    <Layout>
        <div class="@container">
            <div class="container pt-6 @3xl:pt-10">
                <h1 class="text-xl">
                    {{ __('sharp::menu.no-dashboard-message') }}
                </h1>
            </div>
        </div>
    </Layout>
</template>
