<script setup lang="ts">
    import { onUnmounted, ref } from "vue";
    import { Tab, Tabs } from "@sharp/ui";
    import { FormLayoutData, FormLayoutTabData } from "@/types";
    import { slugify } from "@/utils";

    defineProps<{ layout:FormLayoutData }>();

    const query = ref(new URLSearchParams(location.search));
    const onPopState = () => {
        query.value = new URLSearchParams(location.search);
    }
    window.addEventListener('popstate', onPopState);
    onUnmounted(() => window.removeEventListener('popstate', onPopState));

    function isActive(tab: FormLayoutTabData) {
        return query.value.get('tab') === slugify(tab.title);
    }

    function onTabClicked() {
        history.replaceState(null, null, `?${query.value}`);
    }
</script>
<template>
    <div class="SharpTabbedLayout">
        <template v-if="layout.tabbed && layout.tabs.length > 1">
            <Tabs nav-class="bg-white border-bottom p-3 pb-0">
                <template v-for="(tab, i) in layout.tabs" :key="`tab-${i}`">
                    <Tab :title="tab.title" :active="isActive(tab)" @active="onTabClicked(tab)">
                        <slot :tab="tab" />
                    </Tab>
                </template>
                <template v-slot:nav-append>
                    <slot name="nav-append" />
                </template>
            </Tabs>
        </template>
        <template v-else>
            <template v-if="$slots['nav-append']">
                <div class="d-flex justify-content-end bg-white border-bottom p-3">
                    <slot name="nav-append" />
                </div>
            </template>
            <div class="tab-pane">
                <template v-for="tab in layout.tabs">
                    <slot :tab="tab" />
                    <hr class="SharpTabbedLayout__divider">
                </template>
            </div>
        </template>
    </div>
</template>
