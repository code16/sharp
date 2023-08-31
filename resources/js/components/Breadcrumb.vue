<script setup lang="ts">
    import useMenu from "@/composables/useMenu";
    import { BreadcrumbData } from "@/types";

    const props = defineProps<{
        breadcrumb: BreadcrumbData,
    }>();

    const menu = useMenu();
    const currentEntityItem = menu.getEntityItem(props.breadcrumb.items.at(-1)?.entityKey);
</script>
<template>
    <div class="d-flex">
        <div class="d-flex align-items-center">
            <template v-if="currentEntityItem">
                <div class="me-2 pe-1">
                    <i class="fa fa-sm d-block text-primary opacity-75 fs-8" :class="currentEntityItem.icon"></i>
                </div>
            </template>

            <div class="breadcrumb align-items-center p-0 m-0">
                <template v-for="(item, i) in breadcrumb.items">
                    <div class="breadcrumb-item" :class="{ 'active': i === breadcrumb.items.length - 1 }">
                        <template v-if="i === breadcrumb.items.length - 1">
                            <span>{{ item.label }}</span>
                        </template>
                        <template v-else>
                            <a :href="item.url">{{ item.label }}</a>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
