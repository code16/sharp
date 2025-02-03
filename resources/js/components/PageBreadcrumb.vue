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
            <template v-if="breadcrumb.items.length > 1">
                <BreadcrumbItem class="min-w-4">
                    <BreadcrumbLink as-child>
                        <Link class="truncate" :href="breadcrumb.items[0].url" :title="breadcrumb.items[0].label">
                            {{ breadcrumb.items[0].label }}
                        </Link>
                    </BreadcrumbLink>
                </BreadcrumbItem>
                <BreadcrumbSeparator />
            </template>
            <template v-if="breadcrumb.items.length > 2">
                <div class="contents" :class="breadcrumb.items.length === 3 ? 'sm:hidden' : ''">
                    <BreadcrumbItem>
                        <DropdownMenu>
                            <DropdownMenuTrigger class="flex items-center gap-1">
                                <BreadcrumbEllipsis class="h-4 w-4" />
                                <span class="sr-only">Toggle menu</span>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent>
                                <template v-for="item in breadcrumb.items.slice(1, -1)">
                                    <DropdownMenuItem class="sm:last:hidden" as-child>
                                        <Link :href="item.url">
                                            {{ item.label }}
                                        </Link>
                                    </DropdownMenuItem>
                                </template>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                </div>
            </template>
            <template v-for="item in breadcrumb.items.slice(1).slice(-2, -1)">
                <BreadcrumbItem class="min-w-4 max-sm:hidden">
                    <BreadcrumbLink as-child>
                        <Link class="truncate" :href="item.url" :title="item.label">
                            {{ item.label }}
                        </Link>
                    </BreadcrumbLink>
                </BreadcrumbItem>
                <BreadcrumbSeparator class="max-sm:hidden" />
            </template>
            <BreadcrumbItem class="w-max min-w-4 max-w-full">
                <BreadcrumbPage class="truncate" :title="breadcrumb.items.at(-1).label">
                    {{ breadcrumb.items.at(-1).label }}
                </BreadcrumbPage>
            </BreadcrumbItem>
            <template v-if="appendItem">
                <BreadcrumbSeparator />
                <BreadcrumbItem>
                    <BreadcrumbPage>
                        {{ appendItem.label }}
                    </BreadcrumbPage>
                </BreadcrumbItem>
            </template>
        </BreadcrumbList>
    </Breadcrumb>
</template>
