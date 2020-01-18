<template>
    <div class="SharpEntityListPage">
        <div class="container">
            <template v-if="ready">
                <!-- Action bar is defined inside entity list component -->
                <SharpEntityList
                    :entity-key="entityKey"
                    module="entity-list"
                >
                    <template slot="action-bar" slot-scope="{ props, listeners }">
                        <SharpActionBarList v-bind="props" v-on="listeners" />
                    </template>
                </SharpEntityList>
            </template>
        </div>
    </div>
</template>

<script>
    import isEqual from 'lodash/isEqual';
    import { mapGetters } from 'vuex';
    import SharpEntityList from '../list/EntityList';
    import SharpActionBarList from '../action-bar/ActionBarList';

    export default {
        name: 'SharpEntityListPage',
        components: {
            SharpEntityList,
            SharpActionBarList,
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
                return this.$route.params.id;
            },
        },
        methods: {
            handleQueryChanged(query, oldQuery) {
                if(isEqual(query, oldQuery)) {
                    return;
                }
                this.$router.push({
                    query: { ...query }
                });
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