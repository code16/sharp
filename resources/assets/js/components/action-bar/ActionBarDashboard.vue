<template>
    <sharp-action-bar></sharp-action-bar>
</template>

<script>
    import SharpActionBar from './ActionBar';
    import ActionBarMixin from './ActionBarMixin';

    export default {
        name: 'SharpActionBarDashboard',
        mixins: [ActionBarMixin],
        components: {
            SharpActionBar
        }
    }
</script>