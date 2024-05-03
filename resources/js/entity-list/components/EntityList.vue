<script setup lang="ts">
    import { __, trans_choice } from "@/utils/i18n";
    import { FilterManager } from "@/filters/FilterManager";
    import { EntityList } from "../EntityList";
    import {
        CommandData,
        EntityListQueryParamsData,
        EntityStateValueData,
        FilterData
    } from "@/types";
    import WithCommands from "@/commands/components/WithCommands.vue";
    import { CommandManager } from "@/commands/CommandManager";
    import { Ref } from "vue";
    import { computed, ref, watch } from "vue";
    import { showAlert, showDeleteConfirm } from "@/utils/dialogs";
    import { Instance, InstanceId } from "../types";
    import { getAppendableParentUri, route } from "@/utils/url";
    import { Button } from '@/components/ui/button';
    import { StateIcon } from '@/components/ui';
    import EntityActions from "./EntityActions.vue";
    import { api } from "@/api/api";
    import EntityListPagination from "@/entity-list/components/EntityListPagination.vue";
    import Content from "@/components/Content.vue";
    import SharpFilter from "@/filters/components/Filter.vue";
    import PageAlert from "@/components/PageAlert.vue";
    import { useDraggable } from "vue-draggable-plus";
    import { Card, CardContent, CardDescription, CardFooter, CardHeader } from "@/components/ui/card";
    import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
    import { MoreHorizontal, PlusCircle, ListFilter, ChevronsUpDown, ArrowUp, ArrowDown, GripVertical } from "lucide-vue-next";
    import { Checkbox } from "@/components/ui/checkbox";
    import {
        DropdownMenu, DropdownMenuCheckboxItem,
        DropdownMenuContent,
        DropdownMenuGroup,
        DropdownMenuItem, DropdownMenuRadioGroup, DropdownMenuRadioItem,
        DropdownMenuSeparator,
        DropdownMenuSub, DropdownMenuSubContent,
        DropdownMenuSubTrigger,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import { DialogOverlay, DropdownMenuPortal } from "radix-vue";
    import CommandDropdownItems from "@/commands/components/CommandDropdownItems.vue";
    import { Badge } from "@/components/ui/badge";
    import { Link } from "@inertiajs/vue3";
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
    import EntityListSearch from "@/entity-list/components/EntityListSearch.vue";
    import StickyTop from "@/components/StickyTop.vue";
    import { cn } from "@/utils/cn";

    const props = withDefaults(defineProps<{
        entityKey: string,
        entityList: EntityList | null,
        filters: FilterManager,
        commands: CommandManager,
        inline?: boolean,
        title?: string,
        showCreateButton?: boolean,
        showReorderButton?: boolean,
        showSearchField?: boolean,
        showEntityState?: boolean,
        collapsed?: boolean,
    }>(), {
        showCreateButton: true,
        showReorderButton: true,
        showSearchField: true,
        showEntityState: true,
    });

    const emit = defineEmits(['update:query', 'filter-change', 'reset', 'reordering', 'needs-topbar']);
    const selectedItems: Ref<{ [key: InstanceId]: boolean } | null> = ref(null);
    const selecting = computed(() => !!selectedItems.value);

    function onFilterChange(filter: FilterData, value: FilterData['value']) {
        emit('filter-change', filter, value);
    }

    const searchExpanded = ref(false);
    function onSearchSubmit(search: string) {
        emit('update:query', {
            ...props.entityList.query,
            search,
        });
    }

    function onResetAll() {
        emit('reset');
    }

    function onPageChange(page: number) {
        emit('update:query', {
            ...props.entityList.query,
            page,
        });
    }

    function onSortClick(fieldKey: string, dir: EntityListQueryParamsData['dir']) {
        emit('update:query', {
            ...props.entityList.query,
            page: 1,
            sort: dir ? fieldKey : null,
            dir: dir || null,
        });
    }

    function onInstanceStateChange(value: EntityStateValueData['value'], instanceId: InstanceId) {
        const { commands, entityKey } = props;

        api.post(route('code16.sharp.api.list.state', { entityKey, instanceId }), { value })
            .then(response => {
                commands.handleCommandResponse(response.data);
            })
            .catch(error => {
                const data = error.response?.data;
                if(error.response?.status === 422) {
                    showAlert(data.message, {
                        title: __('modals.state.422.title'),
                        isError: true,
                    });
                }
            });
    }

    const confirmingItem: Ref<InstanceId | null> = ref();

    async function onInstanceCommand(command: CommandData, instanceId: InstanceId) {
        const { commands, entityKey } = props;

        if(command.confirmation) {
            confirmingItem.value = instanceId;
        }

        await commands.send(command, {
            postCommand: route('code16.sharp.api.list.command.instance', { entityKey, instanceId, commandKey: command.key }),
            getForm: route('code16.sharp.api.list.command.instance.form', { entityKey, instanceId, commandKey: command.key }),
            query: props.entityList.query,
            entityKey,
            instanceId,
        });

        confirmingItem.value = null;
    }

    async function onEntityCommand(command: CommandData) {
        const { commands, entityKey } = props;

        await commands.send(command, {
            postCommand: route('code16.sharp.api.list.command.entity', { entityKey, commandKey: command.key }),
            getForm: route('code16.sharp.api.list.command.entity.form', { entityKey, commandKey: command.key }),
            query: {
                ...props.entityList.query,
                ids: Object.entries(selectedItems.value ?? {})
                    .filter(([instanceId, selected]) => selected)
                    .map(([instanceId, selected]) => instanceId),
            },
            entityKey,
        });

        selectedItems.value = null;
    }

    async function onDelete(instanceId: InstanceId) {
        const { entityKey, entityList, commands } = props;

        confirmingItem.value = instanceId;
        try {
            if(await showDeleteConfirm(entityList.config.deleteConfirmationText)) {
                await api.delete(route('code16.sharp.api.list.delete', { entityKey, instanceId }));
                commands.handleCommandResponse({ action: 'reload' });
            }
        } finally {
            confirmingItem.value = null;
        }
    }

    const reorderedItems: Ref<Instance[] | null> = ref(null);
    const reordering = computed(() => !!reorderedItems.value);
    const sortableTableBody = ref<InstanceType<typeof TableBody>>();
    const sortable = useDraggable<Instance>(
        sortableTableBody as any,
        computed<Instance[]>({
            get: () => reorderedItems.value ?? [],
            set: (newItems) => reorderedItems.value = newItems
        }),
        {
            immediate: false,
        }
    );

    watch(reordering, reordering => {
        if(reordering) {
            sortable.start();
        } else {
            sortable.pause();
        }
        emit('reordering', reordering);
    });

    async function onReorderSubmit() {
        const { entityKey, commands } = props;

        await api.post(route('code16.sharp.api.list.reorder', { entityKey }), {
            instances: reorderedItems.value.map(item => props.entityList.instanceId(item)),
        });
        await commands.handleCommandResponse({ action: 'reload' });
        reorderedItems.value = null;
    }

    const stuck = ref(false);
    const needsTopBar = computed(() => reordering.value || selecting.value);
    watch([stuck, needsTopBar], () => {
        if(stuck.value) {
            emit('needs-topbar', needsTopBar.value);
        } else {
            emit('needs-topbar', false);
        }
        if(props.inline) {
            document.dispatchEvent(new CustomEvent('breadcrumb:updateAppendItem', { detail: stuck.value ? { label: props.title } : null }));
        }
    });
</script>

<template>
    <WithCommands :commands="commands">
        <div>
            <template v-if="entityList?.pageAlert">
                <PageAlert
                    class="mb-3"
                    :page-alert="entityList.pageAlert"
                />
            </template>

            <transition enter-active-class="transition" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <template v-if="reordering">
                    <div class="fixed inset-0 z-10 bg-black/5"></div>
                </template>
            </transition>

            <template v-if="entityList">
                <template v-if="showSearchField && entityList.config.searchable || entityList.visibleFilters?.length">
                    <StickyTop class="group flex gap-3 top-3.5 mb-4 transition-[padding,opacity] xl:sticky xl:pl-[--sticky-safe-left-offset] xl:pr-[--sticky-safe-right-offset]"
                        :class="cn('z-30 ', {
                            '-top-8 z-0 px-0': inline && !needsTopBar,
                            // 'opacity-0': inline && stuck && !needsTopBar,
                        }, {
                            'z-[60]': reordering,
                        })"
                        v-model:stuck="stuck"
                        v-slot="{ largerThanTopbar }"
                    >
                        <div class="flex flex-row lg:flex-col flex-wrap gap-3 xl:flex-row" v-show="!reordering && !selecting">
                            <template v-if="showSearchField && entityList.config.searchable">
                                <EntityListSearch
                                    v-model:expanded="searchExpanded"
                                    :entity-list="entityList"
                                    @submit="onSearchSubmit"
                                />
                            </template>
                            <template v-if="entityList.visibleFilters?.length">
                                <div class="flex items-center lg:hidden">
                                    <Popover>
                                        <PopoverTrigger as-child>
                                            <Button class="h-8 gap-1" variant="outline" size="sm">
                                                <ListFilter class="h-3.5 w-3.5" />
                                                <span class="sr-only sm:not-sr-only sm:whitespace-nowrap">
                                                    Filter
                                                </span>
                                            </Button>
                                        </PopoverTrigger>
                                        <PopoverContent class="min-w-min lg:hidden" align="start" :align-offset="-16">
                                            <div class="flex flex-col flex-wrap gap-4">
                                                <template v-for="filter in entityList.visibleFilters" :key="filter.key">
                                                    <SharpFilter
                                                        :filter="filter"
                                                        :value="filters.currentValues[filter.key]"
                                                        :disabled="reordering"
                                                        :valuated="filters.isValuated([filter])"
                                                        @input="onFilterChange(filter, $event)"
                                                    />
                                                </template>
                                            </div>
                                            <template v-if="filters.isValuated(entityList.visibleFilters) || entityList.query?.search">
                                                <Button class="w-full mt-8 h-8" variant="secondary" @click="onResetAll">
                                                    {{ __('sharp::filters.reset_all') }}
                                                </Button>
                                            </template>
                                        </PopoverContent>
                                    </Popover>
                                    <template v-if="filters.isValuated(filters.rootFilters)">
                                        <Badge class="ml-2">{{ Object.values(filters.filterValues?.valuated ?? {}).filter(Boolean).length }}</Badge>
                                    </template>
                                </div>

                                <div class="relative flex-1 hidden lg:block">
                                    <template v-if="stuck && largerThanTopbar">
                                        <Button class="absolute top-0 left-0 h-8" variant="outline" size="sm" @click="window.scrollTo({ top:0, behavior: 'smooth'})">
                                            See filters
                                        </Button>
                                    </template>
                                    <div class="hidden md:flex flex-wrap gap-3"
                                        :class="{
                                            'opacity-0 pointer-events-none': searchExpanded,
                                            'invisible': stuck && largerThanTopbar,
                                        }"
                                    >
                                        <template v-for="filter in entityList.visibleFilters" :key="filter.key">
                                            <SharpFilter
                                                :filter="filter"
                                                :value="filters.currentValues[filter.key]"
                                                :disabled="reordering"
                                                :valuated="filters.isValuated([filter])"
                                                inline
                                                @input="onFilterChange(filter, $event)"
                                            />
                                        </template>
                                        <template v-if="filters.isValuated(entityList.visibleFilters) || entityList.query?.search">
                                            <Button class="h-8" variant="ghost" size="sm" @click="onResetAll">
                                                {{ __('sharp::filters.reset_all') }}
                                            </Button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="ml-auto self-start flex flex-wrap gap-2 lg:flex-nowrap">
                            <template v-if="showReorderButton && entityList.canReorder && !selecting">
                                <template v-if="reordering">
                                    <Button class="h-8" size="sm" variant="outline" @click="reorderedItems = null">
                                        {{ __('sharp::action_bar.list.reorder_button.cancel') }}
                                    </Button>
                                    <Button class="h-8" size="sm" @click="onReorderSubmit">
                                        {{ __('sharp::action_bar.list.reorder_button.finish') }}
                                    </Button>
                                </template>
                                <template v-else>
                                    <div class="col-auto">
                                        <Button class="h-8" size="sm" variant="outline" @click="reorderedItems = [...entityList.data]">
                                            {{ __('sharp::action_bar.list.reorder_button') }}
                                        </Button>
                                    </div>
                                </template>
                            </template>

                            <template v-if="entityList.canSelect && !reordering">
                                <template v-if="selecting">
                                    <Button class="h-8" size="sm" variant="outline" @click="selectedItems = null">
                                        {{ __('sharp::action_bar.list.reorder_button.cancel') }}
                                    </Button>
                                </template>
                                <template v-else>
                                    <Button class="h-8" size="sm" variant="outline" @click="selectedItems = Object.fromEntries(entityList.data.map(item => [item.id, false]))">
                                        {{ __('sharp::action_bar.list.select_button') }}
                                    </Button>
                                </template>
                            </template>

                            <template v-if="entityList.dropdownEntityCommands(selecting)?.flat().length && !reordering">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button class="h-8" :variant="selecting ? 'default' : 'outline'" size="sm" :disabled="reordering">
                                            {{ __('sharp::entity_list.commands.entity.label') }}
                                            <template v-if="selecting">
                                                ({{ Object.values(selectedItems).filter(Boolean).length }} selected)
                                            </template>
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent>
                                        <CommandDropdownItems
                                            :commands="entityList.dropdownEntityCommands(selecting)"
                                            :selecting="selecting"
                                            @select="onEntityCommand"
                                        />
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </template>

                            <template v-if="entityList.primaryCommand && !reordering && !selecting">
                                <Button class="h-8" size="sm" @click="onEntityCommand(entityList.primaryCommand)">
                                    {{ entityList.primaryCommand.label }}
                                </Button>
                            </template>

                            <template v-if="showCreateButton && entityList.authorizations.create && !reordering && !selecting">
                                <template v-if="entityList.forms">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button class="h-8 gap-1" size="sm">
                                                <PlusCircle class="h-3.5 w-3.5" />
                                                {{ __('sharp::action_bar.list.forms_dropdown') }}
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent>
                                            <template v-for="form in Object.values(entityList.forms).filter(form => !!form.label)">
                                                <DropdownMenuItem as-child>
                                                    <Link :href="route('code16.sharp.form.create', { parentUri: getAppendableParentUri(), entityKey: `${entityKey}:${form.key}` })">
                                                        {{ form.label }}
                                                    </Link>
                                                </DropdownMenuItem>
                                            </template>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </template>
                                <template v-else>
                                    <Button
                                        class="h-8 gap-1"
                                        size="sm"
                                        :disabled="reordering || selecting"
                                        :href="route('code16.sharp.form.create', { parentUri: getAppendableParentUri(), entityKey })"
                                    >
                                        <PlusCircle class="h-3.5 w-3.5" />
                                        {{ __('sharp::action_bar.list.create_button') }}
                                    </Button>
                                </template>
                            </template>
                        </div>
                    </StickyTop>
                </template>
            </template>
            <template v-else>
                <div class="h-8 mb-4"></div>
            </template>

            <Card :class="reordering ? 'relative z-[11]' : ''">
                <CardHeader>
                    <div class="flex items-baseline gap-4 lg:gap-6">
                        <slot name="card-header" />
                        <template v-if="entityList">
                            <CardDescription class="text-xs">
                                <template v-if="entityList.query?.search">
                                    {{ trans_choice('sharp::action_bar.list.search.title', entityList.count, { count: entityList.count, search: entityList.query.search }) }}
                                </template>
                                <template v-else>
                                    {{ trans_choice('sharp::action_bar.list.items_count', entityList.count, { count: entityList.count }) }}
                                </template>
                            </CardDescription>
                        </template>
                    </div>
                </CardHeader>
                <template v-if="entityList">
                    <CardContent :class="entityList.count > 0 ? 'pb-2' : ''" v-show="!collapsed">
                        <template v-if="entityList.data?.length > 0">
                            <div class="-mx-6">
                                <Table>
                                    <TableHeader>
                                        <TableRow class="hover:bg-transparent first:*:pl-6 last:*:pr-6">
                                            <template v-if="selecting || reordering">
                                                <TableHead>
                                                    <span class="sr-only">Select...</span>
                                                </TableHead>
                                            </template>
                                            <template v-for="(field, fieldIndex) in entityList.fields">
                                                <TableHead
                                                    class="w-[var(--width,auto)]"
                                                    :style="{ '--width': field.width === 'fill' ? (100 / entityList.fields.length)+'%' : field.width }"
                                                >
                                                    <template v-if="field.sortable">
                                                        <DropdownMenu>
                                                            <DropdownMenuTrigger as-child>
                                                                <Button
                                                                    variant="ghost"
                                                                    size="sm"
                                                                    class="-ml-3 h-8 data-[state=open]:bg-accent"
                                                                >
                                                                    <span>{{ field.label }}</span>
                                                                    <template v-if="entityList.currentSort === field.key">
                                                                        <ArrowDown v-if="entityList.currentSortDir === 'desc'" class="ml-2 h-3.5 w-3.5" />
                                                                        <ArrowUp v-else-if=" entityList.currentSortDir === 'asc'" class="ml-2 h-3.5 w-3.5" />
                                                                    </template>
                                                                    <template v-else>
                                                                        <ChevronsUpDown class="ml-2 h-3.5 w-3.5" />
                                                                    </template>
                                                                </Button>
                                                            </DropdownMenuTrigger>
                                                            <DropdownMenuContent align="start" :align-offset="-48">
                                                                <DropdownMenuRadioGroup
                                                                    :model-value="entityList.currentSort === field.key ? entityList.currentSortDir : ''"
                                                                    @update:model-value="dir => onSortClick(field.key, dir as any)"
                                                                >
                                                                    <DropdownMenuRadioItem value="asc">
                                                                        <ArrowUp class="mr-2 h-3.5 w-3.5 text-muted-foreground/70" />
                                                                        {{ __('sharp::entity_list.sort_asc') }}
                                                                    </DropdownMenuRadioItem>
                                                                    <DropdownMenuRadioItem value="desc">
                                                                        <ArrowDown class="mr-2 h-3.5 w-3.5 text-muted-foreground/70" />
                                                                        {{ __('sharp::entity_list.sort_desc') }}
                                                                    </DropdownMenuRadioItem>
                                                                    <template v-if="entityList.currentSort === field.key
                                                                        && !(entityList.config.defaultSort === field.key && entityList.config.defaultSortDir === entityList.currentSortDir)">
                                                                        <DropdownMenuSeparator />
                                                                        <DropdownMenuRadioItem value="">
                                                                            <div class="mr-2 h-3.5 w-3.5"></div>
                                                                            {{ __('sharp::filters.select.reset') }}
                                                                        </DropdownMenuRadioItem>
                                                                    </template>
                                                                </DropdownMenuRadioGroup>
                                                            </DropdownMenuContent>
                                                        </DropdownMenu>
                                                    </template>
                                                    <template v-else>
                                                        {{ field.label }}
                                                    </template>
                                                </TableHead>
                                            </template>
                                            <template v-if="!reordering && !selecting && entityList.data.some(item => entityList.instanceHasActions(item, showEntityState))">
                                                <TableHead class="w-2">
                                                    <span class="sr-only">Edit</span>
                                                </TableHead>
                                            </template>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody ref="sortableTableBody">
                                        <template v-for="(item, itemIndex) in reorderedItems ?? entityList.data">
                                            <TableRow class="relative first:*:pl-6 last:*:pr-6 [&:has([aria-expanded=true])]:bg-muted/50"
                                                :class="[confirmingItem && confirmingItem === entityList.instanceId(item) ? 'bg-white/50 z-[60]' : '', reordering ? 'cursor-move' : '']"
                                            >
                                                <template v-if="selecting && selectedItems">
                                                    <TableCell>
                                                        <Checkbox
                                                            :id="`check-${entityKey}-${entityList.instanceId(item)}`"
                                                            :checked="selectedItems[entityList.instanceId(item)]"
                                                            @update:checked="(checked) => selectedItems[entityList.instanceId(item)] = checked"
                                                        />
                                                        <label class="absolute inset-0 z-20" :for="`check-${entityKey}-${entityList.instanceId(item)}`">
                                                            <span class="sr-only">Select</span>
                                                        </label>
                                                    </TableCell>
                                                </template>
                                                <template v-if="reordering">
                                                    <TableCell>
                                                        <GripVertical class="w-4 h-4 opacity-50" />
                                                    </TableCell>
                                                </template>
                                                <template v-for="(field, fieldIndex) in entityList.fields">
                                                    <template v-if="field.key === '@state' && entityList.config.state && showEntityState">
                                                        <TableCell>
                                                            <DropdownMenu>
                                                                <DropdownMenuTrigger as-child>
                                                                    <Button class="relative -mx-3" variant="ghost" size="sm">
                                                                        <Badge variant="outline">
                                                                            <StateIcon class="-ml-0.5 mr-1.5" :state-value="entityList.instanceStateValue(item)" />
                                                                            {{ entityList.instanceStateValue(item)?.label }}
                                                                        </Badge>
                                                                    </Button>
                                                                </DropdownMenuTrigger>
                                                                <DropdownMenuContent align="start" :align-offset="-16">
                                                                    <template v-for="stateValue in entityList.config.state.values" :key="stateValue.value">
                                                                        <DropdownMenuCheckboxItem
                                                                            :checked="stateValue.value == entityList.instanceState(item)"
                                                                            @update:checked="(checked) => checked && onInstanceStateChange(stateValue.value, entityList.instanceId(item))"
                                                                        >
                                                                            <StateIcon class="mr-1.5" :state-value="stateValue" />
                                                                            <span class="truncate">{{ stateValue.label }}</span>
                                                                        </DropdownMenuCheckboxItem>
                                                                    </template>
                                                                </DropdownMenuContent>
                                                            </DropdownMenu>
                                                        </TableCell>
                                                    </template>
                                                    <template v-else>
                                                        <TableCell>
                                                            <template v-if="fieldIndex === 0 && entityList.instanceUrl(item) && !selecting && !reordering">
                                                                <Link class="absolute inset-0" :href="entityList.instanceUrl(item)"></Link>
                                                            </template>
                                                            <template v-if="field.html && typeof item[field.key] === 'string'">
                                                                <Content class="[&_a]:relative [&_a]:z-10"
                                                                    :class="{ '[&_a]:pointer-events-none': selecting || reordering }"
                                                                    :html="item[field.key]"
                                                                />
                                                            </template>
                                                            <template v-else>
                                                                {{ item[field.key] }}
                                                            </template>
                                                        </TableCell>
                                                    </template>
                                                </template>

                                                <template v-if="!reordering && !selecting && entityList.instanceHasActions(item, showEntityState)">
                                                    <TableCell class="relative">
                                                        <DropdownMenu>
                                                            <DropdownMenuTrigger as-child>
                                                                <Button class="pointer-events-auto" variant="ghost" size="icon">
                                                                    <MoreHorizontal class="h-4" />
                                                                </Button>
                                                            </DropdownMenuTrigger>
                                                            <DropdownMenuContent side="bottom" align="center">
                                                                <template v-if="entityList.config.state && showEntityState && entityList.instanceCanUpdateState(item)">
                                                                    <DropdownMenuGroup>
                                                                        <DropdownMenuSub>
                                                                            <DropdownMenuSubTrigger>
                                                                                {{ __('sharp::modals.entity_state.edit.title') }}
                                                                            </DropdownMenuSubTrigger>
                                                                            <DropdownMenuPortal>
                                                                                <DropdownMenuSubContent>
                                                                                    <template v-for="stateValue in entityList.config.state.values" :key="stateValue.value">
                                                                                        <DropdownMenuCheckboxItem
                                                                                            :checked="stateValue.value == entityList.instanceState(item)"
                                                                                            @update:checked="(checked) => checked && onInstanceStateChange(stateValue.value, entityList.instanceId(item))"
                                                                                        >
                                                                                            <StateIcon class="mr-1.5" :state-value="stateValue" />
                                                                                            <span class="truncate">{{ stateValue.label }}</span>
                                                                                        </DropdownMenuCheckboxItem>
                                                                                    </template>
                                                                                </DropdownMenuSubContent>
                                                                            </DropdownMenuPortal>
                                                                        </DropdownMenuSub>
                                                                    </DropdownMenuGroup>
                                                                    <DropdownMenuSeparator />
                                                                </template>

                                                                <CommandDropdownItems
                                                                    :commands="entityList.instanceCommands(item)"
                                                                    @select="(command) => onInstanceCommand(command, entityList.instanceId(item))"
                                                                />
                                                                <template v-if="!entityList.config.deleteHidden && entityList.instanceCanDelete(item)">
                                                                    <template v-if="entityList.instanceCommands(item)?.flat().length">
                                                                        <DropdownMenuSeparator />
                                                                    </template>
                                                                    <DropdownMenuItem class="text-destructive" @click="onDelete(entityList.instanceId(item))">
                                                                        {{ __('sharp::action_bar.form.delete_button') }}
                                                                    </DropdownMenuItem>
                                                                </template>
                                                            </DropdownMenuContent>
                                                        </DropdownMenu>
                                                    </TableCell>
                                                </template>
                                            </TableRow>
                                        </template>
                                    </TableBody>
                                </Table>
                            </div>
                        </template>
                        <template v-else>
                            {{ __('sharp::entity_list.empty_text') }}
                        </template>
                    </CardContent>
                    <template v-if="entityList.meta?.prev_page_url || entityList.meta?.next_page_url">
                        <CardFooter class="pt-4" v-show="!collapsed">
                            <EntityListPagination
                                :entity-list="entityList"
                                :links-openable="!inline"
                                @change="onPageChange"
                            />
                        </CardFooter>
                    </template>
                </template>
            </Card>
        </div>
    </WithCommands>
</template>
