<template>
    <div class="SharpEntityListPage" data-popover-boundary>
        <div class="container">
            <template v-if="ready">
                <!-- Action bar is defined inside entity list component -->
                <EntityList
                    :entity-key="entityKey"
                    module="entity-list"
                    @error="handleError"
                >
                    <template v-slot:action-bar="{ props, listeners }">
                        <ActionBarList v-bind="props" v-on="listeners" />
                    </template>
                </EntityList>
            </template>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import EntityList from '../EntityList.vue';
    import ActionBarList from '../ActionBar.vue';

    export default {
        name: 'SharpEntityListPage',
        components: {
            EntityList,
            ActionBarList,
        },
        data() {
            return {
                ready: false,
            }
        },
        watch: {
            'query': 'handleQueryChanged',
            '$route.query': 'init',
        },
        computed: {
            ...mapGetters('entity-list', [
                'query',
            ]),
            entityKey() {
                return this.$route.params.entityKey;
            },
        },
        methods: {
            handleQueryChanged(query) {
                const nextRoute = this.$router.resolve({ query }).route;
                if(this.$route.fullPath === nextRoute.fullPath) {
                    return;
                }
                this.$router.push({
                    query: { ...query }
                });
            },
            handleError(error) {
                this.$emit('error', error);
            },
            async init() {
                await this.$store.dispatch('entity-list/setQuery', { ...this.$route.query });
                this.ready = true;
            },
        },
        created() {
            this.init();
        },
    }
</script>
