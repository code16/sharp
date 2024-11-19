<script lang="ts">
    import { inject, Ref } from "vue";

    export function useMenuBoundaryElement() {
        return inject<Ref<HTMLElement>>('menuBoundary');
    }
</script>

<script setup lang="ts">
    import { provide, ref, useTemplateRef } from "vue";
import { CircleUser, ChevronsUpDown, LogOut, Menu, PanelLeftClose, PanelLeftOpen, Moon, Sun, ChevronRight } from "lucide-vue-next";
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
    DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuSub, DropdownMenuSubContent, DropdownMenuSubTrigger,
    DropdownMenuTrigger
} from "@/components/ui/dropdown-menu";
import { Button, buttonVariants } from "@/components/ui/button";
import { Link, usePage } from "@inertiajs/vue3";
import { CollapsibleTrigger, ConfigProvider, DropdownMenuPortal } from "reka-ui";
import { Collapsible, CollapsibleContent } from "@/components/ui/collapsible";
import { Sheet, SheetContent, SheetTrigger } from "@/components/ui/sheet";
import {
    AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle
} from "@/components/ui/alert-dialog";
import { config } from "@/utils/config";
import { GlobalFiltersData } from "@/types";
import GlobalFilters from "@/filters/components/GlobalFilters.vue";
import SharpLogoMini from '../../svg/logo-mini.svg';
import ColorModeDropdownItems from "@/components/ColorModeDropdownItems.vue";
    import Icon from "@/components/ui/Icon.vue";
    import { Dialog, DialogContent } from "@/components/ui/dialog";
    import {
        Sidebar,
        SidebarContent,
        SidebarFooter,
        SidebarGroup,
        SidebarGroupLabel,
        SidebarHeader, SidebarInset,
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
        SidebarMenuSub,
        SidebarMenuSubButton,
        SidebarMenuSubItem,
        SidebarProvider, SidebarSeparator
    } from "@/components/ui/sidebar";

const dialogs = useDialogs();
const menu = useMenu();
const globalFilters = usePage().props.globalFilters as GlobalFiltersData | null;
const showDesktopLeftNav = ref(true);
const menuBoundary = useTemplateRef<HTMLElement>('menuBoundary');
provide('menuBoundary', menuBoundary);
</script>

<template>
    <ConfigProvider>
        <SidebarProvider>
            <Sidebar>
                <SidebarHeader>
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
                <SidebarContent>
                    <template v-if="globalFilters">
                        <SidebarGroup>
                            <GlobalFilters :global-filters="globalFilters" />
                        </SidebarGroup>
                    </template>
                    <SidebarMenu>
                        <template v-for="item in menu.items">
                            <template v-if="item.children">
                                <div class="py-2 mb-4 last:mb-0">
                                    <Collapsible class="group/collapsible" default-open as-child>
                                        <SidebarMenuItem>
                                            <CollapsibleTrigger as-child>
                                                <SidebarMenuButton :tooltip="item.label">
                                                    <span>{{ item.label }}</span>
                                                    <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                                                </SidebarMenuButton>
                                            </CollapsibleTrigger>
                                            <CollapsibleContent>
                                                <SidebarMenuSub>
                                                    <template v-for="childItem in item.children">
                                                        <template v-if="childItem.isSeparator">
                                                            <SidebarSeparator />
                                                        </template>
                                                        <template v-else>
                                                            <SidebarMenuSubItem>
                                                                <SidebarMenuSubButton :is-active="childItem.current" as-child>
                                                                    <component
                                                                        :is="childItem.isExternalLink ? 'a' : Link"
                                                                        :href="childItem.url"
                                                                    >
<!--                                                                    <template v-if="childItem.icon">-->
<!--                                                                        <Icon :icon="childItem.icon" class="size-4" />-->
<!--                                                                    </template>-->
                                                                        <span>{{ childItem.label }}</span>
                                                                    </component>
                                                                </SidebarMenuSubButton>
                                                            </SidebarMenuSubItem>
                                                        </template>
                                                    </template>
                                                </SidebarMenuSub>
                                            </CollapsibleContent>
                                        </SidebarMenuItem>
                                    </Collapsible>
                                </div>
                            </template>
                            <template v-else>
                                <SidebarMenuItem>
                                    <SidebarMenuButton :is-active="item.current" as-child>
                                        <component :is="item.isExternalLink ? 'a' : Link">
<!--                                            <template v-if="item.icon">-->
<!--                                                <Icon :icon="item.icon" class="size-4" />-->
<!--                                            </template>-->
                                            <span>
                                                {{ item.label }}
                                            </span>
                                        </component>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            </template>
                        </template>
                    </SidebarMenu>
                </SidebarContent>
                <SidebarFooter>

                </SidebarFooter>
            </Sidebar>
            <SidebarInset>

            </SidebarInset>
        </SidebarProvider>
            <div class="min-h-screen w-full md:grid-cols-[220px_1fr]" :class="[
                'min-[1244px]:grid-cols-[280px_1fr]', // collapse only before 1024px+220px to prevent EL layout shift
                showDesktopLeftNav ? 'md:grid' : 'xl:grid'
            ]">
                <div class="hidden border-r border-sidebar-border bg-sidebar" :class="showDesktopLeftNav ? 'md:block' : 'xl:block'">
                    <div class="flex h-full max-h-screen flex-col gap-2 sticky top-0">
                        <div class="flex h-14 items-center px-4 lg:h-[60px] xl:px-6">
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
                            <!--                    <Button variant="outline" size="icon" class="ml-auto h-8 w-8">-->
                            <!--                        <Bell class="h-4 w-4" />-->
                            <!--                        <span class="sr-only">Toggle notifications</span>-->
                            <!--                    </Button>-->
                        </div>
                        <div class="flex-1 px-2 xl:px-4">
                            <template v-if="globalFilters">
                                <div class="mb-4 mt-2">
                                    <GlobalFilters :global-filters="globalFilters" />
                                </div>
                            </template>
                            <nav class="grid items-start text-sm font-medium">
                                <template v-for="item in menu.items">
                                    <template v-if="item.children">
                                        <div class="py-2 mb-4 last:mb-0">
                                            <Collapsible default-open v-slot="{ open }">
                                                <div class="flex items-center space-x-4 mb-2">
                                                    <h2 class="flex-1 text-lg font-semibold pl-3 text-sidebar-foreground tracking-tight">
                                                        {{ item.label }}
                                                    </h2>
                                                    <template v-if="item.isCollapsible">
                                                        <CollapsibleTrigger as-child>
                                                            <Button class="gap-3" size="sm" variant="ghost">
                                                                <ChevronsUpDown class="h-4 w-4" />
                                                            </Button>
                                                        </CollapsibleTrigger>
                                                    </template>
                                                </div>
                                                <CollapsibleContent>
                                                    <ul>
                                                        <template v-for="childItem in item.children">
                                                            <li>
                                                                <template v-if="childItem.isSeparator">
                                                                    <hr>
                                                                </template>
                                                                <template v-else>
                                                                    <component :is="childItem.isExternalLink ? 'a' : Link"
                                                                        :href="childItem.url"
                                                                        class="flex items-center gap-3 rounded-lg px-3 py-2 min-w-0 transition-all text-sidebar-foreground data-[active=true]:bg-sidebar-accent data-[active=true]:font-medium data-[active=true]:text-sidebar-accent-foreground hover:text-sidebar-accent-foreground"
                                                                        :data-active="childItem.current"
                                                                    >
                                                                        <template v-if="childItem.icon">
                                                                            <Icon :icon="childItem.icon" class="size-4" />
                                                                        </template>
                                                                        <div class="min-w-0 break-words">
                                                                            {{ childItem.label }}
                                                                        </div>
                                                                    </component>
                                                                </template>
                                                            </li>
                                                        </template>
                                                    </ul>
                                                </CollapsibleContent>
                                            </Collapsible>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <component :is="item.isExternalLink ? 'a' : Link"
                                            :href="item.url"
                                            class="flex items-center gap-3 rounded-lg px-3 py-2 min-w-0 transition-all text-sidebar-foreground data-[active=true]:bg-sidebar-accent data-[active=true]:font-medium data-[active=true]:text-sidebar-accent-foreground hover:text-sidebar-accent-foreground"
                                            :data-active="item.current"
                                        >
                                            <template v-if="item.icon">
                                                <Icon :icon="item.icon" class="size-4" />
                                            </template>
                                            <div class="min-w-0 break-words">
                                                {{ item.label }}
                                            </div>
                                        </component>
                                    </template>
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col min-w-0">
                    <header class="flex h-14 items-center gap-4 border-b backdrop-blur bg-background/90 px-4 sticky top-0 z-20 lg:h-[60px] lg:px-6">
                        <Button
                            variant="outline"
                            size="icon"
                            class="shrink-0 hidden md:inline-flex xl:hidden"
                            @click="showDesktopLeftNav = !showDesktopLeftNav"
                        >
                            <template v-if="showDesktopLeftNav">
                                <PanelLeftClose class="h-[1.2rem] w-[1.2rem]" />
                            </template>
                            <template v-else>
                                <PanelLeftOpen class="h-[1.2rem] w-[1.2rem]" />
                            </template>
                            <span class="sr-only">Toggle navigation menu</span>
                        </Button>
                        <Sheet>
                            <SheetTrigger as-child>
                                <Button
                                    variant="outline"
                                    size="icon"
                                    class="shrink-0 md:hidden"
                                >
                                    <Menu class="h-5 w-5" />
                                    <span class="sr-only">Toggle navigation menu</span>
                                </Button>
                            </SheetTrigger>
                            <SheetContent side="left" class="flex flex-col">
                                <nav class="grid gap-2 text-lg font-medium">
                                    <template v-for="item in menu.items">
                                        <template v-if="item.children">
                                            <div class="py-2 mb-4 last:mb-0">
                                                <h2 class="mb-2 text-lg font-semibold tracking-tight">
                                                    {{ item.label }}
                                                </h2>
                                                <ul>
                                                    <template v-for="childItem in item.children">
                                                        <template v-if="childItem.isSeparator">
                                                            <hr>
                                                        </template>
                                                        <template v-else>
                                                            <li>
                                                                <component :is="childItem.isExternalLink ? 'a' : Link"
                                                                    :href="childItem.url"
                                                                    :data-active="childItem.current"
                                                                    class="mx-[-0.65rem] flex items-center gap-4 rounded-xl px-3 py-2 text-sidebar-foreground data-[active=true]:bg-sidebar-accent data-[active=true]:font-medium data-[active=true]:text-sidebar-accent-foreground hover:text-sidebar-accent-foreground"
                                                                >
                                                                    <template v-if="childItem.icon">
                                                                        <Icon :icon="childItem.icon" class="size-4" />
                                                                    </template>
                                                                    {{ childItem.label }}
                                                                </component>
                                                            </li>
                                                        </template>
                                                    </template>
                                                </ul>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <component :is="item.isExternalLink ? 'a' : Link"
                                                :href="item.url"
                                                :data-active="item.current"
                                                class="mx-[-0.65rem] flex items-center gap-4 rounded-xl px-3 py-2 text-sidebar-foreground data-[active=true]:bg-sidebar-accent data-[active=true]:font-medium data-[active=true]:text-sidebar-accent-foreground hover:text-sidebar-accent-foreground"
                                            >
                                                <template v-if="item.icon">
                                                    <Icon :icon="item.icon" class="size-4" />
                                                </template>
                                                {{ item.label }}
                                            </component>
                                        </template>
                                    </template>
                                </nav>
                            </SheetContent>
                        </Sheet>
                        <div class="min-w-0 flex-1 lg:flex-initial">
                            <slot name="breadcrumb" />
                        </div>
                        <div class="flex-1 self-stretch hidden -mb-px lg:block" data-topbar-sticky-safe-area>
                            <!--                    <form>-->
                            <!--                        <div class="relative">-->
                            <!--                            <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />-->
                            <!--                            <Input-->
                            <!--                                type="search"-->
                            <!--                                placeholder="Search products..."-->
                            <!--                                class="w-full appearance-none bg-background pl-8 shadow-none md:w-2/3 lg:w-1/3"-->
                            <!--                            />-->
                            <!--                        </div>-->
                            <!--                    </form>-->
                        </div>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button class="hidden min-[1900px]:flex" variant="ghost">
                                    <Moon class="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
                                    <Sun class="absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
                                    <span class="sr-only">Toggle theme</span>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent :collision-boundary="null" align="end">
                                <DropdownMenuLabel>
                                    {{ __('sharp::action_bar.color-mode-dropdown.label') }}
                                </DropdownMenuLabel>
                                <ColorModeDropdownItems />
                            </DropdownMenuContent>
                        </DropdownMenu>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="secondary" size="icon" class="rounded-full">
                                    <CircleUser class="h-5 w-5" />
                                    <span class="sr-only">Toggle user menu</span>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent :collision-boundary="null" align="end">
                                <DropdownMenuLabel class="font-normal flex">
                                    <div class="flex flex-col space-y-1">
                                        <p class="text-sm font-medium leading-none">
                                            {{ auth().user.name }}
                                        </p>
                                        <p class="text-xs leading-none text-muted-foreground">
                                            {{ auth().user.email }}
                                        </p>
                                    </div>
                                </DropdownMenuLabel>
                                <template v-if="menu.userMenu">
                                    <DropdownMenuSeparator />
                                    <DropdownMenuGroup>
                                        <template v-for="item in menu.userMenu.items">
                                            <template v-if="item.isSeparator">
                                                <DropdownMenuSeparator />
                                            </template>
                                            <template v-else>
                                                <DropdownMenuItem :as="item.isExternalLink ? 'a' : Link" :href="item.url">
                                                    <template v-if="item.icon">
                                                        <Icon class="mr-2 size-4" :icon="item.icon" />
                                                    </template>
                                                    <span>
                                                        {{ item.label }}
                                                    </span>
                                                </DropdownMenuItem>
                                            </template>
                                        </template>
                                    </DropdownMenuGroup>
                                </template>
                                <DropdownMenuSeparator />
                                <DropdownMenuSub>
                                    <DropdownMenuSubTrigger>
                                        <Sun class="w-4 h-4 mr-2 dark:hidden" />
                                        <Moon class="hidden w-4 h-4 mr-2 dark:block" />
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
                                        <LogOut class="w-4 h-4 mr-2" />
                                        {{ __('sharp::menu.logout_label') }}
                                    </DropdownMenuItem>
                                </form>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </header>
                    <main class="relative flex-1 pt-4 lg:pt-6 pb-24">
                        <div class="absolute inset-0 -z-10">
                            <div class="container h-full">
                                <div class="h-full" data-menu-boundary ref="menuBoundary"></div>
                            </div>
                        </div>
                        <slot />
                    </main>
                </div>
            </div>

        <Notifications />

        <template v-for="dialog in dialogs" :key="dialog.id">
            <template v-if="dialog.isHtmlContent">
                <Dialog
                    v-model:open="dialog.open"
                    @update:open="(open) => !open && window.setTimeout(() => dialog.onHidden(), 200)"
                >
                    <DialogContent class="max-w-5xl h-[90dvh]">
                        <iframe class="size-full" :srcdoc="`<style>body,pre{margin:0}</style>${dialog.text}`"></iframe>
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
                            <AlertDialogDescription class="break-all">
                                {{ dialog.text }}
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
