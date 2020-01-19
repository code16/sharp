<template>
    <div class="SharpTabbedLayout">
        <template v-if="showTabs">
            <Tabs>
                <template slot="nav-prepend"><slot name="nav-prepend" /></template>
                <Tab v-for="(tab,i) in layout.tabs" :title="tab.title" :key="i">
                    <slot v-bind="tab"></slot>
                </Tab>
            </Tabs>
        </template>
        <template v-else>
            <div class="mb-3"><slot name="nav-prepend" /></div>
            <div v-for="tab in layout.tabs">
                <slot v-bind="tab"></slot>
            </div>
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
                return this.layout.tabbed && this.layout.tabs.length>1;
            }
        }
    }
</script>
