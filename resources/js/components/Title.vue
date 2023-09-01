<script setup lang="ts">
    import { BreadcrumbData, MenuData, MenuItemData } from "@/types";
    import { usePage, Head } from "@inertiajs/vue3";
    import { config } from "@/utils/config";
    import useMenu from "@/composables/useMenu";

    const props = defineProps<{
        entityKey?: string,
        breadcrumb?: BreadcrumbData,
        prepend?: string,
    }>();

    const menu = useMenu();

    const currentBreadcrumbItem = props.breadcrumb?.items.at(-1);
    const currentEntityItem = menu.getEntityItem(currentBreadcrumbItem?.entityKey ?? props.entityKey);
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
