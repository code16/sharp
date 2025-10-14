<script setup lang="ts">
    import { ChevronsUpDown } from "lucide-vue-next";
    import { ShowFieldProps } from "@/show/types";
    import {
        DashboardData,
        FilterData, ShowDashboardFieldData,
    } from "@/types";
    import { Ref, ref, } from "vue";
    import { useCommands } from "@/commands/useCommands";
    import { api } from "@/api/api";
    import { route } from "@/utils/url";
    import { FilterManager } from "@/filters/FilterManager";
    import { useFilters } from "@/filters/useFilters";
    import { CardContent, CardTitle } from "@/components/ui/card";
    import { Button } from "@/components/ui/button";
    import { hasPoppedState, useRemember } from "@/router";
    import { router } from "@inertiajs/vue3";
    import { Dashboard } from "@/dashboard/Dashboard";
    import { CommandResponseHandlers } from "@/commands/types";
    import DashboardComponent from "@/dashboard/components/Dashboard.vue";
    import RootCard from "@/components/ui/RootCard.vue";
    import RootCardHeader from "@/components/ui/RootCardHeader.vue";

    const props = defineProps<ShowFieldProps<ShowDashboardFieldData> & { ariaLabelledby?: string }>();

    const collapsed = ref(props.collapsable);
    const dashboard: Ref<Dashboard | null> = ref(null);
    const filters: FilterManager = useFilters();
    const reload: CommandResponseHandlers['reload'] = (data, { formModal }) => {
        onQueryChange(dashboard.value.query);
        router.reload({
            headers: {
                'X-No-Preload': '1',
            },
            only: ['notifications'],
        });
        formModal.shouldReopen && formModal.reopen();
    };
    const commands = useCommands('dashboard', {
        reload,
        refresh: (data, { formModal }) => {
            reload({ action: 'reload' }, { formModal });
        },
    });
    const remembered = useRemember({
        data: null as DashboardData | null,
        collapsed: collapsed.value, // TODO handle remembered collapse state
    }, `dashboard_${props.field.key}`);


    if(remembered.value.data && !collapsed.value) {
        update(remembered.value.data);
    }

    if(!hasPoppedState() && !collapsed.value) {
        init();
    }

    async function init() {
        const data = await api.get(props.field.endpointUrl, {
            preloaded: true,
        })
            .then(response => response.data as DashboardData);

        update(data);
    }

    function update(data: DashboardData) {
        dashboard.value = new Dashboard(
            data,
            props.field.dashboardKey,
            props.field.hiddenFilters,
            props.field.hiddenCommands,
        );
        filters.update(
            data.config.filters,
            data.filterValues
        );
        remembered.value.data = data;
    }

    async function onQueryChange(newQuery: DashboardData['query']) {
        const data = await api.get(
            route('code16.sharp.api.dashboard', { entityKey: props.field.dashboardKey }),
            { params: newQuery }
        )
            .then(response => response.data as DashboardData);

        update(data);
    }

    async function onFilterChange(filter: FilterData, value: FilterData['value']) {
        const data = await api.post(
            route('code16.sharp.api.dashboard.filters.store', { entityKey: props.field.dashboardKey }),
            {
                query: dashboard.value.query,
                filterValues: filters.nextValues(filter, value),
                hiddenFilters: props.field.hiddenFilters,
            }
        )
            .then(response => response.data as DashboardData);

        update(data);
    }

    async function onFiltersReset(resettedFilters: FilterData[]) {
        const data = await api.post(
            route('code16.sharp.api.dashboard.filters.store', { entityKey: props.field.dashboardKey }),
            {
                query: { ...dashboard.value.query, search: null },
                filterValues: filters.defaultValues(resettedFilters),
                hiddenFilters: props.field.hiddenFilters,
            }
        )
            .then(response => response.data as DashboardData);

        update(data);
    }

    function onToggle() {
        collapsed.value = !collapsed.value;
        remembered.value.collapsed = collapsed.value;
        if(!dashboard.value) {
            init();
        }
    }
</script>

<template>
    <RootCard>
        <RootCardHeader>
            <CardTitle :id="ariaLabelledby" class="line-clamp-2">
                {{ field.label }}
            </CardTitle>
            <template v-if="collapsable">
                <Button variant="ghost" size="sm" class="w-9 p-0 -my-1.5 -mr-3" @click="onToggle">
                    <ChevronsUpDown class="w-4 h-4" />
                </Button>
            </template>
        </RootCardHeader>
        <CardContent v-show="!collapsed">
            <DashboardComponent
                :dashboard="dashboard"
                :dashboard-key="field.dashboardKey"
                :filters="filters"
                :commands="commands"
                :collapsed="collapsed"
                :title="field.label"
                inline
                @filter-change="onFilterChange"
                @filters-reset="onFiltersReset"
                v-bind="$attrs"
            />
        </CardContent>
    </RootCard>
</template>
