<script setup lang="ts">
    import { router } from "@inertiajs/vue3";
    import { config } from "@/utils/config";

    function onClick(e: MouseEvent & { target: HTMLElement }) {
        const link: HTMLAnchorElement = e.target.closest('a[href]');
        const base = `${location.origin}/${config('sharp.custom_url_segment')}/`;

        if(link?.href.startsWith(base)) {
            router.visit(link.href);
            e.preventDefault();
        }
    }
</script>

<template>
    <div @click="onClick">
        <slot />
    </div>
</template>
