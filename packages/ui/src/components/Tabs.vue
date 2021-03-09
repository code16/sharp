<template>
    <b-tabs
        class="SharpTabs"
        nav-class="SharpTabs__nav mb-4 align-items-center"
        pills
    >
        <template v-slot:tabs-start>
            <li class="nav-item me-sm-auto">
                <slot name="nav-prepend" />
            </li>
        </template>

        <template v-slot:tabs-end>
            <li class="ms-auto"></li>
        </template>

        <slot />
    </b-tabs>
</template>

<script>
    import { BTabs, BTab } from 'bootstrap-vue';

    export default {
        name: 'SharpTabs',

        components: {
            BTabs,
            BTab,
        },

        computed: {
            // maybe useful for responsive
            dropdownButtonClasses() {
                return this.tabs[this.currentTab].hasError ? 'error-dot' : this.tabsHaveError ? 'error-dot--partial' : '';
            },
        },
    }
</script>

