<script setup lang="ts">
    import { CommandData, DashboardData, FilterData } from "@/types";
    import Widget from "@/dashboard/components/Widget.vue";
    import SharpFilter from '@/filters/components/Filter.vue';
    import WithCommands from "@/commands/components/WithCommands.vue";
    import { __ } from "@/utils/i18n";
    import { route } from "@/utils/url";
    import PageAlert from "@/components/PageAlert.vue";
    import { Button } from "@/components/ui/button";
    import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
    import CommandDropdownItems from "@/commands/components/CommandDropdownItems.vue";
    import DropdownChevronDown from "@/components/ui/DropdownChevronDown.vue";
    import {
        Dialog,
        DialogClose,
        DialogFooter,
        DialogHeader, DialogScrollContent,
        DialogTitle,
        DialogTrigger
    } from "@/components/ui/dialog";
    import { Filter } from "lucide-vue-next";
    import { Badge } from "@/components/ui/badge";
    import { CommandManager } from "@/commands/CommandManager";
    import { FilterManager } from "@/filters/FilterManager";
    import { Dashboard } from "@/dashboard/Dashboard";
    import RootCard from "@/components/ui/RootCard.vue";
    import RootCardHeader from "@/components/ui/RootCardHeader.vue";
    import { CardContent } from "@/components/ui/card";

    const props = defineProps<{
        dashboardKey: string,
        dashboard: Dashboard | null,
        filters: FilterManager,
        commands: CommandManager,
        inline?: boolean,
        collapsed?: boolean,
        title: string,
    }>();

    const emit = defineEmits<{
        (e: 'filter-change', filter: FilterData, value: FilterData['value']),
        (e: 'filters-reset', filters: FilterData[]),
    }>();

    function onFilterChange(filter: FilterData, value: FilterData['value']) {
        emit('filter-change', filter, value);
    }

    function onFiltersReset(resettedFilters: FilterData[]) {
        emit('filters-reset', resettedFilters);
    }

    function onCommand(command: CommandData) {
        props.commands.send(command, {
            postCommand: route('code16.sharp.api.dashboard.command', { dashboardKey: props.dashboardKey, commandKey: command.key }),
            getForm: route('code16.sharp.api.dashboard.command.form', { dashboardKey: props.dashboardKey, commandKey: command.key }),
            query: {
                ...props.dashboard.query,
            },
            entityKey: props.dashboardKey,
        });
    }

    defineOptions({ inheritAttrs: false });
</script>

<template>
    <WithCommands :commands="commands">
        <template v-if="dashboard && dashboard.pageAlert && !dashboard.pageAlert.sectionKey">
            <PageAlert
                class="mb-8"
                :page-alert="dashboard.pageAlert"
            />
        </template>

        <component :is="inline ? RootCard : 'div'">
            <component :is="inline ? RootCardHeader : 'div'" :collapsed="collapsed || !dashboard">
                <template v-if="inline || dashboard?.visibleFilters?.length > 0 || dashboard?.visibleCommands?.dashboard?.flat().length">
                    <div class="flex gap-x-3" :class="inline ? 'flex-wrap gap-y-7' : 'mb-8'">
                        <slot name="title" />
                        <template v-if="dashboard">
                            <div class="contents" v-show="!collapsed">
                                <template v-if="dashboard.visibleFilters?.length > 0">
                                    <div :class="inline ? 'w-full order-1' : ''">
                                        <div class="hidden @2xl:flex flex-wrap gap-2">
                                            <template v-for="filter in dashboard.visibleFilters" :key="filter.key">
                                                <SharpFilter
                                                    :filter="filter"
                                                    :value="filters.currentValues[filter.key]"
                                                    :valuated="filters.isValuated([filter])"
                                                    :entity-key="dashboardKey"
                                                    inline
                                                    @input="onFilterChange(filter, $event)"
                                                />
                                            </template>
                                            <template v-if="filters.isValuated(dashboard.visibleFilters)">
                                                <Button class="h-8 underline underline-offset-4 -ml-2" variant="ghost" size="sm" @click="onFiltersReset(dashboard.visibleFilters)">
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
                                                            {{ __('sharp::filters.popover_button') }} : {{ title }}
                                                        </DialogTitle>
                                                    </DialogHeader>
                                                    <div class="flex flex-col flex-wrap gap-4">
                                                        <template v-for="filter in dashboard.visibleFilters" :key="filter.key">
                                                            <SharpFilter
                                                                :filter="filter"
                                                                :value="filters.currentValues[filter.key]"
                                                                :valuated="filters.isValuated([filter])"
                                                                :entity-key="dashboardKey"
                                                                @input="onFilterChange(filter, $event)"
                                                            />
                                                        </template>
                                                    </div>
                                                    <DialogFooter class="flex-row gap-2 mt-2">
                                                        <DialogClose as-child>
                                                            <Button class="flex-1" variant="secondary" :disabled="!filters.isValuated(dashboard.visibleFilters)"
                                                                @click="onFiltersReset(dashboard.visibleFilters)">
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
                                            <template v-if="filters.isValuated(dashboard.visibleFilters)">
                                                <Badge class="ml-2">{{ filters.valuatedCount(dashboard.visibleFilters) }}</Badge>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                <template v-if="dashboard.visibleCommands?.dashboard?.flat().length">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button class="ml-auto h-8" :class="inline ? '-my-1' : ''" variant="outline" size="sm">
                                                {{ __('sharp::dashboard.commands.dashboard.label') }}
                                                <DropdownChevronDown />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent>
                                            <CommandDropdownItems
                                                :commands="dashboard.visibleCommands.dashboard"
                                                @select="onCommand"
                                            />
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </component>

            <template v-if="dashboard">
                <component :is="inline ? CardContent : 'div'" v-show="!collapsed">
                    <div class="grid grid-cols-1 gap-x-4 gap-y-10 md:gap-x-8">
                        <template v-for="(section, i) in dashboard.layout.sections">
                            <section>
                                <template v-if="i || !inline">
                                    <template v-if="section.title">
                                        <h2 class="mb-4 text-2xl font-semibold leading-none">
                                            {{ section.title }}
                                        </h2>
                                    </template>
                                    <template v-else-if="!i">
                                        <h2 class="mb-4 text-2xl font-semibold leading-none">
                                            {{ title }}
                                        </h2>
                                    </template>
                                </template>
                                <template v-if="dashboard.pageAlert?.sectionKey && dashboard.pageAlert.sectionKey === section.key">
                                    <PageAlert
                                        class="mb-4"
                                        :page-alert="dashboard.pageAlert"
                                    />
                                </template>
                                <template v-if="dashboard.sectionVisibleFilters(section)?.length || dashboard.visibleCommands?.[section.key]?.flat().length">
                                    <div class="flex gap-2 mb-4">
                                        <template v-if="dashboard.sectionVisibleFilters(section)?.length">
                                            <div class="hidden @2xl:flex flex-wrap gap-2">
                                                <template v-for="filter in dashboard.sectionVisibleFilters(section)" :key="filter.key">
                                                    <SharpFilter
                                                        :filter="filter"
                                                        :value="filters.currentValues[filter.key]"
                                                        :valuated="filters.isValuated([filter])"
                                                        :entity-key="dashboardKey"
                                                        inline
                                                        @input="onFilterChange(filter, $event)"
                                                    />
                                                </template>
                                                <template v-if="filters.isValuated(dashboard.sectionVisibleFilters(section))">
                                                    <Button class="h-8 underline underline-offset-4 -ml-2" variant="ghost" size="sm"
                                                        @click="onFiltersReset(dashboard.sectionVisibleFilters(section))"
                                                    >
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
                                                            <template v-for="filter in dashboard.sectionVisibleFilters(section)" :key="filter.key">
                                                                <SharpFilter
                                                                    :filter="filter"
                                                                    :value="filters.currentValues[filter.key]"
                                                                    :valuated="filters.isValuated([filter])"
                                                                    :entity-key="dashboardKey"
                                                                    @input="onFilterChange(filter, $event)"
                                                                />
                                                            </template>
                                                        </div>
                                                        <DialogFooter class="flex-row gap-2 mt-2">
                                                            <DialogClose as-child>
                                                                <Button class="flex-1" variant="secondary" :disabled="!filters.isValuated(dashboard.sectionVisibleFilters(section))"
                                                                    @click="onFiltersReset(dashboard.sectionVisibleFilters(section))">
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
                                                <template v-if="filters.isValuated(dashboard.sectionVisibleFilters(section))">
                                                    <Badge class="ml-2">{{ filters.valuatedCount(dashboard.sectionVisibleFilters(section)) }}</Badge>
                                                </template>
                                            </div>
                                        </template>
                                        <template v-if="dashboard.visibleCommands?.[section.key]?.flat().length">
                                            <DropdownMenu>
                                                <DropdownMenuTrigger as-child>
                                                    <Button class="ml-auto h-8" variant="outline" size="sm">
                                                        {{ __('sharp::dashboard.commands.dashboard.label') }}
                                                        <DropdownChevronDown />
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent>
                                                    <CommandDropdownItems
                                                        :commands="dashboard.visibleCommands[section.key]"
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
                </component>
            </template>
        </component>
    </WithCommands>
</template>
