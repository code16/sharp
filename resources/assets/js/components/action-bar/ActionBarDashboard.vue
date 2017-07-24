<template>
    <sharp-action-bar></sharp-action-bar>
</template>

<script>
    import ActionBar from './ActionBar';
    import ActionBarMixin from './ActionBarMixin';

    export default {
        name: 'SharpActionBarDashboard',
        mixins: [ActionBarMixin],
        components: {
            [ActionBar.name]:ActionBar
        }
    }
</script>