<template>
    <sharp-action-bar>
        <template slot="left">

        </template>
        <template slot="right">

        </template>
    </sharp-action-bar>
</template>

<script>
    import ActionBarMixin from './ActionBarMixin';
    import { Localization } from '../../mixins';

    export default {
        mixins: [ActionBarMixin, Localization]
    }
</script>