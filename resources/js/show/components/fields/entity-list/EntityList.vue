<script setup lang="ts">
    import FieldLayout from "../../FieldLayout.vue";
    import { EntityList } from '@/entity-list/EntityList';
    import EntityListComponent from '@/entity-list/components/EntityList.vue'
    import EntityListTitle from '@/entity-list/components/EntityListTitle.vue'
    import { ShowFieldProps } from "@/show/types";
    import {
        EntityListData,
        EntityListQueryParamsData,
        FilterData,
        ShowEntityListFieldData
    } from "@/types";
    import { nextTick, Ref, ref } from "vue";
    import { useStickyLayout } from "./useStickyLayout";
    import { useCommands } from "@/commands/useCommands";
    import { api } from "@/api/api";
    import { FilterQueryParams, FilterValues } from "@/filters/types";
    import { route } from "@/utils/url";
    import { FilterManager } from "@/filters/FilterManager";
    import { useParentShow } from "@/show/useParentShow";

    const props = defineProps<ShowFieldProps<ShowEntityListFieldData>>();

    const el = ref();
    const show = useParentShow();
    const loading = ref(false);
    const collapsed = ref(props.collapsable);
    const { sticky, updateStickyLayout } = useStickyLayout(el);
    const entityList: Ref<EntityList | null> = ref(null);
    const filters: Ref<FilterManager | null> = ref(null);
    const currentQuery: Ref<EntityListQueryParamsData & FilterQueryParams> = ref({});
    const commands = useCommands({
        reload: () => {
            init();
        },
        refresh: (data) => {
            entityList.value = entityList.value.withRefreshedItems(data.items)
        },
    });

    async function init({ query, filterValues }: { query?: EntityListQueryParamsData & FilterQueryParams, filterValues?: FilterValues } = {}) {
        loading.value = true;
        const data = await api.post(
            route('code16.sharp.api.list.filters.store', { entityKey: props.field.entityListKey }),
            {
                query: query ?? currentQuery.value,
                filterValues: filterValues ?? filters.value.values,
                hiddenFilters: props.field.hiddenFilters ?? {},
            }
        )
            .then(response => response.data as EntityListData & {
                meta: EntityListData['meta'] & {
                    query: EntityListQueryParamsData & FilterQueryParams,
                }
            });

        loading.value = false;
        entityList.value = new EntityList(
            data,
            props.field.entityListKey,
            props.field.hiddenFilters,
            props.field.hiddenCommands,
        );
        filters.value = new FilterManager(
            entityList.value.config.filters,
            entityList.value.filterValues,
        );
        currentQuery.value = data.meta.query;
        await nextTick();
        updateStickyLayout();
    }

    function onQueryChange(newQuery) {
        init({
            query: newQuery
        });
    }

    function onFilterChange(filter: FilterData, value: FilterData['value']) {
        init({
            filterValues: filters.value.nextValues(filter, value),
        });
    }

    function onReset() {
        init({
            filterValues: filters.value.defaultValues(filters.value.rootFilters),
        });
    }

    function onToggle() {
        collapsed.value = !collapsed.value;
        if(!entityList.value) {
            init({
                filterValues: props.field.hiddenFilters ?? {}
            });
        }
    }

    if(!props.collapsable) {
        init({
            filterValues: props.field.hiddenFilters ?? {}
        });
    }
</script>

<template>
    <div ref="el">
<!--       <template v-if="collapsable">-->
<!--           <div class="section__header section__header&#45;&#45;collapsable position-relative">-->
<!--               <div class="row align-items-center gx-0 h-100">-->
<!--                   <div class="col-auto">-->
<!--                       <details :open="!collapsed" @toggle="onToggle">-->
<!--                           <summary class="stretched-link">-->
<!--                               <span class="visually-hidden">{{ field.label }}</span>-->
<!--                           </summary>-->
<!--                       </details>-->
<!--                   </div>-->
<!--                   <div class="col">-->
<!--                       <EntityListTitle :count="field.showCount ? entityList.count : null">-->
<!--                           <h2 class="ShowEntityListField__label section__title mb-0">-->
<!--                               {{ field.label }}-->
<!--                           </h2>-->
<!--                       </EntityListTitle>-->
<!--                   </div>-->
<!--               </div>-->
<!--           </div>-->
<!--       </template>-->
<!--       <template v-else>-->
<!--           <div class="section__header d-grid">-->
<!--               <EntityListTitle :count="field.showCount ? entityList.count : null">-->
<!--                   <h2 class="ShowEntityListField__label section__title mb-0">-->
<!--                       {{ field.label }}-->
<!--                   </h2>-->
<!--               </EntityListTitle>-->
<!--           </div>-->
<!--       </template>-->

       <EntityListComponent
           v-if="!collapsable || !collapsed"
           :entity-list="entityList"
           :entity-key="field.entityListKey"
           :query="currentQuery"
           :filters="filters"
           :commands="commands"
           :show-create-button="field.showCreateButton"
           :show-reorder-button="field.showReorderButton"
           :show-search-field="field.showSearchField"
           :show-entity-state="field.showEntityState"
           :loading="loading"
           inline
           @update:query="onQueryChange"
           @filter-change="onFilterChange"
           @reset="onReset"
           @reordering="$emit('reordering', $event)"
       >
           <template #title>
               {{ field.label }}
           </template>
<!--               <template v-slot:action-bar="{ props, listeners }">-->
<!--                   <ActionBar-->
<!--                       class="ShowEntityListField__action-bar"-->
<!--                       v-bind="props"-->
<!--                       v-on="listeners"-->
<!--                       :collapsed="collapsed"-->
<!--                       :sticky="sticky"-->
<!--                   >-->
<!--                      -->
<!--                   </ActionBar>-->
<!--               </template>-->
       </EntityListComponent>
   </div>
</template>
