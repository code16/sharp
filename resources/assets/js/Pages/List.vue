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
    }
</script>
