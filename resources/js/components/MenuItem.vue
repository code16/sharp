<script setup lang="ts">
    import { Link } from '@inertiajs/vue3';
    import { isSharpLink } from '@/utils/url';
    import Icon from '@/components/ui/Icon.vue';
    import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
    import { Badge } from '@/components/ui/badge';
    import { ExternalLink } from 'lucide-vue-next';
    import { SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
    import { vScrollIntoView } from '@/directives/scroll-into-view';
    import { MenuItemData } from "@/types";

    defineProps<{
        item: MenuItemData,
    }>()
</script>

<template>
    <SidebarMenuItem>
        <SidebarMenuButton
            class="relative"
            :is-active="item.current"
            as-child
        >
            <div v-scroll-into-view.center="item.current">
                <template v-if="item.icon">
                    <Icon :icon="item.icon" class="size-4" />
                </template>
                <span class="flex-1">
                    <component :is="item.isExternalLink ? 'a' : Link" :href="item.url">
                        <span class="absolute inset-0 z-1"></span>
                        {{ item.label }}
                    </component>
                </span>
                <template v-if="item.badge != null && item.badge !== ''">
                    <TooltipProvider>
                        <Tooltip :delay-duration="0" :disabled="!item.badgeTooltip">
                            <TooltipTrigger as-child>
                                <Badge
                                    :as="item.badgeUrl || item.badgeTooltip ? isSharpLink(item.badgeUrl || item.url) ? Link : 'a' : 'div'"
                                    class="-my-px -mr-1"
                                    :class="item.badgeUrl || item.badgeTooltip ? 'relative z-1' : ''"
                                    :href="item.badgeUrl || item.url"
                                    variant="sidebar"
                                >
                                    {{ item.badge }}
                                </Badge>
                            </TooltipTrigger>
                            <TooltipContent>
                                {{ item.badgeTooltip }}
                            </TooltipContent>
                        </Tooltip>
                    </TooltipProvider>
                </template>
                <template v-if="item.isExternalLink">
                    <ExternalLink class="size-4 opacity-50" />
                </template>
            </div>
        </SidebarMenuButton>
    </SidebarMenuItem>
</template>
