<script lang="ts">
    import {inject, Ref} from "vue";

    export function useMenuBoundaryElement() {
        return inject<Ref<HTMLElement>>('menuBoundary');
    }
</script>

<script setup lang="ts">
    import { onMounted, provide, useTemplateRef } from "vue";
    import { CircleUser, ChevronsUpDown, LogOut, Moon, Sun, ChevronDown, ExternalLink } from "lucide-vue-next";
    import Notifications from "@/components/Notifications.vue";
    import { useDialogs } from "@/utils/dialogs";
    import useMenu from "@/composables/useMenu";
    import Logo from "@/components/Logo.vue";
    import { auth } from "@/utils/auth";
    import { __ } from "@/utils/i18n";
    import { route } from "@/utils/url";
    import { getCsrfToken } from "@/utils/request";
    import {
        DropdownMenu,
        DropdownMenuContent, DropdownMenuGroup, DropdownMenuItem,
         DropdownMenuSeparator, DropdownMenuSub, DropdownMenuSubContent, DropdownMenuSubTrigger,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import { buttonVariants } from "@/components/ui/button";
    import { Link, usePage } from "@inertiajs/vue3";
    import { ConfigProvider, DropdownMenuPortal } from "reka-ui";
    import { Collapsible, CollapsibleTrigger, CollapsibleContent } from "@/components/ui/collapsible";
    import {
        AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent,
        AlertDialogDescription,
        AlertDialogFooter,
        AlertDialogHeader,
        AlertDialogTitle
    } from "@/components/ui/alert-dialog";
    import { config } from "@/utils/config";
    import { GlobalSearchData, GlobalFiltersData } from "@/types";
    import GlobalFilters from "@/filters/components/GlobalFilters.vue";
    import SharpLogoMini from '../../svg/logo-mini.svg';
    import ColorModeDropdownItems from "@/components/ColorModeDropdownItems.vue";
    import Icon from "@/components/ui/Icon.vue";
    import { Dialog, DialogContent } from "@/components/ui/dialog";
    import {
        Sidebar,
        SidebarContent,
        SidebarFooter,
        SidebarGroup, SidebarGroupContent,
        SidebarGroupLabel,
        SidebarHeader, SidebarInset,
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
        SidebarProvider, SidebarRail, SidebarSeparator, SidebarTrigger
    } from "@/components/ui/sidebar";
    import { useEventListener, useStorage } from "@vueuse/core";
    import GlobalSearch from "@/components/GlobalSearch.vue";
    import Content from "@/components/Content.vue";
    import MenuItem from "@/components/MenuItem.vue";
    import { Separator } from "@/components/ui/separator";

    const dialogs = useDialogs();
    const menu = useMenu();
    const globalSearch = usePage().props.globalSearch as GlobalSearchData | null;
    const globalFilters = usePage().props.globalFilters as GlobalFiltersData | null;
    const menuBoundary = useTemplateRef<HTMLElement>('menuBoundary');
    provide('menuBoundary', menuBoundary);

    const openedMenu = useStorage(
        'opened-menu',
        Object.fromEntries(
            menu?.items.filter(item => item.children?.length > 0).map(item => [item.label, true]) ?? []
        ),
        sessionStorage,
        { mergeDefaults: true },
    );
    const currentItemWithChildren = menu?.items.find(item => item.children?.some(child => child.current));
    if(currentItemWithChildren) {
        openedMenu.value[currentItemWithChildren.label] = true;
    }
    const savedScrollTop = useStorage('menu-scroll', 0, sessionStorage);
    const sidebarContent = useTemplateRef<InstanceType<typeof SidebarContent>>('sidebarContent');
    useEventListener(() => sidebarContent.value?.$el, 'scroll', (e) => {
        savedScrollTop.value = (e.target as HTMLElement).scrollTop;
    });
    onMounted(() => {
        if(!history.state?.scrollRegions?.length && savedScrollTop.value) {
            (sidebarContent.value?.$el as HTMLElement).scrollTop = savedScrollTop.value;
        }
    });
</script>

<template>
    <ConfigProvider>
        <SidebarProvider persist>
            <template v-if="auth()?.user">
                <Sidebar>
                    <SidebarHeader class="p-4 h-14 items-start justify-center">
                        <template v-if="$page.props.logo">
                            <div class="text-sidebar-accent-foreground">
                                <Logo />
                            </div>
                        </template>
                        <template v-else>
                            <div class="flex items-center gap-2 font-semibold">
                                <div class="grid place-content-center w-6 h-6">
                                    <SharpLogoMini class="w-3 h-3" />
                                </div>
                                <span>
                                    {{ config('sharp.name') }}
                                </span>
                            </div>
                        </template>
                    </SidebarHeader>
                    <SidebarContent ref="sidebarContent">
                        <template v-if="globalFilters">
                            <SidebarGroup>
                                <GlobalFilters :global-filters="globalFilters" />
                            </SidebarGroup>
                        </template>
                        <template v-if="globalSearch">
                            <GlobalSearch :global-search="globalSearch" />
                        </template>

                        <template v-if="menu?.isVisible">
                            <template v-for="item in menu.items" :key="item.label">
                                <template v-if="item.children">
                                    <Collapsible class="group/collapsible" v-model:open="openedMenu[item.label]" as-child :disabled="!item.isCollapsible">
                                        <SidebarGroup>
                                            <SidebarGroupLabel class="break-words gap-x-2 text-left h-auto py-2 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground disabled:pointer-events-none" as-child>
                                                <CollapsibleTrigger>
                                                    <span class="flex-1 min-w-0">{{ item.label }}</span>
                                                    <template v-if="item.isCollapsible">
                                                        <ChevronDown class="ml-auto transition-transform group-data-[state=open]/collapsible:rotate-180" />
                                                    </template>
                                                </CollapsibleTrigger>
                                            </SidebarGroupLabel>
                                            <CollapsibleContent>
                                                <SidebarGroupContent>
                                                    <SidebarMenu>
                                                        <template v-for="childItem in item.children" :key="childItem.label">
                                                            <template v-if="childItem.isSeparator">
                                                                <div class="relative flex items-center min-h-2 my-1 gap-2 mx-2">
                                                                    <SidebarSeparator class="mx-0 absolute inset-x-0 top-1/2" />
                                                                    <template v-if="childItem.label">
                                                                        <div class="relative text-[.625rem]/[.875rem]">
                                                                            <span class="bg-sidebar text-sidebar-foreground/70 py-[.1875rem] pr-2">
                                                                                {{ childItem.label }}
                                                                            </span>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </template>
                                                            <template v-else>
                                                                <MenuItem :item="childItem" />
                                                            </template>
                                                        </template>
                                                    </SidebarMenu>
                                                </SidebarGroupContent>
                                            </CollapsibleContent>
                                        </SidebarGroup>
                                    </Collapsible>
                                </template>
                                <template v-else>
                                    <SidebarGroup>
                                        <SidebarGroupContent>
                                            <SidebarMenu>
                                                <MenuItem :item="item" />
                                            </SidebarMenu>
                                        </SidebarGroupContent>
                                    </SidebarGroup>
                                </template>
                            </template>
                        </template>
                    </SidebarContent>
                    <SidebarFooter>
                        <SidebarMenu>
                            <SidebarMenuItem>
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <SidebarMenuButton
                                            size="lg"
                                            class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                                        >
                                            <span class="inline-flex items-center justify-center size-8 bg-secondary rounded-lg overflow-hidden">
                                                <template v-if="auth().user.avatar">
                                                    <img class="size-full aspect-1/1 object-cover" :src="auth().user.avatar" :alt="auth().user.name">
                                                </template>
                                                <template v-else>
                                                    <CircleUser class="size-5" />
                                                </template>
                                            </span>
                                            <div class="grid flex-1 text-left text-sm leading-tight">
                                                <span class="truncate font-semibold"> {{ auth().user.name }}</span>
                                                <span class="truncate text-xs">{{ auth().user.email }}</span>
                                            </div>
                                            <ChevronsUpDown class="ml-auto size-4" />
                                        </SidebarMenuButton>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg" side="bottom" :side-offset="4" :collision-boundary="null" align="end">
                                        <template v-if="menu?.userMenu?.items?.length">
                                            <DropdownMenuGroup>
                                                <template v-for="item in menu.userMenu.items">
                                                    <template v-if="item.isSeparator">
                                                        <DropdownMenuSeparator />
                                                    </template>
                                                    <template v-else>
                                                        <DropdownMenuItem :as="item.isExternalLink ? 'a' : Link" :href="item.url">
                                                            <template v-if="item.icon">
                                                                <Icon class="size-4" :icon="item.icon" />
                                                            </template>
                                                            <span>
                                                                {{ item.label }}
                                                            </span>
                                                        </DropdownMenuItem>
                                                    </template>
                                                </template>
                                            </DropdownMenuGroup>
                                        </template>
                                        <DropdownMenuSeparator class="first:hidden" />
                                        <DropdownMenuSub>
                                            <DropdownMenuSubTrigger>
                                                <Sun class="w-4 h-4 dark:hidden" />
                                                <Moon class="hidden w-4 h-4 dark:block" />
                                                {{ __('sharp::action_bar.color-mode-dropdown.label') }}
                                            </DropdownMenuSubTrigger>
                                            <DropdownMenuPortal>
                                                <DropdownMenuSubContent>
                                                    <ColorModeDropdownItems />
                                                </DropdownMenuSubContent>
                                            </DropdownMenuPortal>
                                        </DropdownMenuSub>
                                        <DropdownMenuSeparator />
                                        <form :action="route('code16.sharp.logout')" method="post">
                                            <input name="_token" :value="getCsrfToken()" type="hidden">
                                            <DropdownMenuItem type="submit" @click="$event.target.closest('form').submit()">
                                                <LogOut class="w-4 h-4" />
                                                {{ __('sharp::menu.logout_label') }}
                                            </DropdownMenuItem>
                                        </form>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </SidebarFooter>
                    <SidebarRail />
                </Sidebar>
            </template>
            <SidebarInset class="min-w-0">
                <header class="flex h-14 items-center gap-4 border-b backdrop-blur-sm bg-background/90 px-4 sticky top-0 z-40 lg:px-6
                    transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12
                ">
                    <template v-if="auth()?.user">
                        <SidebarTrigger class="-ml-1 shrink-0" />
                    </template>
                    <Separator
                        orientation="vertical"
                        class="mr-2 data-[orientation=vertical]:h-4"
                    />
                    <div class="min-w-0 flex-1 lg:flex-initial">
                        <slot name="breadcrumb" />
                    </div>
                    <div class="flex-1 self-stretch hidden -mb-px lg:block" data-topbar-sticky-safe-area>
                    </div>
                </header>
                <main class="relative flex-1 pb-24">
                    <div class="absolute inset-0 -z-10">
                        <div class="container h-full">
                            <div class="h-full" data-menu-boundary ref="menuBoundary"></div>
                        </div>
                    </div>
                    <slot />
                </main>
            </SidebarInset>
        </SidebarProvider>

        <Notifications />

        <template v-for="dialog in dialogs" :key="dialog.id">
            <template v-if="dialog.isHtmlContent">
                <Dialog
                    v-model:open="dialog.open"
                    @update:open="(open) => !open && window.setTimeout(() => dialog.onHidden(), 200)"
                >
                    <DialogContent
                        class="max-w-(--breakpoint-xl) overflow-hidden w-[calc(100%-4rem)] h-[90dvh]"
                        :class="!dialog.text?.includes('window.Sfdump') ? 'p-0' : ''"
                    >
                        <iframe class="size-full" :srcdoc="`
                            <style>body,pre{margin:0}</style>
                            ${dialog.text.replace('</head>', '<style>html{font-size:14px}.container{max-width: none;}</style></head>')}
                        `"></iframe>
                    </DialogContent>
                </Dialog>
            </template>
            <template v-else>
                <AlertDialog
                    v-model:open="dialog.open"
                    @update:open="(open) => !open && window.setTimeout(() => dialog.onHidden(), 200)"
                >
                    <AlertDialogContent :highlight-element="dialog.highlightElement">
                        <AlertDialogHeader>
                            <template v-if="dialog.title">
                                <AlertDialogTitle>
                                    {{ dialog.title }}
                                </AlertDialogTitle>
                            </template>
                            <AlertDialogDescription as="div"
                                class="break-words max-h-[calc(100vh-14rem)] pr-6 -mr-6 overflow-auto" :class="!dialog.title ? 'text-base font-medium text-foreground' : ''"
                            >
                                <Content :html="dialog.text"></Content>
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                            <template v-if="!dialog.okOnly">
                                <AlertDialogCancel>
                                    {{ __('sharp::modals.cancel_button') }}
                                </AlertDialogCancel>
                            </template>
                            <AlertDialogAction :class="buttonVariants({ variant: dialog.okVariant })" @click="dialog.onOk()">
                                {{ dialog.okTitle ?? __('sharp::modals.ok_button') }}
                            </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            </template>
        </template>
    </ConfigProvider>
</template>
