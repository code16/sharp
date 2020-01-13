<template>
    <div class="SharpTabbedLayout">
        <template v-if="showTabs">
            <SharpTabs>
                <template slot="nav-prepend"><slot name="nav-prepend" /></template>
                <SharpTab v-for="(tab,i) in layout.tabs" :title="tab.title" :key="i">
                    <slot v-bind="tab"></slot>
                </SharpTab>
            </SharpTabs>
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
    import SharpTabs from './Tabs';
    import SharpTab from './Tab';

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
            SharpTabs,
            SharpTab,
        },
        computed: {
            showTabs() {
                return this.layout.tabbed && this.layout.tabs.length>1;
            }
        }
    }
</script>
