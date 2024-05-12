<script setup lang="ts">
    import { BreadcrumbData, BreadcrumbItemData } from "@/types";
    import {
        Breadcrumb,
        BreadcrumbEllipsis,
        BreadcrumbItem,
        BreadcrumbLink,
        BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator
    } from "@/components/ui/breadcrumb";
    import { Link } from "@inertiajs/vue3";
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import { ref } from "vue";

    const props = defineProps<{
        breadcrumb: BreadcrumbData,
    }>();
    const appendItem = ref<BreadcrumbItemData | null>();

    document.addEventListener('breadcrumb:updateAppendItem', (event: CustomEvent<BreadcrumbItemData | null>) => {
        appendItem.value = event.detail;
    });
</script>
<template>
    <Breadcrumb v-if="breadcrumb.items?.length">
        <BreadcrumbList class="flex-nowrap max-w-screen overflow-hidden">
            <BreadcrumbItem class="min-w-0">
                <template v-if="breadcrumb.items.length === 1">
                    <BreadcrumbPage>
                        {{ breadcrumb.items[0].label }}
                    </BreadcrumbPage>
                </template>
                <template v-else>
                    <BreadcrumbLink as-child>
                        <Link class="truncate" :href="breadcrumb.items[0].url">
                            {{ breadcrumb.items[0].label }}
                        </Link>
                    </BreadcrumbLink>
                </template>
            </BreadcrumbItem>
            <template v-if="breadcrumb.items.length > 3">
                <BreadcrumbSeparator />
                <BreadcrumbItem>
                    <DropdownMenu>
                        <DropdownMenuTrigger class="flex items-center gap-1">
                            <BreadcrumbEllipsis class="h-4 w-4" />
                            <span class="sr-only">Toggle menu</span>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="start">
                            <template v-for="item in breadcrumb.items.slice(1, -2)">
                                <DropdownMenuItem as-child>
                                    <Link :href="item.url">
                                        {{ item.label }}
                                    </Link>
                                </DropdownMenuItem>
                            </template>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </BreadcrumbItem>
            </template>
            <template v-for="item in breadcrumb.items.slice(1).slice(-2, -1)">
                <BreadcrumbSeparator />
                <BreadcrumbItem class="min-w-0">
                    <BreadcrumbLink as-child>
                        <Link class="truncate min-w-0" :href="item.url">
                            {{ item.label }}
                        </Link>
                    </BreadcrumbLink>
                </BreadcrumbItem>
            </template>
            <BreadcrumbSeparator />
            <BreadcrumbItem >
                <BreadcrumbPage class="truncate w-max min-w-0 max-w-full">
                    {{ breadcrumb.items.at(-1).label }}
                </BreadcrumbPage>
            </BreadcrumbItem>
<!--            <template v-if="appendItem">-->
<!--                <BreadcrumbSeparator />-->
<!--                <BreadcrumbItem>-->
<!--                    <BreadcrumbPage>-->
<!--                        {{ appendItem.label }}-->
<!--                    </BreadcrumbPage>-->
<!--                </BreadcrumbItem>-->
<!--            </template>-->
        </BreadcrumbList>
    </Breadcrumb>
</template>
