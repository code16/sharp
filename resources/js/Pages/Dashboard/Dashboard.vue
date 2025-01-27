<script setup lang="ts">
    import { BreadcrumbData, CommandData, DashboardData, FilterData } from "@/types";
    import { useFilters } from "@/filters/useFilters";
    import { parseQuery } from "@/utils/querystring";
    import { router } from "@inertiajs/vue3";
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
    import { watch } from "vue";
    import DropdownChevronDown from "@/components/ui/DropdownChevronDown.vue";
    import useMenu from "@/composables/useMenu";
    import PageBreadcrumb from "@/components/PageBreadcrumb.vue";
    import { config } from "@/utils/config";
    import EntityListSearch from "@/entity-list/components/EntityListSearch.vue";
    import {
        Dialog,
        DialogClose,
        DialogFooter,
        DialogHeader, DialogScrollContent,
        DialogTitle,
        DialogTrigger
    } from "@/components/ui/dialog";
    import { Filter } from "lucide-vue-next";
    import RootCard from "@/components/ui/RootCard.vue";
    import { CardContent, CardHeader, CardTitle } from "@/components/ui/card";
    import { Badge } from "@/components/ui/badge";

    const props = defineProps<{
        dashboard: DashboardData,
        breadcrumb: BreadcrumbData,
    }>();

    const dashboardKey = route().params.dashboardKey as string;
    const filters = useFilters(props.dashboard.config.filters, props.dashboard.filterValues);
    const commands = useCommands('dashboard');

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

        <template #breadcrumb>
            <template v-if="config('sharp.display_breadcrumb')">
                <PageBreadcrumb :breadcrumb="breadcrumb" />
            </template>
        </template>

        <WithCommands :commands="commands">
            <div class="container @container mx-auto">
                <div :class="dashboard.pageAlert ? 'pt-4' : 'pt-10'">
                    <template v-if="dashboard.pageAlert">
                        <PageAlert
                            class="mb-8"
                            :page-alert="dashboard.pageAlert"
                        />
                    </template>

                    <template v-if="filters.rootFilters.length > 0 || dashboard.config.commands?.dashboard?.length">
                        <div class="mb-8 flex gap-3">
                            <template v-if="filters.rootFilters.length > 0">
                                <div class="hidden @2xl:flex flex-wrap gap-3">
                                    <template v-for="filter in filters.rootFilters" :key="filter.key">
                                        <SharpFilter
                                            :filter="filter"
                                            :value="filters.currentValues[filter.key]"
                                            :valuated="filters.isValuated([filter])"
                                            inline
                                            @input="onFilterChange(filter, $event)"
                                        />
                                    </template>
                                    <template v-if="filters.isValuated(filters.rootFilters)">
                                        <Button class="h-8 underline underline-offset-4 -ml-2" variant="ghost" size="sm" @click="onFiltersReset(filters.rootFilters)">
                                            {{ __('sharp::filters.reset_all') }}
                                        </Button>
                                    </template>
                                </div>
                                <div class="flex items-center @2xl:hidden">
                                    <Dialog>
                                        <DialogTrigger as-child>
                                            <Button class="h-8 gap-1" variant="outline" size="sm">
                                                <Filter class="h-3.5 w-3.5" />
                                                <span>
                                                    {{ __('sharp::filters.popover_button') }}
                                                </span>
                                            </Button>
                                        </DialogTrigger>
                                        <DialogScrollContent @open-auto-focus.prevent>
                                            <DialogHeader>
                                                <DialogTitle>
                                                    {{ __('sharp::filters.popover_button') }} : {{ breadcrumb.items[0].label }}
                                                </DialogTitle>
                                            </DialogHeader>
                                            <div class="flex flex-col flex-wrap gap-4">
                                                <template v-for="filter in filters.rootFilters" :key="filter.key">
                                                    <SharpFilter
                                                        :filter="filter"
                                                        :value="filters.currentValues[filter.key]"
                                                        :valuated="filters.isValuated([filter])"
                                                        @input="onFilterChange(filter, $event)"
                                                    />
                                                </template>
                                            </div>
                                            <DialogFooter class="flex-row gap-2 mt-2">
                                                <DialogClose as-child>
                                                    <Button class="flex-1" variant="secondary" :disabled="!filters.isValuated(filters.rootFilters)"
                                                        @click="onFiltersReset(filters.rootFilters)">
                                                        {{ __('sharp::filters.reset_all') }}
                                                    </Button>
                                                </DialogClose>
                                                <DialogClose as-child>
                                                    <Button class="flex-1">
                                                        {{ __('sharp::filters.dialog.submit') }}
                                                    </Button>
                                                </DialogClose>
                                            </DialogFooter>
                                        </DialogScrollContent>
                                    </Dialog>
                                    <template v-if="filters.isValuated(filters.rootFilters)">
                                        <Badge class="ml-2">{{ filters.valuatedCount(filters.rootFilters) }}</Badge>
                                    </template>
                                </div>
                            </template>
                            <template v-if="dashboard.config.commands?.dashboard?.length">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button class="ml-auto h-8" variant="outline" size="sm">
                                            {{ __('sharp::dashboard.commands.dashboard.label') }}
                                            <DropdownChevronDown />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent>
                                        <CommandDropdownItems
                                            :commands="dashboard.config.commands.dashboard"
                                            @select="onCommand"
                                        />
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </template>
                        </div>
                    </template>

                    <div class="grid grid-cols-1 gap-x-4 gap-y-10 md:gap-x-8">
                        <template v-for="(section, i) in dashboard.layout.sections">
                            <section>
                                <template v-if="section.title">
                                    <h2 class="mb-4 text-2xl font-semibold">
                                        {{ section.title }}
                                    </h2>
                                </template>
                                <template v-else-if="!i">
                                    <h2 class="mb-4 text-2xl font-semibold">
                                        {{ breadcrumb.items[0].label }}
                                    </h2>
                                </template>
                                <template v-if="dashboard.config.filters?.[section.key]?.length || dashboard.config.commands?.[section.key]?.flat().length">
                                    <div class="flex  gap-3 mb-4">
                                        <template v-if="dashboard.config.filters?.[section.key]?.length">
                                            <div class="hidden @2xl:flex flex-wrap gap-3">
                                                <template v-for="filter in dashboard.config.filters[section.key]" :key="filter.key">
                                                    <SharpFilter
                                                        :filter="filter"
                                                        :value="filters.currentValues[filter.key]"
                                                        :valuated="filters.isValuated([filter])"
                                                        inline
                                                        @input="onFilterChange(filter, $event)"
                                                    />
                                                </template>
                                                <template v-if="filters.isValuated(dashboard.config.filters[section.key])">
                                                    <Button class="h-8 underline underline-offset-4 -ml-2" variant="ghost" size="sm" @click="onFiltersReset(dashboard.config.filters[section.key])">
                                                        {{ __('sharp::filters.reset_all') }}
                                                    </Button>
                                                </template>
                                            </div>
                                            <div class="flex items-center @2xl:hidden">
                                                <Dialog>
                                                    <DialogTrigger as-child>
                                                        <Button class="h-8 gap-1" variant="outline" size="sm">
                                                            <Filter class="h-3.5 w-3.5" />
                                                            <span>
                                                                {{ __('sharp::filters.popover_button') }}
                                                            </span>
                                                        </Button>
                                                    </DialogTrigger>
                                                    <DialogScrollContent @open-auto-focus.prevent>
                                                        <DialogHeader>
                                                            <DialogTitle>
                                                                {{ __('sharp::filters.popover_button') }} : {{ section.title }}
                                                            </DialogTitle>
                                                        </DialogHeader>
                                                        <div class="flex flex-col flex-wrap gap-4">
                                                            <template v-for="filter in dashboard.config.filters[section.key]" :key="filter.key">
                                                                <SharpFilter
                                                                    :filter="filter"
                                                                    :value="filters.currentValues[filter.key]"
                                                                    :valuated="filters.isValuated([filter])"
                                                                    @input="onFilterChange(filter, $event)"
                                                                />
                                                            </template>
                                                        </div>
                                                        <DialogFooter class="flex-row gap-2 mt-2">
                                                            <DialogClose as-child>
                                                                <Button class="flex-1" variant="secondary" :disabled="!filters.isValuated(dashboard.config.filters[section.key])"
                                                                    @click="onFiltersReset(dashboard.config.filters[section.key])">
                                                                    {{ __('sharp::filters.reset_all') }}
                                                                </Button>
                                                            </DialogClose>
                                                            <DialogClose as-child>
                                                                <Button class="flex-1">
                                                                    {{ __('sharp::filters.dialog.submit') }}
                                                                </Button>
                                                            </DialogClose>
                                                        </DialogFooter>
                                                    </DialogScrollContent>
                                                </Dialog>
                                                <template v-if="filters.isValuated(dashboard.config.filters[section.key])">
                                                    <Badge class="ml-2">{{ filters.valuatedCount(dashboard.config.filters[section.key]) }}</Badge>
                                                </template>
                                            </div>
                                        </template>
                                        <template v-if="dashboard.config.commands?.[section.key]?.flat().length">
                                            <DropdownMenu>
                                                <DropdownMenuTrigger as-child>
                                                    <Button class="ml-auto h-8" variant="outline" size="sm">
                                                        {{ __('sharp::dashboard.commands.dashboard.label') }}
                                                        <DropdownChevronDown />
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent>
                                                    <CommandDropdownItems
                                                        :commands="dashboard.config.commands[section.key]"
                                                        @select="onCommand"
                                                    />
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </template>
                                    </div>
                                </template>
                                <div class="grid grid-cols-1 @2xl:grid-cols-12 gap-6">
                                    <template v-for="row in section.rows">
                                        <template v-for="widgetLayout in row">
                                            <div class="contents @2xl:*:col-[span_var(--size)]" :style="{ '--size': widgetLayout.size }">
                                                <Widget
                                                    :widget="dashboard.widgets[widgetLayout.key]"
                                                    :value="dashboard.data[widgetLayout.key]"
                                                />
                                            </div>
                                        </template>
                                    </template>
                                </div>
                            </section>
                        </template>
                    </div>
                </div>
            </div>
        </WithCommands>
    </Layout>
</template>
