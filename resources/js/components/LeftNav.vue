<script setup lang="ts">
import {usePage} from "@inertiajs/vue3";
import {config} from "@/util/config";
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";
import { ChevronRightIcon } from '@heroicons/vue/20/solid'

const menu = usePage().props.menu as Code16.Sharp.Data.MenuData;
</script>

<template>
    <tw-scoped>
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <!-- Sidebar component, swap this element with another sidebar if you like -->
            <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6 pb-4">
                <div class="flex h-16 shrink-0 items-center">
                    <img class="h-8 w-auto" :src="menu.logo" :alt="config('sharp.name')" />
                </div>
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                <li v-for="item in menu.items">
                                    <template v-if="item.children">
                                        <Disclosure as="div" default-open v-slot="{ open }">
                                            <DisclosureButton :class="[item.current ? 'bg-gray-50' : 'hover:bg-gray-50', 'flex items-center w-full text-left rounded-md p-2 gap-x-3 text-sm leading-6 font-semibold text-gray-700']">
                                                <ChevronRightIcon :class="[open ? 'rotate-90 text-gray-500' : 'text-gray-400', 'h-5 w-5 shrink-0']" aria-hidden="true" />
                                                {{ item.label }}
                                            </DisclosureButton>
                                            <DisclosurePanel as="ul" class="mt-1 px-2">
                                                <li v-for="subItem in item.children" :key="subItem.label">
                                                    <DisclosureButton as="a" :href="subItem.url" :class="[subItem.current ? 'bg-gray-50' : 'hover:bg-gray-50', 'block rounded-md py-2 pr-2 pl-9 text-sm leading-6 text-gray-700']">
<!--                                                        <template v-if="item.icon">-->
<!--                                                            <i class="fa fa-fw" :class="[item.icon, item.current ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600']"></i>-->
<!--                                                        </template>-->
                                                        {{ subItem.label }}
                                                    </DisclosureButton>
                                                </li>
                                            </DisclosurePanel>
                                        </Disclosure>
                                    </template>
                                    <template v-else>
                                        <a :href="item.url" :class="[item.current ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50', 'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold']">
                                            <template v-if="item.icon">
                                                <i class="fa fa-fw" :class="[item.icon, item.current ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600']"></i>
                                            </template>
                                            {{ item.label }}
                                        </a>
                                    </template>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </tw-scoped>
</template>
