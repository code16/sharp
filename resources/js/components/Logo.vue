<script setup lang="ts">
    import { config } from "@/utils/config";
</script>

<template>
    <template v-if="config('sharp.theme.logo_urls.menu')">
        <div class="max-w-[200px]">
            <img class="h-auto w-auto max-w-full max-h-full" :src="config('sharp.theme.logo_urls.menu')" :alt="config('sharp.name')" />
        </div>
    </template>
    <template v-else>
        <div class="text-white">
            {{ config('sharp.name') }}
        </div>
    </template>
</template>
