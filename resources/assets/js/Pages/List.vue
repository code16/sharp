<template>
    <Layout>
        <div class="SharpEntityListPage" data-popover-boundary>
            <div class="container">
                <EntityList
                    :entity-key="entityKey"
                    :initial-data="$props"
                    module="entity-list"
                >
                    <template v-slot:action-bar="{ props, listeners }">
                        <ActionBar v-bind="props" v-on="listeners" />
                    </template>
                </EntityList>
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
                const search = new URLSearchParams(query).toString();
                if(location.search.replace('?', '') !== search) {
                    router.get(location.pathname, { ...query });
                }
            },
        },
        created() {
            this.$store.dispatch('entity-list/setQuery', {
                ...Object.fromEntries(new URLSearchParams(location.search).entries()),
            });
        },
    }
</script>
