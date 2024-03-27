<script setup lang="ts">
    import FieldLayout from "../../FieldLayout.vue";
    import { EntityList } from '@/entity-list/EntityList';
    import EntityListComponent from '@/entity-list/components/EntityList.vue'
    import EntityListTitle from '@/entity-list/components/EntityListTitle.vue'
    import { ShowFieldProps } from "../../../types";
    import { EntityListQueryParamsData, ShowEntityListFieldData } from "@/types";
    import { Ref, ref } from "vue";
    import { useStickyLayout } from "./useStickyLayout";
    import { useFilters } from "@/filters/useFilters";
    import { useCommands } from "@/commands/useCommands";
    import { api } from "@/api";
    import { FilterQueryParams } from "@/filters/types";
    import { route } from "@/utils/url";

    const props = defineProps<ShowFieldProps<ShowEntityListFieldData>>();

    const el = ref();
    const loading = ref(false);
    const collapsed = ref(props.collapsable);
    const { sticky, onListChange } = useStickyLayout(el);
    const entityList: Ref<EntityList | null> = ref(null);
    const filters = useFilters();
    const query: Ref<EntityListQueryParamsData & FilterQueryParams> = ref({
        ...filters.getQueryParams(props.field.hiddenFilters)
    });
    const commands = useCommands({
        reload: () => init(query.value),
        refresh: (data) => {
            entityList.value = entityList.value.withRefreshedItems(data.items)
        },
    });

    async function init(query) {
        loading.value = true;
        const data = await api.get(
            route('code16.sharp.api.list', { entityKey: props.field.entityListKey }),
            { params: query }
        )
            .then(response => response.data);

        loading.value = false;
        entityList.value = new EntityList(
            data,
            props.field.entityListKey,
            props.field.hiddenFilters,
            props.field.hiddenCommands,
        );
        filters.filters = entityList.value.config.filters;
        filters.setValuesFromQuery(query);
    }

    function onToggle() {
        collapsed.value = !collapsed.value;
        if(!entityList.value) {
            init(query.value);
        }
    }

    async function onQueryChange(newQuery) {
        await init(newQuery);
        query.value = newQuery;
    }

    if(!props.collapsable) {
        init(query.value);
    }
</script>

<template>
    <div ref="el">
       <FieldLayout
           class="ShowEntityListField"
           :class="{
               'ShowEntityListField--collapsed': collapsed,
           }"
       >
           <EntityListComponent
               v-if="value"
               :entity-list="entityList"
               :entity-key="field.entityListKey"
               :query="query"
               :filters="filters"
               :commands="commands"
               :show-create-button="field.showCreateButton"
               :show-reorder-button="field.showReorderButton"
               :show-search-field="field.showSearchField"
               :show-entity-state="field.showEntityState"
               :loading="loading"
               inline
               @change="onListChange"
               @update:query="onQueryChange"
               @reordering="$emit('reordering', $event)"
           >
               <template v-slot:title>
                   <template v-if="collapsable">
                       <div class="section__header section__header--collapsable position-relative">
                           <div class="row align-items-center gx-0 h-100">
                               <div class="col-auto">
                                   <details :open="!collapsed" @toggle="onToggle">
                                       <summary class="stretched-link">
                                           <span class="visually-hidden">{{ field.label }}</span>
                                       </summary>
                                   </details>
                               </div>
                               <div class="col">
                                   <EntityListTitle :count="field.showCount ? entityList.count : null">
                                       <h2 class="ShowEntityListField__label section__title mb-0">
                                           {{ field.label }}
                                       </h2>
                                   </EntityListTitle>
                               </div>
                           </div>
                       </div>
                   </template>
                   <template v-else>
                       <div class="section__header d-grid">
                           <EntityListTitle :count="field.showCount ? entityList.count : null">
                               <h2 class="ShowEntityListField__label section__title mb-0">
                                   {{ field.label }}
                               </h2>
                           </EntityListTitle>
                       </div>
                   </template>
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
       </FieldLayout>
   </div>
</template>
