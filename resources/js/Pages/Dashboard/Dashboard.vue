<script setup lang="ts">
    import { BreadcrumbData, DashboardData, FilterData } from "@/types";
    import { useFilters } from "@/filters/useFilters";
    import { parseQuery } from "@/utils/querystring";
    import { router } from "@inertiajs/vue3";
    import Layout from "@/Layouts/Layout.vue";
    import { route } from "@/utils/url";
    import Title from "@/components/Title.vue";
    import { useCommands } from "@/commands/useCommands";
    import { ref, watch } from "vue";
    import PageBreadcrumb from "@/components/PageBreadcrumb.vue";
    import { config } from "@/utils/config";
    import DashboardComponent from "@/dashboard/components/Dashboard.vue";
    import { Dashboard } from "@/dashboard/Dashboard";

    const props = defineProps<{
        dashboard: DashboardData,
        breadcrumb: BreadcrumbData,
    }>();

    const dashboardKey = route().params.dashboardKey as string;
    const dashboard = ref(new Dashboard(props.dashboard, dashboardKey));
    const filters = useFilters(props.dashboard.config.filters, props.dashboard.filterValues);
    const commands = useCommands('dashboard');

    watch(() => props.dashboard, () => {
        dashboard.value = new Dashboard(props.dashboard, dashboardKey);
        filters.update(props.dashboard.config.filters, props.dashboard.filterValues);
    });

    function onFilterChange(filter: FilterData, value: FilterData['value']) {
        router.post(
            route('code16.sharp.dashboard.filters.store', { dashboardKey }),
            {
                filterValues: filters.nextValues(filter, value),
                query: parseQuery(location.search),
            },
            { preserveState: true, preserveScroll: true },
        );
    }

    function onFiltersReset(resettedFilters: FilterData[]) {
        router.post(
            route('code16.sharp.dashboard.filters.store', { dashboardKey }),
            {
                filterValues: filters.defaultValues(resettedFilters),
                query: parseQuery(location.search),
            },
            { preserveState: true, preserveScroll: true },
        );
    }
</script>

<template>
    <Layout>
        <Title :entity-key="dashboardKey" />

        <template #breadcrumb>
            <template v-if="config('sharp.display_breadcrumb')">
                <PageBreadcrumb :breadcrumb="breadcrumb" />
            </template>
        </template>

        <div class="container @container mx-auto">
            <div :class="dashboard.pageAlert ? 'pt-4' : 'pt-10'">
                <DashboardComponent
                    :dashboard-key="dashboardKey"
                    :dashboard="dashboard"
                    :filters="filters"
                    :commands="commands"
                    :title="breadcrumb.items[0].label"
                    @filter-change="onFilterChange"
                    @filters-reset="onFiltersReset"
                />
            </div>
        </div>
    </Layout>
</template>
