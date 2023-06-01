<template>
    <b-tabs
        class="SharpTabs"
        :nav-class="['SharpTabs__nav align-items-center', navClass]"
        pills
    >
        <template v-slot:tabs-start>
            <li class="nav-item me-auto">
                <slot name="nav-prepend" />
            </li>
        </template>

        <template v-slot:tabs-end>
            <li class="nav-item ms-auto invisible">
                <div class="d-none d-md-block">
                    <slot name="nav-prepend" />
                </div>
            </li>
        </template>

        <slot />
    </b-tabs>
</template>

<script>
    import { BTabs } from 'bootstrap-vue';

    export default {
        name: 'SharpTabs',

        props: {
            navClass: String,
        },

        components: {
            BTabs,
        },

        computed: {
            // maybe useful for responsive
            dropdownButtonClasses() {
                return this.tabs[this.currentTab].hasError ? 'error-dot' : this.tabsHaveError ? 'error-dot--partial' : '';
            },
        },
    }
</script>

