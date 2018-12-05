<template>
    <sharp-action-bar ready></sharp-action-bar>
</template>

<script>
    import SharpActionBar from './ActionBar';
    import ActionBarMixin from './ActionBarMixin';

    export default {
        name: 'SharpActionBarDashboard',
        mixins: [ActionBarMixin],
        components: {
            SharpActionBar
        },
        data() {
            return {
                filters: []
            }
        },
        actions: {
            setup({ filters }) {
                this.filters = filters;
            }
        }
    }
</script>