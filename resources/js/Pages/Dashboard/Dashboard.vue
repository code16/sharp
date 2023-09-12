<script setup lang="ts">
    import { CommandData, DashboardData, FilterData } from "@/types";
    import { useFilters } from "@sharp/filters";
    import { parseQuery, stringifyQuery } from "@/utils/querystring";
    import { router } from "@inertiajs/vue3";
    import { GlobalMessage, Grid, SectionTitle, } from '@sharp/ui';
    import Widget from "@sharp/dashboard/src/components/Widget.vue";
    import Layout from "@/Layouts/Layout.vue";
    import { SharpFilter } from '@sharp/filters';
    import { CommandsDropdown } from '@sharp/commands';
    import { __ } from "@/utils/i18n";
    import { route } from "@/utils/url";
    import Title from "@/components/Title.vue";
    import { useCommands } from "@sharp/commands/src/useCommands";
    import { WithCommands } from "@sharp/commands";

    const props = defineProps<{
        dashboard: DashboardData,
    }>();

    const { dashboardKey } = route().params;
    const filters = useFilters(props.dashboard.config.filters);
    const commands = useCommands();

    filters.setValuesFromQuery(parseQuery(location.search));

    function onFilterChange(filter: FilterData, value: FilterData['value']) {
        router.get(route('code16.sharp.dashboard', { dashboardKey }) + stringifyQuery({
            ...filters.nextQuery(filter, value),
        }));
    }

    function onFiltersReset(resettedFilters: FilterData[]) {
        router.get(route('code16.sharp.dashboard', { dashboardKey }) + stringifyQuery({
            ...filters.defaultQuery(resettedFilters),
        }));
    }

    function onCommand(command: CommandData) {
        commands.send(command, {
            postCommand: route('code16.sharp.api.dashboard.command', { dashboardKey, commandKey: command.key }),
            getForm: route('code16.sharp.api.dashboard.command.form', { dashboardKey, commandKey: command.key }),
            query: {
                ...filters.getQueryParams(filters.values),
                ...parseQuery(location.search),
            }
        });
    }
</script>

<template>
    <Layout>
        <Title :entity-key="route().params.dashboardKey" />

        <WithCommands :commands="commands">
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
                                                :value="filters.values[filter.key]"
                                                @input="onFilterChange(filter, $event)"
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
                                    @select="onCommand"
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
                        <div class="section">
                            <div class="row align-items-center">
                                <div class="col">
                                    <SectionTitle
                                        :section="section"
                                    />
                                </div>
                                <template v-if="dashboard.config.filters?.[section.key]?.length || dashboard.config.commands?.[section.key]?.flat().length">
                                    <div class="col-auto align-self-end mb-2">
                                        <div class="row justify-content-end">
                                            <template v-if="dashboard.config.filters?.[section.key]?.length">
                                                <div class="col">
                                                    <div class="row row-cols-auto gx-2">
                                                        <template v-for="filter in dashboard.config.filters[section.key]" :key="filter.key">
                                                            <SharpFilter
                                                                :filter="filter"
                                                                :value="filters.values[filter.key]"
                                                                @input="onFilterChange(filter, $event)"
                                                            />
                                                        </template>
                                                        <template v-if="filters.isValuated(dashboard.config.filters[section.key])">
                                                            <div class="d-flex">
                                                                <button class="btn btn-sm d-inline-flex align-items-center btn-link" @click="onFiltersReset(dashboard.config.filters[section.key])">
                                                                    {{ __('sharp::filters.reset_all') }}
                                                                </button>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                            <template v-if="dashboard.config.commands?.[section.key]?.flat().length">
                                                <div class="col-auto">
                                                    <CommandsDropdown :commands="dashboard.config.commands[section.key]" @select="onCommand">
                                                        <template v-slot:text>
                                                            {{ __('sharp::dashboard.commands.dashboard.label') }}
                                                        </template>
                                                    </CommandsDropdown>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <Grid :rows="section.rows" row-class="g-3" v-slot="{ itemLayout: widgetLayout }">
                                <Widget
                                    :widget="dashboard.widgets[widgetLayout.key]"
                                    :value="dashboard.data[widgetLayout.key]"
                                />
                            </Grid>
                        </div>
                    </template>
                </div>
            </div>
        </WithCommands>
    </Layout>
</template>
