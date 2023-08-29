<script setup lang="ts">
    import { BreadcrumbData, MenuData, MenuItemData } from "@/types";
    import { usePage, Head } from "@inertiajs/vue3";
    import { config } from "@/utils/config";

    const props = defineProps<{
        entityKey?: string,
        breadcrumb?: BreadcrumbData,
        prepend?: string,
    }>();

    const menu = usePage().props.menu as MenuData;

    const currentBreadcrumbItem = props.breadcrumb?.items.at(-1);
    const currentEntityKey = currentBreadcrumbItem?.entityKey ?? props.entityKey;
    const currentEntityItem = currentEntityKey
        ? menu.items
            .map(item => [item, item.children])
            .flat(2)
            .filter(Boolean)
            .find((item: MenuItemData) => item.entityKey === currentEntityKey)
        : null;
</script>

<template>
    <Head>
        <component is="title">
            {{ $slots.default?.()[0].children }}
            {{ currentBreadcrumbItem?.documentTitleLabel
                ? `${currentBreadcrumbItem.documentTitleLabel},`
                : '' }}
            {{ currentEntityItem?.label }}
            |
            {{ config('sharp.name') }}
            {{ config('sharp.display_sharp_version_in_title')
                ? `(${usePage().props.sharpVersion})`
                : '' }}
        </component>
    </Head>
</template>
