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
    import { ref, Ref, watch } from "vue";
    import { parseQuery, stringifyQuery } from "@/utils/querystring";
    import { router } from "@inertiajs/vue3";
    import { FilterQueryParams } from "@/filters/types";
    import { route } from "@/utils/url";
    import PageBreadcrumb from "@/components/PageBreadcrumb.vue";
    import { CardTitle } from "@/components/ui/card";

    const props = defineProps<{
        entityList: EntityListData,
        breadcrumb: BreadcrumbData,
    }>();

    const entityKey = route().params.entityKey as string;
    const entityList: Ref<EntityList> = ref(new EntityList(props.entityList, entityKey));
    const filters = useFilters(entityList.value.config.filters, entityList.value.filterValues);
    const commands = useCommands('entityList', {
        refresh: (data, { formModal }) => {
            entityList.value = entityList.value.withRefreshedItems(data.items);
            formModal.shouldReopen && formModal.reopen();
        },
    });

    watch(() => props.entityList, () => {
        entityList.value = new EntityList(props.entityList, entityKey);
        filters.update(props.entityList.config.filters, props.entityList.filterValues);
    });

    function onQueryChange(query: FilterQueryParams) {
        if(stringifyQuery(entityList.value.query) !== stringifyQuery(query)) {
            router.visit(route('code16.sharp.list', { entityKey }) + stringifyQuery(query), {
                preserveState: true,
                preserveScroll: false,
            });
        }
    }

    function onFilterChange(filter: FilterData, value: FilterData['value']) {
        router.post(
            route('code16.sharp.list.filters.store', { entityKey }),
            {
                filterValues: filters.nextValues(filter, value),
                query: entityList.value.query,
            },
            { preserveState: true, preserveScroll: false }
        );
    }

    function onReset() {
        router.post(
            route('code16.sharp.list.filters.store', { entityKey }),
            {
                filterValues: filters.defaultValues(filters.rootFilters),
                query: { ...entityList.value.query, search: null },
            },
            { preserveState: true, preserveScroll: false }
        );
    }
</script>

<template>
    <Layout>
        <Title :breadcrumb="breadcrumb" />

        <template #breadcrumb>
            <PageBreadcrumb :breadcrumb="breadcrumb" />
        </template>

        <EntityListComponent
            :entity-key="entityKey"
            :entity-list="entityList"
            :filters="filters"
            :commands="commands"
            :title="breadcrumb.items[0].label"
            @reset="onReset"
            @filter-change="onFilterChange"
            @update:query="onQueryChange"
        >
            <template #card-header>
                <CardTitle>
                    {{ entityList.title }}
                </CardTitle>
            </template>
        </EntityListComponent>
    </Layout>
</template>
