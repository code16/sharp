<script setup lang="ts">
    import { config } from "@/utils/config";
    import { usePage } from "@inertiajs/vue3";
    import { LogoData } from "@/types";
    const logo = usePage().props.logo as LogoData | null;
</script>

<template>
    <template v-if="logo.svg">
        <div class="[&_svg]:w-auto [&_svg]:max-w-full [&_svg]:h-(--height)"
            :style="{ '--height': config('sharp.theme.logo_height') ?? '1.5rem' }"
            v-html="logo.svg"
        ></div>
    </template>
    <template v-else>
        <img class="w-auto h-(--height)"
            :style="{ '--height': config('sharp.theme.logo_height') ?? '1.5rem' }"
            :src="logo.url"
            :alt="config('sharp.name')"
        >
    </template>
</template>
