<script lang="ts" setup>
    import { useData, useRoute } from 'vitepress/client'
    import type { DefaultTheme } from 'vitepress'
    import VPNavBarMenuLink from 'vitepress/dist/client/theme-default/components/VPNavBarMenuLink.vue';
    import VPNavBarMenuGroup from 'vitepress/dist/client/theme-default/components/VPNavBarMenuGroup.vue';
    import { computed } from "vue";
    import { getCurrentVersionForRoute } from "../utils/getCurrentVersionForRoute";

    const props = defineProps<{
        items: DefaultTheme.NavItemWithLink[]
    }>()

    const route = useRoute();
</script>

<template>
    <template v-if="route.data.relativePath === 'index.md'">
        <VPNavBarMenuLink :item="{ text: 'Docs', link: props.items[0].link }" />
    </template>
    <template v-else>
        <VPNavBarMenuGroup :item="{
            text: getCurrentVersionForRoute(route)?.name,
            items: props.items
        }" />
    </template>
</template>
