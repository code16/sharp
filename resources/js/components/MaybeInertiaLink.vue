<script setup lang="ts">
    import { Link } from '@inertiajs/vue3';
    import { isSharpLink } from "@/utils/url";

    defineProps<{
        href: string,
    }>();
</script>

<template>
    <template v-if="isSharpLink(href)">
        <Link :href="href" v-bind="$attrs">
            <slot />
        </Link>
    </template>
    <template v-else>
        <a :href="href" v-bind="$attrs">
            <slot />
        </a>
    </template>
</template>

