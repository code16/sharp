<template>
    <div class="SharpTabbedLayout">
        <template v-if="showTabs">
            <Tabs>
                <template v-slot:nav-prepend>
                    <slot name="nav-prepend" />
                </template>
                <template v-for="(tab, i) in layout.tabs">
                    <Tab :title="tab.title" :key="`tab-${i}`">
                        <slot :tab="tab" />
                    </Tab>
                </template>
            </Tabs>
        </template>
        <template v-else>
            <div class="mb-3">
                <slot name="nav-prepend" />
            </div>
            <template v-for="tab in layout.tabs">
                <slot :tab="tab" />
            </template>
        </template>
    </div>
</template>

<script>
    import Tabs from './Tabs';
    import Tab from './Tab';

    export default {
        name:'SharpTabbedLayout',
        props : {
            layout: Object,
        },
        provide() {
            if(!this.showTabs) {
                return { $tab: false }
            }
        },
        components: {
            Tabs,
            Tab,
        },
        computed: {
            showTabs() {
                return this.layout.tabbed && this.layout.tabs.length > 1;
            }
        }
    }
</script>
