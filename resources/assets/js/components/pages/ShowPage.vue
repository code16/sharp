<template>
    <div class="SharpShowPage">
        <template v-if="ready">
            <SharpActionBarShow />
        </template>
    </div>
</template>

<script>
    import { mapState, mapGetters } from 'vuex';
    import SharpActionBarShow from "../action-bar/ActionBarShow";

    export default {
        components: {
            SharpActionBarShow,
        },

        data() {
            return {
                ready: false,
            }
        },

        computed: {
            ...mapState('show', {
                entityKey: state => state.entityKey,
            }),
            ...mapGetters('show', [
                'canEdit',
            ]),
        },

        methods: {
            async init() {
                await this.$store.dispatch('show/setEntityKey', this.$route.params.entityKey);
                await this.$store.dispatch('show/setInstanceId', this.$route.params.instanceId);
                await this.$store.dispatch('show/get');

                this.ready = true;
            }
        },

        created() {
            this.init();
        },
    }
</script>