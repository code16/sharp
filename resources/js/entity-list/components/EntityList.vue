<script setup lang="ts">
    import { __ } from "@/utils/i18n";
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
    import Pagination from "@/components/ui/Pagination.vue";
    import CaptureInternalLinks from "@/components/CaptureInternalLinks.vue";
    import SharpFilter from "@/filters/components/Filter.vue";
    import PageAlert from "@/components/PageAlert.vue";
    import { useDraggable } from "vue-draggable-plus";
    import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
    import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
    import { ChevronDown, MoreHorizontal, PlusCircle, ListFilter } from "lucide-vue-next";
    import { Checkbox } from "@/components/ui/checkbox";
    import {
        DropdownMenu, DropdownMenuCheckboxItem,
        DropdownMenuContent,
        DropdownMenuGroup,
        DropdownMenuItem,
        DropdownMenuSeparator,
        DropdownMenuSub, DropdownMenuSubContent,
        DropdownMenuSubTrigger,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import { DropdownMenuPortal } from "radix-vue";
    import CommandDropdownItems from "@/commands/components/CommandDropdownItems.vue";
    import { Badge } from "@/components/ui/badge";
    import { Link } from "@inertiajs/vue3";
    import { FilterQueryParams } from "@/filters/types";
    import { Input } from "@/components/ui/input";
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
    import FilterContainer from "@/filters/components/FilterContainer.vue";

    const props = withDefaults(defineProps<{
        entityKey: string,
        entityList: EntityList,
        filters: FilterManager,
        commands: CommandManager,
        query: EntityListQueryParamsData & FilterQueryParams,
        inline?: boolean,
        showCreateButton?: boolean,
        showReorderButton?: boolean,
        showSearchField?: boolean,
        showEntityState?: boolean,
        loading?: boolean,
        collapsed?: boolean,
    }>(), {
        showCreateButton: true,
        showReorderButton: true,
        showSearchField: true,
        showEntityState: true,
    });

    const emit = defineEmits(['update:query', 'filter-change', 'reset', 'reordering']);
    const selectedItems: Ref<{ [key: InstanceId]: boolean } | null> = ref(null);
    const selecting = computed(() => !!selectedItems.value);

    function onFilterChange(filter: FilterData, value: FilterData['value']) {
        emit('filter-change', filter, value);
    }

    function onSearchSubmit(search: string) {
        emit('update:query', {
            ...props.query,
            search,
        });
    }

    function onResetAll() {
        emit('reset');
    }

    function onPageChange(page: number) {
        emit('update:query', {
            ...props.query,
            page,
        });
    }

    function onSortClick(fieldKey: string) {
        emit('update:query', {
            ...props.query,
            page: 1,
            sort: fieldKey,
            dir: props.query.sort === fieldKey && props.query.dir === 'asc'
                ? 'desc'
                : 'asc',
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

    function onInstanceCommand(command: CommandData, instanceId: InstanceId) {
        const { commands, entityKey, query } = props;

        commands.send(command, {
            postCommand: route('code16.sharp.api.list.command.instance', { entityKey, instanceId, commandKey: command.key }),
            getForm: route('code16.sharp.api.list.command.instance.form', { entityKey, instanceId, commandKey: command.key }),
            query,
            entityKey,
            instanceId,
        });
    }

    async function onEntityCommand(command: CommandData) {
        const { commands, entityKey, query, filters } = props;

        await commands.send(command, {
            postCommand: route('code16.sharp.api.list.command.entity', { entityKey, commandKey: command.key }),
            getForm: route('code16.sharp.api.list.command.entity.form', { entityKey, commandKey: command.key }),
            query: {
                ...query,
                ids: Object.entries(selectedItems.value ?? {})
                    .filter(([instanceId, selected]) => selected)
                    .map(([instanceId, selected]) => instanceId),
            },
            entityKey,
        });

        selectedItems.value = null;
    }

    const deletingItem: Ref<InstanceId | null> = ref();
    async function onDelete(instanceId: InstanceId) {
        const { entityKey, entityList, commands } = props;

        deletingItem.value = instanceId;
        try {
            if(await showDeleteConfirm(entityList.config.deleteConfirmationText)) {
                await api.delete(route('code16.sharp.api.list.delete', { entityKey, instanceId }));
                commands.handleCommandResponse({ action: 'reload' });
            }
        } finally {
            deletingItem.value = null;
        }
    }

    const reorderedItems: Ref<Instance[] | null> = ref(null);
    const reordering = computed(() => !!reorderedItems.value);
    const sortableTableBody = ref<InstanceType<typeof TableBody>>();
    const sortable = useDraggable<Instance>(
        sortableTableBody as Ref<HTMLElement>,
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
</script>

<template>
    <WithCommands :commands="commands">
        <div class="SharpEntityList">
<!--            <div class="flex">-->
<!--                <div class="flex-1">-->
<!--                    <slot name="breadcrumb" />-->
<!--                </div>-->
<!--            </div>-->

            <div>
                <template v-if="entityList?.pageAlert">
                    <PageAlert
                        class="mb-3"
                        :page-alert="entityList.pageAlert"
                    />
                </template>

                <template v-if="entityList">
                    <template v-if="showSearchField && entityList.config.searchable || entityList.visibleFilters?.length">
                        <div class="flex gap-3 mb-4">
                            <div class="flex flex-wrap gap-3">
                                <template v-if="showSearchField && entityList.config.searchable">
                                    <Input
                                        :placeholder="__('sharp::action_bar.list.search.placeholder')"
                                        :model-value="query.search"
                                        :disabled="reordering"
                                        class="h-8 w-[150px] lg:w-[250px]"
                                    />
                                </template>
                                <template v-if="entityList.visibleFilters?.length">
                                    <div class="flex items-center">
                                        <Popover>
                                            <PopoverTrigger as-child>
                                                <Button class="h-8" variant="outline" size="sm">
                                                    <ListFilter class="h-3.5 w-3.5 mr-1" />
                                                    <span class="sr-only sm:not-sr-only sm:whitespace-nowrap">
                                                        Filter
                                                    </span>
                                                </Button>
                                            </PopoverTrigger>
                                            <PopoverContent class="min-w-min" align="start" :align-offset="-16">
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
                                                <template v-if="filters.isValuated(entityList.visibleFilters) || query.search">
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

<!--                                    <template v-for="filter in entityList.visibleFilters" :key="filter.key">-->
<!--                                        <SharpFilter-->
<!--                                            :filter="filter"-->
<!--                                            :value="filters.currentValues[filter.key]"-->
<!--                                            :disabled="reordering"-->
<!--                                            :valuated="filters.isValuated([filter])"-->
<!--                                            @input="onFilterChange(filter, $event)"-->
<!--                                        />-->
<!--                                    </template>-->
                                </template>
                            </div>
                            <div class="ml-auto self-start flex flex-wrap gap-2">
                                <template v-if="showReorderButton && entityList.canReorder && !selecting">
                                    <template v-if="reordering">
                                        <div class="col-auto">
                                            <Button class="h-8" size="sm" variant="outline" @click="reorderedItems = null">
                                                {{ __('sharp::action_bar.list.reorder_button.cancel') }}
                                            </Button>
                                        </div>
                                        <div class="col-auto">
                                            <Button class="h-8" size="sm" @click="onReorderSubmit">
                                                {{ __('sharp::action_bar.list.reorder_button.finish') }}
                                            </Button>
                                        </div>
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
                                        <DropdownMenuTrigger>
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
                        </div>
                    </template>
                </template>
                <template v-else>
                    <div class="h-8 mb-4"></div>
                </template>

                <Card>
                    <CardHeader>
                        <slot name="card-header" />
                    </CardHeader>
                    <template v-if="entityList">
                        <CardContent v-show="!collapsed">
                            <template v-if="entityList.data?.length > 0">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <template v-if="selecting">
                                                <TableHead>
                                                    <span class="sr-only">Select...</span>
                                                </TableHead>
                                            </template>
                                            <template v-for="(field, fieldIndex) in entityList.fields">
                                                <TableHead
                                                    class="w-[var(--width,auto)]"
                                                    :style="{ '--width': field.width === 'fill' ? (100 / entityList.fields.length)+'%' : field.width }"
                                                >
                                                    <div class="relative flex items-center" :class="{ 'hover:underline': field.sortable }">
                                                        <div class="flex-1">
                                                            {{ field.label }}
                                                        </div>
                                                        <template v-if="field.sortable">
                                                            <button class="shrink-0 ml-2" @click="onSortClick(field.key)">
                                                                <span class="absolute inset-0"></span>
                                                                <ChevronDown
                                                                    class="h-4 w-4 transition-transform duration-200"
                                                                    :class="{
                                                                    'rotate-180': query.dir === 'asc',
                                                                    'invisible': query.sort !== field.key
                                                                }"
                                                                />
                                                            </button>
                                                        </template>
                                                    </div>
                                                </TableHead>
                                            </template>
                                            <template v-if="!reordering && entityList.data.some(item => entityList.instanceHasActions(item, showEntityState))">
                                                <TableHead class="w-2">
                                                    <span class="sr-only">Edit</span>
                                                </TableHead>
                                            </template>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody ref="sortableTableBody">
                                        <template v-for="(item, itemIndex) in reorderedItems ?? entityList.data">
                                            <TableRow class="relative [&:has([aria-expanded=true])]:bg-muted/50">
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
                                                <template v-for="(field, fieldIndex) in entityList.fields">
                                                    <template v-if="field.key === '@state' && entityList.config.state && showEntityState">
                                                        <TableCell>
                                                            <DropdownMenu>
                                                                <DropdownMenuTrigger as-child>
                                                                    <Button class="relative" variant="ghost" size="sm">
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
                                                                <a class="absolute inset-0" :href="entityList.instanceUrl(item)"></a>
                                                            </template>
                                                            <template v-if="field.html">
                                                                <CaptureInternalLinks>
                                                                    <div class="[&_a]:relative [&_a]:underline [&_a]:z-10 [&_a]:text-indigo-600 [&_a:hover]:text-indigo-900"
                                                                        :class="{ '[&_a]:pointer-events-none': selecting || reordering }"
                                                                        v-html="item[field.key]"
                                                                    ></div>
                                                                </CaptureInternalLinks>
                                                            </template>
                                                            <template v-else>
                                                                {{ item[field.key] }}
                                                            </template>
                                                        </TableCell>
                                                    </template>
                                                </template>

                                                <template v-if="!reordering && entityList.instanceHasActions(item, showEntityState)">
                                                    <TableCell class="relative">
                                                        <EntityActions v-slot="{ menuOpened, stateSubmenuOpened, requestedStateMenu, openStateMenu }">
                                                            <div class="flex items-center">
                                                                <DropdownMenu v-model:open="menuOpened.value">
                                                                    <DropdownMenuTrigger as-child>
                                                                        <Button class="pointer-events-auto" variant="ghost" size="icon">
                                                                            <MoreHorizontal class="h-4" />
                                                                        </Button>
                                                                    </DropdownMenuTrigger>
                                                                    <DropdownMenuContent side="bottom" :align="requestedStateMenu.value ? 'end' : 'center'">
                                                                        <template v-if="entityList.config.state && showEntityState">
                                                                            <DropdownMenuGroup>
                                                                                <DropdownMenuSub v-model:open="stateSubmenuOpened.value">
                                                                                    <DropdownMenuSubTrigger :disabled="!entityList.instanceCanUpdateState(item)">
                                                                                        <template v-if="entityList.instanceCanUpdateState(item)">
                                                                                            {{ __('sharp::modals.entity_state.edit.title') }} :
                                                                                        </template>
                                                                                        <Badge class="ml-1.5" variant="outline">
                                                                                            <StateIcon class="-ml-0.5 mr-1.5" :state-value="entityList.instanceStateValue(item)" />
                                                                                            {{ entityList.instanceStateValue(item)?.label }}
                                                                                        </Badge>
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
                                                            </div>
                                                        </EntityActions>
                                                    </TableCell>
                                                </template>
                                            </TableRow>
                                        </template>
                                    </TableBody>
                                </Table>
                            </template>
                            <template v-else>
                                {{ __('sharp::entity_list.empty_text') }}
                            </template>
                        </CardContent>
                        <template v-if="entityList.meta?.last_page > 1">
                            <CardFooter v-show="!collapsed">
                                <div class="mt-12">
                                    <Pagination
                                        :paginator="entityList"
                                        :links-openable="!inline"
                                        @change="onPageChange"
                                    />
                                </div>
                            </CardFooter>
                        </template>
                    </template>
                </Card>
            </div>
        </div>
    </WithCommands>
</template>
