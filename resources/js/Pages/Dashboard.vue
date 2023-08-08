<script setup lang="ts">
    import { DashboardData, FilterData } from "@/types";
    import { useFilters } from "@sharp/filters";
    import { parseQuery, stringifyQuery } from "@/utils/querystring";
    import { router } from "@inertiajs/vue3";
    import { GlobalMessage, } from '@sharp/ui';
    import { CommandFormModal, CommandViewPanel } from '@sharp/commands';
    import Widget from "@sharp/dashboard/src/components/Widget.vue";
    import Layout from "../Layouts/Layout.vue";
    import Section from '@sharp/dashboard/src/components/Section.vue';
    import { SharpFilter } from '@sharp/filters';
    import { CommandsDropdown } from '@sharp/commands';
    import { __ } from "@/utils/i18n";

    const props = defineProps<{
        dashboard: DashboardData,
    }>();

    const filters = useFilters(props.dashboard.config.filters);
    filters.setValuesFromQuery(parseQuery(location.search));

    console.log(parseQuery(location.search), filters);

    function onFilterChanged(filter: FilterData, value) {
        router.get(route('code16.sharp.dashboard', {
            dashboardKey: route().params.dashboardKey,
        }) + stringifyQuery({
            ...filters.nextQuery(filter, value),
        }));
    }

    function onFiltersReset(resettedFilters: FilterData[]) {
        router.get(route('code16.sharp.dashboard', {
            dashboardKey: route().params.dashboardKey,
        }) + stringifyQuery({
            ...filters.defaultQuery(resettedFilters),
        }));
    }
</script>

<template>
    <Layout>
        <div class="SharpDashboardPage">
            <div class="container">
                <div class="my-4">
                    <div class="row gx-3">
                        <div class="col">
                            <template v-if="filters.rootFilters.length > 0">
                                <div class="row gx-2">
                                    <template v-for="filter in filters.rootFilters" :key="filter.key">
                                        <div class="col-auto">
                                            <SharpFilter
                                                :filter="filter"
                                                :value="filters.values?.[filter.key]"
                                                @input="onFilterChanged(filter, $event)"
                                            />
                                        </div>
                                    </template>
                                    <template v-if="filters.isValuated(filters.rootFilters)">
                                        <div class="col-auto d-flex">
                                            <button
                                                class="btn btn-sm btn-link d-inline-flex align-items-center fs-8"
                                                @click="onFiltersReset(filters.rootFilters)"
                                            >
                                                {{ __('sharp::filters.reset_all') }}
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <template v-if="dashboard.config.commands?.dashboard?.length">
                            <div class="col-auto">
                                <CommandsDropdown
                                    :commands="dashboard.config.commands.dashboard"
                                    @select="handleCommandRequested"
                                >
                                    <template v-slot:text>
                                        {{ __('sharp::dashboard.commands.dashboard.label') }}
                                    </template>
                                </CommandsDropdown>
                            </div>
                        </template>
                    </div>
                </div>

                <template v-if="dashboard.config.globalMessage">
                    <GlobalMessage
                        :options="dashboard.config.globalMessage"
                        :data="dashboard.data"
                        :fields="dashboard.fields"
                    />
                </template>

                <div class="mb-n4.5">
                    <template v-for="section in dashboard.layout.sections">
                        <Section class="mb-4.5"
                            :section="section"
                            :commands="dashboard.config.commands?.[section.key]"
                            :filters="filters.filters?.[section.key]"
                            :filter-values="filters.values"
                            :show-reset="filters.isValuated(dashboard.config.filters?.[section.key] ?? [])"
                            @filter-change="onFilterChanged"
                            @filters-reset="onFiltersReset"
                            v-slot="{ widgetLayout }"
                        >
                            <Widget
                                :widget-type="dashboard.widgets[widgetLayout.key].type"
                                :widget-props="dashboard.widgets[widgetLayout.key]"
                                :value="dashboard.data[widgetLayout.key]"
                            />
                        </Section>
                    </template>
                </div>
            </div>

            <CommandFormModal
                :command="currentCommand"
                :entity-key="route().params.dashboardKey"
                v-bind="commandFormProps"
                v-on="commandFormListeners"
            />
            <CommandViewPanel
                :content="commandViewContent"
                @close="handleCommandViewPanelClosed"
            />
        </div>
    </Layout>
</template>

<script lang="ts">
    import { withCommands } from "sharp/mixins";
    import { getDashboardCommandForm, postDashboardCommand } from "@sharp/dashboard/src/api";

    export default {
        mixins: [withCommands],
        methods: {
            handleCommandRequested(command) {
                const query = {
                    ...useFilters(this.dashboard.config.filters).getQueryParams(parseQuery(location.search)),
                    ...parseQuery(location.search),
                };
                this.sendCommand(command, {
                    postCommand: data => postDashboardCommand({
                        dashboardKey: route().params.dashboardKey,
                        commandKey: command.key,
                        query,
                        data,
                    }),
                    getForm: commandQuery => getDashboardCommandForm({
                        dashboardKey: route().params.dashboardKey,
                        commandKey: command.key,
                        query: { ...query, ...commandQuery },
                    }),
                });
            },
        },
    }
</script>
