<script setup lang="ts">
import LeftNav from "../components/LeftNav.vue";
import { ref } from "vue";
import { CircleUser, ChevronsUpDown, LogOut, Menu } from "lucide-vue-next";
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
    DropdownMenuLabel, DropdownMenuSeparator,
    DropdownMenuTrigger
} from "@/components/ui/dropdown-menu";
import { Button, buttonVariants } from "@/components/ui/button";
import { Link, usePage } from "@inertiajs/vue3";
import { CollapsibleTrigger } from "radix-vue";
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
import { GlobalFiltersData, LogoData } from "@/types";
import GlobalFilters from "@/filters/components/GlobalFilters.vue";
import ColorModeDropdown from "@/components/ColorModeDropdown.vue";
import SharpLogoMini from '../../svg/logo-mini.svg';

const dialogs = useDialogs();
const menu = useMenu();
const globalFilters = usePage().props.globalFilters as GlobalFiltersData | null;
</script>

<template>
    <div class="min-h-screen w-full md:grid md:grid-cols-[220px_1fr] lg:grid-cols-[280px_1fr]">
        <div class="hidden border-r bg-muted/40 md:block">
            <div class="flex h-full max-h-screen flex-col gap-2 sticky top-0">
                <div class="flex h-14 items-center border-b px-4 lg:h-[60px] lg:px-6">
                    <template v-if="$page.props.logo">
                        <Logo />
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
                <div class="flex-1 px-2 lg:px-4">
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
                                            <h2 class="flex-1 text-lg font-semibold pl-3 tracking-tight">
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
                                                                class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all hover:text-primary"
                                                                :class="childItem.current ? 'bg-muted text-primary' : 'text-muted-foreground'"
                                                            >
                                                                <i class="fa fa-fw h-4 w-4" :class="childItem.icon"></i>
                                                                {{ childItem.label }}
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
                                    class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all hover:text-primary"
                                    :class="item.current ? 'bg-muted text-primary' : 'text-muted-foreground'"
                                >
                                    <i class="fa fa-fw h-4 w-4" :class="item.icon"></i>
                                    {{ item.label }}
                                </component>
                            </template>
                        </template>
                    </nav>
                </div>
            </div>
        </div>
        <div class="flex flex-col min-w-0">
            <header class="flex h-14 items-center gap-4 border-b bg-muted/40 backdrop-blur px-4 sticky top-0 z-20 lg:h-[60px] lg:px-6">
<!--                <div class="absolute inset-0 bg-muted/40 -z-10"></div>-->
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
                                                            class="mx-[-0.65rem] flex items-center gap-4 rounded-xl px-3 py-2 text-muted-foreground hover:text-foreground"
                                                            :class="childItem.current ? 'bg-muted text-foreground' : 'text-muted-foreground'"
                                                        >
                                                            <i class="fa h-4 w-4" :class="childItem.icon"></i>
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
                                        class="mx-[-0.65rem] flex items-center gap-4 rounded-xl px-3 py-2 text-muted-foreground hover:text-foreground"
                                        :class="item.current ? 'bg-muted text-foreground' : 'text-muted-foreground'"
                                    >
                                        <i class="fa h-4 w-4" :class="item.icon"></i>
                                        {{ item.label }}
                                    </component>
                                </template>
                            </template>
                        </nav>
                    </SheetContent>
                </Sheet>
                <slot name="breadcrumb" />
                <div class="flex-1 self-stretch ml-3" data-topbar-sticky-safe-area>
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
                <ColorModeDropdown />
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="secondary" size="icon" class="rounded-full">
                            <CircleUser class="h-5 w-5" />
                            <span class="sr-only">Toggle user menu</span>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
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
                                            <i class="fa w-4 h-4 mr-2" :class="item.icon"></i>
                                            <span>
                                                {{ item.label }}
                                            </span>
                                        </DropdownMenuItem>
                                    </template>
                                </template>
                            </DropdownMenuGroup>
                        </template>
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
            <main class="flex-1 pt-4 lg:pt-6 pb-24">
                <slot />
            </main>
        </div>
    </div>

    <Notifications />

    <template v-for="dialog in dialogs" :key="dialog.id">
        <AlertDialog
            v-model:open="dialog.open"
            @update:open="(open) => !open && window.setTimeout(() => dialog.onHidden(), 200)"
        >
            <AlertDialogContent>
                <AlertDialogHeader>
                    <template v-if="dialog.title">
                        <AlertDialogTitle>
                            {{ dialog.title }}
                        </AlertDialogTitle>
                    </template>
                    <AlertDialogDescription>
                        {{ dialog.text }}
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <template v-if="!dialog.okOnly">
                        <AlertDialogCancel>
                            {{ __('sharp::modals.cancel_button') }}
                        </AlertDialogCancel>
                    </template>
                    <AlertDialogAction :class="buttonVariants({ variant: 'destructive' })" @click="dialog.onOk()">
                        {{ dialog.okTitle ?? __('sharp::modals.ok_button') }}
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </template>
</template>
