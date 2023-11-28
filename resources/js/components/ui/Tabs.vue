<template>
    <b-tabs
        class="SharpTabs"
        :nav-class="['SharpTabs__nav align-items-center nav-tabs', navClass]"
    >
        <template v-slot:tabs-end>
            <li class="nav-item ms-auto">
                <slot name="nav-append" />
            </li>
        </template>

        <slot />
    </b-tabs>
</template>

<script>
    export default {
        name: 'SharpTabs',

        props: {
            navClass: String,
        },

        components: {
            BTabs: {
                template: '<div><slot /></div>',  // todo
            },
        },

        computed: {
            // maybe useful for responsive
            dropdownButtonClasses() {
                return this.tabs[this.currentTab].hasError ? 'error-dot' : this.tabsHaveError ? 'error-dot--partial' : '';
            },
        },
    }
</script>

