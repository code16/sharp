<template>
    <div class="SharpFormLayout">
        <template v-if="layout.tabbed && layout.tabs.length>1">
            <b-tabs pills>
                <b-tab v-for="(tab,i) in layout.tabs" :title="tab.title" :key="i">
                    <slot v-bind="tab"></slot>
                </b-tab>
            </b-tabs>
        </template>
        <template v-else>
            <div v-for="tab in layout.tabs">
                <slot v-bind="tab"></slot>
            </div>
        </template>
    </div>
</template>

<script>
    import bTabs from './vendor/bootstrap-vue/components/tabs'
    import bTab from './vendor/bootstrap-vue/components/tab'
    export default {
        name:'SharpFormLayout',
        props : {
            layout: Object
        },
        components: {
            bTab, bTabs
        }
    }
</script>