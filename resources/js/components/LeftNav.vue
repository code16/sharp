<script setup lang="ts">
import { usePage } from "@inertiajs/vue3";
import { config } from "@/util/config";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { ChevronRightIcon } from '@heroicons/vue/20/solid'
import { MenuData } from "@/types";
import { Link } from "@inertiajs/vue3";

const menu = usePage().props.menu as MenuData;
</script>

<template>
    <!-- Sidebar component, swap this element with another sidebar if you like -->
    <div class="flex grow flex-col  overflow-y-auto bg-white pb-4">
        <div class="flex h-16 shrink-0 items-center bg-indigo-600 px-6">
            <img class="h-auto w-32" :src="menu.logo" :alt="config('sharp.name')" />
        </div>
        <nav class="flex flex-1 flex-col px-6 pt-5 border-r border-gray-200">
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
                                    <DisclosurePanel as="ul" class="mt-1">
                                        <li v-for="childItem in item.children" :key="childItem.label">
                                            <template v-if="childItem.isSeparator">
                                                <hr>
                                            </template>
                                            <template v-else>
                                                <component :is="childItem.isExternalLink ? 'a' : Link" :href="childItem.url" :class="[childItem.current ? 'bg-gray-50' : 'hover:bg-gray-50', 'flex  items-center gap-x-3 rounded-md py-2 px-2 text-sm leading-6 text-gray-700']">
                                                    <template v-if="childItem.icon">
                                                        <i class="fa fa-fw" :class="[childItem.icon, childItem.current ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600']"></i>
                                                    </template>
                                                    {{ childItem.label }}
                                                </component>
                                            </template>
                                        </li>
                                    </DisclosurePanel>
                                </Disclosure>
                            </template>
                            <template v-else>
                                <component :is="item.isExternalLink ? 'a' : Link" :href="item.url" :class="[item.current ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50', 'group flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold']">
                                    <template v-if="item.icon">
                                        <i class="fa fa-fw" :class="[item.icon, item.current ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600']"></i>
                                    </template>
                                    {{ item.label }}
                                </component>
                            </template>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</template>
