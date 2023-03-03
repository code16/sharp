<template>
    <Layout>
        <div class="SharpEntityListPage" data-popover-boundary>
            <div class="container">
                <EntityList
                    :entity-key="route().params.entityKey"
                    :entity-list="entityList"
                    module="entity-list"
                >
                    <template v-slot:action-bar="{ props, listeners }">
                        <ActionBarList v-bind="props" v-on="listeners" />
                    </template>
                </EntityList>
            </div>
        </div>
    </Layout>
</template>

<script>
    import Layout from "../Layouts/Layout.vue";
    import { EntityList } from "sharp-entity-list";
    import ActionBarList from "sharp-entity-list/src/components/ActionBar.vue";
    import { router } from "@inertiajs/vue2";
    import { mapGetters } from "vuex";
    import { parseQuery, stringifyQuery } from "../util/querystring";

    export default {
        components: {
            Layout,
            EntityList,
            ActionBarList,
        },
        props: {
            entityList: Object,
        },
        data() {
            return {
                ready: false,
            }
        },
        watch: {
            'query': 'handleQueryChanged',
        },
        computed: {
            ...mapGetters('entity-list', [
                'query',
            ]),
        },
        methods: {
            handleQueryChanged(query) {
                if(location.search !== stringifyQuery(query)) {
                    this.$store.dispatch('setLoading', true);
                    router.visit(location.pathname + stringifyQuery(query), {
                        preserveState: true,
                        preserveScroll: true,
                        onFinish: () => {
                            this.$store.dispatch('setLoading', false);
                        }
                    });
                }
            },
        },
        created() {
            this.$store.dispatch('entity-list/setQuery', {
                ...parseQuery(location.search),
            });
        },
    }
</script>
