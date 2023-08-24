<script setup lang="ts">
    import { BreadcrumbData, BreadcrumbItemData, MenuData, MenuItemData } from "@/types";
    import { usePage } from "@inertiajs/vue3";
    import { config } from "@/utils/config";

    const props = defineProps<{
        breadcrumb: BreadcrumbData,
    }>();

    const menu = usePage().props.menu as MenuData;

    const currentBreadcrumbItem = props.breadcrumb?.items.at(-1);
    const currentEntityItem: MenuItemData | null = currentBreadcrumbItem
        ? menu.items
            .map(item => [item, item.children])
            .flat(2)
            .filter(Boolean)
            .find((item: MenuItemData) => item.entityKey === currentBreadcrumbItem.entityKey)
        : null;
</script>

<template>
    <component is="title">
        <template v-if="$slots.default">
            <slot /> |
        </template>
        <template v-else>
            <template v-if="currentBreadcrumbItem?.documentTitleLabel">
                {{ currentBreadcrumbItem?.documentTitleLabel }},
            </template>
            <template v-if="currentEntityItem">
                {{ currentEntityItem.label }} |
            </template>
        </template>
        {{ config('sharp.name') }}
        <template v-if="config('sharp.display_sharp_version_in_title')">
            ({{ usePage().props.sharpVersion }})
        </template>
    </component>
</template>
