<script setup lang="ts">
    import { onUnmounted, ref, watchEffect } from "vue";
    import { slugify } from "@/utils";
    import { Form } from "../Form";
    import { Tab, TabGroup, TabList, TabPanel, TabPanels } from "@headlessui/vue";

    const props = defineProps<{
        form: Form,
    }>();
    const selectedIndex = ref(0);

    function onTabChange(index: number) {
        selectedIndex.value = index;
    }

    function onPopState() {
        selectedIndex.value = Math.max(0, props.form.layout.tabs
            .findIndex(tab => new URLSearchParams(location.search).get('tab') == slugify(tab.title))
        );
    }
    window.addEventListener('popstate', onPopState);
    onUnmounted(() => window.removeEventListener('popstate', onPopState));
    watchEffect(() => {
        if(props.form.layout.tabs.length > 1) {
            history.replaceState(null, null, `?tab=${slugify(props.form.layout.tabs[selectedIndex.value].title)}`);
        }
    });
</script>
<template>
    <template v-if="form.layout.tabbed && form.layout.tabs.length > 1">
        <TabGroup :selected-index="selectedIndex" @change="onTabChange">
            <div class="sm:hidden">
                <select
                    class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-i-500 focus:outline-none focus:ring-i-500 sm:text-sm"
                    :value="selectedIndex"
                    @change="onTabChange($event.target.value)"
                >
                    <template v-for="(tab, i) in form.layout.tabs">
                        <option :value="i">{{ tab.title }}</option>
                    </template>
                </select>
            </div>
            <div class="hidden sm:block">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <TabList>
                            <template v-for="tab in form.layout.tabs">
                                <Tab v-slot="{ selected }">
                                    <button :class="[selected ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700', 'flex whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium']">
                                        {{ tab.title }}

                                        <template v-if="form.tabHasError(tab)">
                                            <svg class="h-1.5 w-1.5 fill-red-500" viewBox="0 0 6 6" aria-hidden="true">
                                                <circle cx="3" cy="3" r="3" />
                                            </svg>
                                        </template>
                                    </button>
                                </Tab>
                            </template>
                        </TabList>
                    </nav>
                </div>
            </div>

            <div class="bg-white p-4">
                <TabPanels>
                    <template v-for="tab in form.layout.tabs">
                        <TabPanel>
                            <slot :tab="tab" />
                        </TabPanel>
                    </template>
                </TabPanels>
            </div>
        </TabGroup>
    </template>
    <template v-else>
        <template v-for="tab in form.layout.tabs">
            <slot :tab="tab" />
        </template>
    </template>
</template>
