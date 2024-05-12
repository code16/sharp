<script setup lang="ts">
    import { CommandData, DashboardData, FilterData } from "@/types";
    import { useFilters } from "@/filters/useFilters";
    import { parseQuery, stringifyQuery } from "@/utils/querystring";
    import { router } from "@inertiajs/vue3";
    import { SectionTitle } from '@/components/ui';
    import Widget from "@/dashboard/components/Widget.vue";
    import Layout from "@/Layouts/Layout.vue";
    import SharpFilter from '@/filters/components/Filter.vue';
    import WithCommands from "@/commands/components/WithCommands.vue";
    import { __ } from "@/utils/i18n";
    import { route } from "@/utils/url";
    import Title from "@/components/Title.vue";
    import { useCommands } from "@/commands/useCommands";
    import PageAlert from "@/components/PageAlert.vue";
    import { Button } from "@/components/ui/button";
    import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
    import CommandDropdownItems from "@/commands/components/CommandDropdownItems.vue";
    import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
    import { watch } from "vue";

    const props = defineProps<{
        dashboard: DashboardData,
    }>();

    const dashboardKey = route().params.dashboardKey as string;
    const filters = useFilters(props.dashboard.config.filters, props.dashboard.filterValues);
    const commands = useCommands();

    watch(() => props.dashboard, () => {
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

    function onCommand(command: CommandData) {
        commands.send(command, {
            postCommand: route('code16.sharp.api.dashboard.command', { dashboardKey, commandKey: command.key }),
            getForm: route('code16.sharp.api.dashboard.command.form', { dashboardKey, commandKey: command.key }),
            query: {
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
                <template v-if="dashboard.pageAlert">
                    <PageAlert
                        class="mb-4"
                        :page-alert="dashboard.pageAlert"
                    />
                </template>

                <div class="flex gap-3 mb-4">
                    <template v-if="filters.rootFilters.length > 0">
                        <div class="flex gap-3">
                            <template v-for="filter in filters.rootFilters" :key="filter.key">
                                <SharpFilter
                                    :filter="filter"
                                    :value="filters.currentValues[filter.key]"
                                    :valuated="filters.isValuated([filter])"
                                    @input="onFilterChange(filter, $event)"
                                />
                            </template>
                            <template v-if="filters.isValuated(filters.rootFilters)">
                                <Button class="h-8" size="sm" variant="link" @click="onFiltersReset(filters.rootFilters)">
                                    {{ __('sharp::filters.reset_all') }}
                                </Button>
                            </template>
                        </div>
                    </template>
                    <template v-if="dashboard.config.commands?.dashboard?.length">
                        <div class="ml-auto">
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button class="h-8" variant="outline" size="sm">
                                        {{ __('sharp::dashboard.commands.dashboard.label') }}
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent>
                                    <CommandDropdownItems
                                        :commands="dashboard.config.commands.dashboard"
                                        @select="onCommand"
                                    />
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                    </template>
                </div>

                <div class="grid gap-4 md:gap-8">
                    <template v-for="section in dashboard.layout.sections">
                        <section>
                            <template v-if="dashboard.config.filters?.[section.key]?.length || dashboard.config.commands?.[section.key]?.flat().length">
                                <div class="flex gap-3 mb-4">
                                    <template v-if="dashboard.config.filters?.[section.key]?.length">
                                        <div class="flex gap-3">
                                            <template v-for="filter in dashboard.config.filters[section.key]" :key="filter.key">
                                                <SharpFilter
                                                    :filter="filter"
                                                    :value="filters.currentValues[filter.key]"
                                                    :valuated="filters.isValuated([filter])"
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
                                    </template>
                                    <template v-if="dashboard.config.commands?.[section.key]?.flat().length">
                                        <div class="ml-auto">
                                            <DropdownMenu>
                                                <DropdownMenuTrigger as-child>
                                                    <Button class="h-8" variant="outline" size="sm">
                                                        {{ __('sharp::dashboard.commands.dashboard.label') }}
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent>
                                                    <CommandDropdownItems
                                                        :commands="dashboard.config.commands[section.key]"
                                                        @select="onCommand"
                                                    />
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <Card>
                                <template v-if="section.title">
                                    <CardHeader>
                                        <CardTitle>
                                            {{ section.title }}
                                        </CardTitle>
                                    </CardHeader>
                                </template>
                                <template v-else>
                                    <CardHeader class="pb-0" />
                                </template>
                                <CardContent>
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
                                </CardContent>
                            </Card>
                        </section>
                    </template>
                </div>
            </div>
        </WithCommands>
    </Layout>
</template>
