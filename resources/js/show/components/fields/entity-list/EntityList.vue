<script setup lang="ts">
    import { EntityList } from '@/entity-list/EntityList';
    import EntityListComponent from '@/entity-list/components/EntityList.vue'
    import { ChevronsUpDown } from "lucide-vue-next";
    import { ShowFieldProps } from "@/show/types";
    import {
        EntityListData,
        FilterData,
        ShowEntityListFieldData
    } from "@/types";
    import { nextTick, Ref, ref, toRaw, onBeforeUnmount, watch } from "vue";
    import { useCommands } from "@/commands/useCommands";
    import { api } from "@/api/api";
    import { route } from "@/utils/url";
    import { FilterManager } from "@/filters/FilterManager";
    import { useFilters } from "@/filters/useFilters";
    import { CardTitle } from "@/components/ui/card";
    import { Button } from "@/components/ui/button";
    import { hasPoppedState, useRemember } from "@/router";
    import { router } from "@inertiajs/vue3";

    const props = defineProps<ShowFieldProps<ShowEntityListFieldData> & { highlightedInstanceId?: string | number, ariaLabelledby?: string }>();

    const el = ref();
    const collapsed = ref(props.collapsable && !props.highlightedInstanceId);
    const entityList: Ref<EntityList | null> = ref(null);
    const filters: FilterManager = useFilters();
    const commands = useCommands('entityList', {
        reload: (data, { formModal }) => {
            init();
            router.reload();
            formModal.shouldReopen && formModal.reopen();
        },
        refresh: (data, { formModal }) => {
            entityList.value = entityList.value.withRefreshedItems(data.items);
            formModal.shouldReopen && formModal.reopen();
        },
    });
    const remembered = useRemember({
        data: null as EntityListData | null,
        collapsed: collapsed.value, // TODO handle remembered collapse state
    }, `entityList_${props.field.key}`);


    if(remembered.value.data && !collapsed.value) {
        update(remembered.value.data);
    }

    if(!hasPoppedState() && !collapsed.value) {
        init();
    }

    // console.log('setup', cloneDeep(remembered.value));
    // console.log('hasPoppedState EL', hasPoppedState());
    // console.log('remembered', remembered.value.data);

    async function init() {
        const data = await api.get(props.field.endpointUrl, {
            fetchOptions: { cache: 'no-store' } as RequestInit,
        })
            .then(response => response.data as EntityListData);

        update(data);
    }

    function update(data: EntityListData) {
        entityList.value = new EntityList(
            data,
            props.field.entityListKey,
            props.field.hiddenFilters,
            props.field.hiddenCommands,
        );
        filters.update(
            data.config.filters,
            data.filterValues
        );
        remembered.value.data = data;
    }

    async function onQueryChange(newQuery) {
        const data = await api.get(
            route('code16.sharp.api.list', { entityKey: props.field.entityListKey }),
            { params: newQuery }
        )
            .then(response => response.data as EntityListData);

        update(data);
    }

    async function onFilterChange(filter: FilterData, value: FilterData['value']) {
        const data = await api.post(
            route('code16.sharp.api.list.filters.store', { entityKey: props.field.entityListKey }),
            {
                query: entityList.value.query,
                filterValues: filters.nextValues(filter, value),
                hiddenFilters: props.field.hiddenFilters,
            }
        )
            .then(response => response.data as EntityListData);

        update(data);
    }

    async function onFilterResetAll() {
        const data = await api.post(
            route('code16.sharp.api.list.filters.store', { entityKey: props.field.entityListKey }),
            {
                query: { ...entityList.value.query, search: null },
                filterValues: filters.defaultValues(filters.rootFilters),
                hiddenFilters: props.field.hiddenFilters,
            }
        )
            .then(response => response.data as EntityListData);

        update(data);
    }

    function onToggle() {
        collapsed.value = !collapsed.value;
        remembered.value.collapsed = collapsed.value;
        if(!entityList.value) {
            init();
        }
    }
</script>

<template>
    <div ref="el">
       <EntityListComponent
           :entity-list="entityList"
           :entity-key="field.entityListKey"
           :filters="filters"
           :commands="commands"
           :show-create-button="field.showCreateButton"
           :show-reorder-button="field.showReorderButton"
           :show-search-field="field.showSearchField"
           :show-entity-state="field.showEntityState"
           :highlighted-instance-id="highlightedInstanceId"
           :collapsed="collapsed"
           :title="field.label"
           inline
           @update:query="onQueryChange"
           @filter-change="onFilterChange"
           @reset="onFilterResetAll"
           @reordering="$emit('reordering', $event)"
           v-bind="$attrs"
       >
           <template #card-header>
               <div class="flex items-center gap-x-4">
                   <CardTitle :id="ariaLabelledby" class="line-clamp-2">
                       {{ field.label }}
                   </CardTitle>
                   <template v-if="collapsable">
                       <Button variant="ghost" size="sm" class="w-9 p-0 -my-1.5 -mr-3" @click="onToggle">
                           <ChevronsUpDown class="w-4 h-4" />
                       </Button>
                   </template>
               </div>
           </template>
       </EntityListComponent>
   </div>
</template>
