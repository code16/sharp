<script setup lang="ts">
    import { BreadcrumbData } from "@/types";
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

    const props = defineProps<{
        breadcrumb: BreadcrumbData,
    }>();

</script>
<template>
    <Breadcrumb v-if="breadcrumb.items?.length">
        <BreadcrumbList>
            <BreadcrumbItem>
                <template v-if="breadcrumb.items.length === 1">
                    <BreadcrumbPage>
                        {{ breadcrumb.items[0].label }}
                    </BreadcrumbPage>
                </template>
                <template v-else>
                    <BreadcrumbLink as-child>
                        <Link :href="breadcrumb.items[0].url">
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
            <template v-for="item in breadcrumb.items.slice(1).slice(-2)">
                <BreadcrumbItem>
                    <template v-if="item.url === breadcrumb.items.at(-1).url">
                        <BreadcrumbPage>
                            {{ item.label }}
                        </BreadcrumbPage>
                    </template>
                    <template v-else>
                        <BreadcrumbLink as-child>
                            <Link :href="item.url">
                                {{ item.label }}
                            </Link>
                        </BreadcrumbLink>
                    </template>
                </BreadcrumbItem>
            </template>
        </BreadcrumbList>
    </Breadcrumb>
</template>
