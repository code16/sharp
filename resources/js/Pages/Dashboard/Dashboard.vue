<script setup lang="ts">
    import { CommandData, DashboardData, FilterData } from "@/types";
    import { useFilters } from "@/filters/useFilters";
    import { parseQuery, stringifyQuery } from "@/utils/querystring";
    import { router } from "@inertiajs/vue3";
    import { SectionTitle } from '@/components/ui';
    import Widget from "@/dashboard/components/Widget.vue";
    import Layout from "@/Layouts/Layout.vue";
    import SharpFilter from '@/filters/components/Filter.vue';
    import CommandsDropdown from "@/commands/components/CommandsDropdown.vue";
    import WithCommands from "@/commands/components/WithCommands.vue";
    import { __ } from "@/utils/i18n";
    import { route } from "@/utils/url";
    import Title from "@/components/Title.vue";
    import { useCommands } from "@/commands/useCommands";
    import PageAlert from "@/components/PageAlert.vue";

    const props = defineProps<{
        dashboard: DashboardData,
    }>();

    const dashboardKey = route().params.dashboardKey as string;
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
            },
            entityKey: dashboardKey,
        });
    }
</script>

<template>
    <Layout>
        <Title :entity-key="dashboardKey" />

        <WithCommands :commands="commands">
            <div class="container mx-auto">
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

                <template v-if="dashboard.pageAlert">
                    <PageAlert
                        class="mb-3"
                        :page-alert="dashboard.pageAlert"
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

                            <div class="grid grid-cols-12 gap-4">
                                <template v-for="row in section.rows">
                                    <template v-for="widgetLayout in row">
                                        <div class="col-[span_var(--size)]" :style="{ '--size': widgetLayout.size }">
                                            <Widget
                                                :widget="dashboard.widgets[widgetLayout.key]"
                                                :value="dashboard.data[widgetLayout.key]"
                                            />
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </WithCommands>
    </Layout>
</template>
