<template>
    <div class="SharpTabbedLayout">
        <template v-if="showTabs">
            <Tabs nav-class="bg-white border-bottom p-3 pb-0">
                <template v-for="(tab, i) in layout.tabs">
                    <Tab :title="tab.title" :active="isActive(tab)" @active="handleTabActivated(tab)" :key="`tab-${i}`">
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

<script>
    import Tabs from './Tabs.vue';
    import Tab from './Tab.vue';

    function slugify(text) {
        return text
            .toLowerCase()
            .normalize("NFD").replace(/\p{Diacritic}/gu, "")
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
    }

    export default {
        name:'SharpTabbedLayout',
        props : {
            layout: Object,
        },
        components: {
            Tabs,
            Tab,
        },
        computed: {
            showTabs() {
                return this.layout.tabbed && this.layout.tabs.length > 1;
            },
        },
        methods: {
            isActive(tab) {
                return this.$route.query.tab === slugify(tab.title);
            },
            handleTabActivated(tab) {
                this.$router.replace({
                    query: {
                        ...this.$route.query,
                        tab: slugify(tab.title),
                    },
                })
            },
        },
    }
</script>
