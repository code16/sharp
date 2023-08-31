<script setup lang="ts">
    import Layout from "@/Layouts/Layout.vue";
    import { EntityList, EntityListTitle } from "@sharp/entity-list";
    import { BreadcrumbData, EntityListData } from "@/types";
    import Title from "@/components/Title.vue";
    import { config } from "@/utils/config";
    import Breadcrumb from "@/components/Breadcrumb.vue";

    const props = defineProps<{
        entityList: EntityListData,
        breadcrumb: BreadcrumbData,
    }>();
</script>

<template>
    <Layout>
        <Title :breadcrumb="breadcrumb" />

        <div class="container">
            <EntityList
                :entity-key="route().params.entityKey"
                :entity-list="entityList"
                module="entity-list"
            >
                <template v-slot:title="{ count }">
                    <EntityListTitle :count="count">
                        <template v-if="config('sharp.display_breadcrumb')">
                            <Breadcrumb :breadcrumb="breadcrumb" />
                        </template>
                    </EntityListTitle>
                </template>
            </EntityList>
        </div>
    </Layout>
</template>

<script lang="ts">
    import { router } from "@inertiajs/vue3";
    import { mapGetters } from "vuex";
    import { parseQuery, stringifyQuery } from "@/utils/querystring";

    export default {
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
                    router.visit(route('code16.sharp.list', route().params) + stringifyQuery(query), {
                        preserveState: true,
                        preserveScroll: true,
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
