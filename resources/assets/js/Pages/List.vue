<template>
    <Layout>
        <div class="SharpEntityListPage" data-popover-boundary>
            <div class="container">
                <template v-if="ready">
                    <EntityList
                        :entity-key="entityKey"
                        :initial-data="$props"
                        module="entity-list"
                    >
                        <template v-slot:action-bar="{ props, listeners }">
                            <ActionBar v-bind="props" v-on="listeners" />
                        </template>
                    </EntityList>
                </template>
            </div>
        </div>
    </Layout>
</template>

<script>
    import Layout from "../Layouts/Layout.vue";
    import { EntityList } from "sharp-entity-list";
    import ActionBar from "sharp-entity-list/src/components/ActionBar.vue";
    import { router } from "@inertiajs/vue2";
    import { mapGetters } from "vuex";
    import { parseQuery, stringifyQuery } from "../util/querystring";

    export default {
        components: {
            Layout,
            EntityList,
            ActionBar,
        },
        props: {
            entityKey: String,
            containers: null,
            layout: null,
            data: null,
            fields: null,
            config: null,
            authorizations: null,
            forms: null,
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
        async created() {
            await this.$store.dispatch('entity-list/setQuery', {
                ...parseQuery(location.search),
            });
            this.ready = true;
        },
    }
</script>
