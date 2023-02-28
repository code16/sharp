<template>
    <Layout>
        <div class="SharpEntityListPage" data-popover-boundary>
            <div class="container">
                <EntityList :entity-key="entityKey" module="entity-list">
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
        },
    }
</script>
