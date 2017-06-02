<template>
    <sharp-action-bar>
        <template slot="left">

        </template>
        <template slot="right">
            <button class="btn btn-primary" @click="emitAction('main-button-clicked')">Envoyer</button>
        </template>
    </sharp-action-bar>
</template>

<script>
    import ActionBar from './ActionBar';
    import ActionBarMixin from './ActionBarMixin';

    export default {
        name: 'SharpActionBarForm',
        mixins: [ActionBarMixin],
        components: {
            [ActionBar.name]:ActionBar
        }
    }
</script>