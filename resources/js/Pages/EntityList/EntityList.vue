<script setup lang="ts">
    import Layout from "@/Layouts/Layout.vue";
    import { EntityList as EntityListComponent, EntityListTitle } from "@sharp/entity-list";
    import { BreadcrumbData, EntityListData, EntityListQueryParamsData } from "@/types";
    import Title from "@/components/Title.vue";
    import { config } from "@/utils/config";
    import Breadcrumb from "@/components/Breadcrumb.vue";
    import { EntityList } from "@sharp/entity-list/src/EntityList";
    import { useFilters } from "@sharp/filters";
    import { useCommands } from "@sharp/commands/src/useCommands";
    import { ref, Ref } from "vue";
    import { parseQuery, stringifyQuery } from "@/utils/querystring";
    import { router } from "@inertiajs/vue3";
    import { FilterQueryParams } from "@sharp/filters/src/types";

    const props = defineProps<{
        entityList: EntityListData,
        breadcrumb: BreadcrumbData,
    }>();

    const entityKey = route().params.entityKey as string;
    const entityList: Ref<EntityList> = ref(new EntityList(props.entityList, entityKey));
    const filters = useFilters(entityList.value.config.filters);
    const commands = useCommands({
        refresh: (data) => {
            entityList.value = entityList.value.withRefreshedItems(data.items)
        },
    });
    const query = parseQuery(location.search) as (EntityListQueryParamsData & FilterQueryParams);

    filters.setValuesFromQuery(query);

    function onQueryChange(query) {
        if(location.search !== stringifyQuery(query)) {
            router.visit(route('code16.sharp.list', { entityKey }) + stringifyQuery(query));
        }
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
                @update:query="onQueryChange"
            >
                <template v-slot:title>
                    <EntityListTitle :count="entityList.count">
                        <template v-if="config('sharp.display_breadcrumb')">
                            <Breadcrumb :breadcrumb="breadcrumb" />
                        </template>
                    </EntityListTitle>
                </template>
            </EntityListComponent>
        </div>
    </Layout>
</template>
