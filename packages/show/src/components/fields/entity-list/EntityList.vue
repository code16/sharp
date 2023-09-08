<script setup lang="ts">
    import FieldLayout from "../../FieldLayout.vue";
    import { EntityList as EntityListComponent, EntityListTitle } from '@sharp/entity-list';
    import { FieldProps } from "../../types";
    import { EntityListQueryParamsData, ShowEntityListFieldData } from "@/types";
    import { Ref, ref } from "vue";
    import { useStickyLayout } from "./useStickyLayout";
    import { EntityList } from "@sharp/entity-list/src/EntityList";
    import { useFilters } from "@sharp/filters";
    import { useCommands } from "@sharp/commands/src/useCommands";
    import { api } from "@/api";
    import { stringifyQuery } from "@/utils/querystring";

    const props = defineProps<FieldProps & {
        field: ShowEntityListFieldData,
        value: ShowEntityListFieldData['value'],
    }>();

    const el = ref();
    const collapsed = ref(props.collapsable);
    const { sticky, onListChange } = useStickyLayout(el);
    const entityKey = props.field.entityListKey;
    const entityList: Ref<EntityList | null> = ref(null);
    const filters = useFilters(entityList.value.config.filters);
    const commands = useCommands({
        reload: () => init(),
        refresh: (data) => {
            entityList.value = entityList.value.withRefreshedItems(data.items)
        },
    });
    const query: Ref<EntityListQueryParamsData & { [key: string]: any }> = ref({
        ...filters.getQueryParams(props.field.hiddenFilters)
    });

    async function init() {
        const data = await api.get(
            route('code16.sharp.api.list', { entityKey }) + stringifyQuery(query.value)
        ).then(response => response.data);

        entityList.value = new EntityList(data, entityKey);
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
               :entity-key="entityKey"
               :query="query"
               :filters="filters"
               :commands="commands"
               :show-create-button="field.showCreateButton"
               :show-reorder-button="field.showReorderButton"
               :show-search-field="field.showSearchField"
               :show-entity-state="field.showEntityState"
               :hidden-commands="field.hiddenCommands"
               :visible="!collapsed"
               inline
               @change="onListChange"
               @reordering="$emit('reordering', $event)"
           >
               <template v-slot:title>
                   <template v-if="collapsable">
                       <div class="section__header section__header--collapsable position-relative">
                           <div class="row align-items-center gx-0 h-100">
                               <div class="col-auto">
                                   <details :open="!collapsed" @toggle="collapsed = !$event.target.open">
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
