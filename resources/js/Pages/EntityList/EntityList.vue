<script setup lang="ts">
    import Layout from "@/Layouts/Layout.vue";
    import EntityListComponent from "@/entity-list/components/EntityList.vue";
    import EntityListTitle from "@/entity-list/components/EntityListTitle.vue";
    import { BreadcrumbData, EntityListData, EntityListQueryParamsData, FilterData } from "@/types";
    import Title from "@/components/Title.vue";
    import { config } from "@/utils/config";
    import Breadcrumb from "@/components/PageBreadcrumb.vue";
    import { EntityList } from "@/entity-list/EntityList";
    import { useFilters } from "@/filters/useFilters";
    import { useCommands } from "@/commands/useCommands";
    import { ref, Ref } from "vue";
    import { parseQuery, stringifyQuery } from "@/utils/querystring";
    import { router } from "@inertiajs/vue3";
    import { FilterQueryParams } from "@/filters/types";
    import { route } from "@/utils/url";
    import PageBreadcrumb from "@/components/PageBreadcrumb.vue";

    const props = defineProps<{
        entityList: EntityListData,
        breadcrumb: BreadcrumbData,
    }>();

    const entityKey = route().params.entityKey as string;
    const entityList: Ref<EntityList> = ref(new EntityList(props.entityList, entityKey));
    const filters = useFilters(entityList.value.config.filters, entityList.value.filterValues);
    const commands = useCommands({
        refresh: (data) => {
            entityList.value = entityList.value.withRefreshedItems(data.items)
        },
    });
    const query = parseQuery(location.search) as (EntityListQueryParamsData & FilterQueryParams);

    function onQueryChange(query: FilterQueryParams) {
        if(location.search !== stringifyQuery(query)) {
            router.visit(route('code16.sharp.list', { entityKey }) + stringifyQuery(query));
        }
    }

    function onReset() {
        router.post(
            route('code16.sharp.list.filters.store', { entityKey }),
            {
                filterValues: filters.defaultValues(filters.rootFilters),
                query,
            },
            { preserveState: false, preserveScroll: false }
        );
    }

    function onFilterChange(filter: FilterData, value: FilterData['value']) {
        router.post(
            route('code16.sharp.list.filters.store', { entityKey }),
            {
                filterValues: filters.nextValues(filter, value),
                query,
            },
            { preserveState: false, preserveScroll: false }
        );
    }
</script>

<template>
    <Layout>
        <Title :breadcrumb="breadcrumb" />

        <div class="container mx-auto">
            <EntityListComponent
                :entity-key="entityKey"
                :entity-list="entityList"
                :filters="filters"
                :commands="commands"
                :query="query"
                @reset="onReset"
                @filter-change="onFilterChange"
                @update:query="onQueryChange"
            >
                <template v-slot:breadcrumb>
                    <PageBreadcrumb class="mb-4" :breadcrumb="breadcrumb" />
                    <!--                    <EntityListTitle :count="entityList.count">-->
                    <!--                        <template v-if="config('sharp.display_breadcrumb')">-->
                    <!--                            <Breadcrumb :breadcrumb="breadcrumb" />-->
                    <!--                        </template>-->
                    <!--                    </EntityListTitle>-->
                </template>
                <template v-slot:title>
                    {{ breadcrumb.items[0].label }}
                </template>
            </EntityListComponent>
        </div>
    </Layout>
</template>
