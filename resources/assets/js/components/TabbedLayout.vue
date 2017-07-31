<template>
    <div class="SharpTabbedLayout">
        <template v-if="showTabs">
            <sharp-b-tabs>
                <sharp-b-tab v-for="(tab,i) in layout.tabs" :title="tab.title" :key="i">
                    <slot v-bind="tab"></slot>
                </sharp-b-tab>
            </sharp-b-tabs>
        </template>
        <template v-else>
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
            [Tabs.name]:Tabs,
            [Tab.name]:Tab,
        },
        computed: {
            showTabs() {
                return this.layout.tabbed && this.layout.tabs.length>1;
            }
        }
    }
</script>