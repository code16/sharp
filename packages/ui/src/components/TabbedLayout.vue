<template>
    <div class="SharpTabbedLayout">
        <template v-if="showTabs">
            <Tabs>
                <template v-slot:nav-prepend>
                    <slot name="nav-prepend" />
                </template>
                <template v-for="(tab, i) in layout.tabs">
                    <Tab :title="tab.title" :active="isActive(tab)" @active="handleTabActivated(tab)" :key="`tab-${i}`">
                        <slot :tab="tab" />
                    </Tab>
                </template>
            </Tabs>
        </template>
        <template v-else>
            <template v-if="$slots['nav-prepend']">
                <div class="my-4">
                    <slot name="nav-prepend" />
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
    import Tabs from './Tabs';
    import Tab from './Tab';

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
