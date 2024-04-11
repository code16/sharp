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
    import { Ref, watchEffect } from "vue";
    import { computed, ref, watch } from "vue";
    import { showAlert, showDeleteConfirm } from "@/utils/dialogs";
    import { Instance, InstanceId } from "../types";
    import { getAppendableParentUri, route } from "@/utils/url";
    import { Button } from '@/components/ui/button';
    import { Dropdown, DropdownItem, DropdownSeparator, StateIcon, Search } from '@/components/ui';
    import EntityActions from "./EntityActions.vue";
    import { api } from "@/api/api";
    import Pagination from "@/components/ui/Pagination.vue";
    import CaptureInternalLinks from "@/components/CaptureInternalLinks.vue";
    import SharpFilter from "@/filters/components/Filter.vue";
    import PageAlert from "@/components/PageAlert.vue";
    import { useDraggable } from "vue-draggable-plus";
    import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
    import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
    import { ChevronDown, MoreHorizontal } from "lucide-vue-next";
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
    import { SelectTrigger as PrimitiveSelect } from "radix-vue";
    import { DropdownMenuPortal } from "radix-vue";
    import CommandDropdownItems from "@/commands/components/CommandDropdownItems.vue";
    import { Badge } from "@/components/ui/badge";
    import { SelectTrigger } from "@/components/ui/select";

    const props = withDefaults(defineProps<{
        entityKey: string,
        entityList: EntityList,
        filters: FilterManager,
        commands: CommandManager,
        query: EntityListQueryParamsData,
        inline?: boolean,
        showCreateButton?: boolean,
        showReorderButton?: boolean,
        showSearchField?: boolean,
        showEntityState?: boolean,
        loading?: boolean,
    }>(), {
        showCreateButton: true,
        showReorderButton: true,
        showSearchField: true,
        showEntityState: true,
    });

    const emit = defineEmits(['update:query', 'reordering']);
    const selectedItems: Ref<{ [key: InstanceId]: boolean } | null> = ref(null);
    const selecting = computed(() => !!selectedItems.value);

    function onFilterChanged(filter: FilterData, value: FilterData['value']) {
        emit('update:query', {
            ...props.query,
            ...props.filters.nextQuery(filter, value),
            page: 1,
        });
    }

    function onSearchSubmit(search: string) {
        emit('update:query', {
            ...props.query,
            search,
        });
    }

    function onResetAll() {
        emit('update:query', {
            ...props.query,
            ...props.filters.defaultQuery(props.entityList.visibleFilters),
            search: null,
            page: 1,
        });
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
                ...filters.getQueryParams(filters.values),
                ids: Object.entries(selectedItems.value)
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
            <div class="flex">
                <div class="flex-1">
                    <slot name="title" />
                </div>
                <template v-if="entityList">
                    <div class="flex gap-3">
                        <template v-if="showReorderButton && entityList.canReorder && !selecting">
                            <template v-if="reordering">
                                <div class="col-auto">
                                    <Button variant="outline" @click="reorderedItems = null">
                                        {{ __('sharp::action_bar.list.reorder_button.cancel') }}
                                    </Button>
                                </div>
                                <div class="col-auto">
                                    <Button @click="onReorderSubmit">
                                        {{ __('sharp::action_bar.list.reorder_button.finish') }}
                                    </Button>
                                </div>
                            </template>
                            <template v-else>
                                <div class="col-auto">
                                    <Button variant="outline" @click="reorderedItems = [...entityList.data]">
                                        {{ __('sharp::action_bar.list.reorder_button') }}
                                    </Button>
                                </div>
                            </template>
                        </template>

                        <template v-if="entityList.canSelect && !reordering">
                            <template v-if="selecting">
                                <div class="col-auto">
                                    <Button key="cancel" variant="outline" @click="selectedItems = null">
                                        {{ __('sharp::action_bar.list.reorder_button.cancel') }}
                                    </Button>
                                </div>
                            </template>
                            <template v-else>
                                <div class="col-auto">
                                    <Button key="select" variant="outline" @click="selectedItems = Object.fromEntries(entityList.data.map(item => [item.id, false]))">
                                        {{ __('sharp::action_bar.list.select_button') }}
                                    </Button>
                                </div>
                            </template>
                        </template>

                        <template v-if="entityList.dropdownEntityCommands(selecting)?.flat().length && !reordering">
                            <div class="col-auto">
                                <DropdownMenu>
                                    <DropdownMenuTrigger>
                                        <Button :disabled="reordering">
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
                            </div>
                        </template>

                        <template v-if="entityList.primaryCommand && !reordering && !selecting">
                            <div class="col-auto">
                                <Button @click="onEntityCommand(entityList.primaryCommand)">
                                    {{ entityList.primaryCommand.label }}
                                </Button>
                            </div>
                        </template>

                        <template v-if="showCreateButton && entityList.authorizations.create && !reordering && !selecting">
                            <div class="col-auto">
                                <template v-if="entityList.forms">
                                    <Dropdown right>
                                        <template v-slot:text>
                                            {{ __('sharp::action_bar.list.forms_dropdown') }}
                                        </template>
                                        <template v-for="form in Object.values(entityList.forms).filter(form => !!form.label)">
                                            <DropdownItem
                                                :href="route('code16.sharp.form.create', { parentUri: getAppendableParentUri(), entityKey: `${entityKey}:${form.key}` })"
                                            >
                                                {{ form.label }}
                                            </DropdownItem>
                                        </template>
                                    </Dropdown>
                                </template>
                                <template v-else>
                                    <Button
                                        :disabled="reordering || selecting"
                                        :href="route('code16.sharp.form.create', { parentUri: getAppendableParentUri(), entityKey })"
                                    >
                                        {{ __('sharp::action_bar.list.create_button') }}
                                    </Button>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <div v-show="!loading">
                <template v-if="entityList.pageAlert">
                    <PageAlert
                        class="mb-3"
                        :page-alert="entityList.pageAlert"
                    />
                </template>

                <template v-if="showSearchField && entityList.config.searchable || entityList.visibleFilters?.length">
                    <div class="flex gap-3 mb-4">
                        <template v-if="showSearchField && entityList.config.searchable">
                            <Search
                                class="h-100 mw-100"
                                style="--width: 150px; --focused-width: 250px;"
                                :value="query.search"
                                :placeholder="__('sharp::action_bar.list.search.placeholder')"
                                :disabled="reordering"
                                @submit="onSearchSubmit"
                            />
                        </template>
                        <template v-if="entityList.visibleFilters?.length">
                            <div class="flex gap-3">
                                <template v-for="filter in entityList.visibleFilters" :key="filter.key">
                                    <SharpFilter
                                        :filter="filter"
                                        :value="filters.values[filter.key]"
                                        :disabled="reordering"
                                        @input="onFilterChanged(filter, $event)"
                                    />
                                </template>
                                <template v-if="filters.isValuated(entityList.visibleFilters) || query.search">
                                    <Button variant="link" size="sm" @click="onResetAll">
                                        {{ __('sharp::filters.reset_all') }}
                                    </Button>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                <Card>
                    <CardHeader>
                        <CardTitle>
                            <slot name="title" />
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <template v-if="entityList.data?.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <template v-if="selecting">
                                            <TableHead>
                                                <span class="sr-only">Select...</span>
                                            </TableHead>
                                            <template v-for="field in entityList.fields">
                                                <TableHead class="w-[calc(var(--size)/12*100%)]" :style="{ '--size': field.size === 'fill' ? 12 / entityList.fields.length : field.size }">
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
                                                <TableHead>
                                                    <span class="sr-only">Edit</span>
                                                </TableHead>
                                            </template>
                                        </template>
                                    </TableRow>
                                </TableHeader>
                                <TableBody ref="sortableTableBody">
                                    <template v-for="(item, itemIndex) in reorderedItems ?? entityList.data">
                                        <TableRow class="relative">
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
                                            <template v-if="entityList.config.state && showEntityState">
                                                <TableCell class="relative">
                                                    <DropdownMenu>
                                                        <DropdownMenuTrigger>
                                                            <Button variant="ghost" size="sm">
                                                                <Badge variant="outline">
                                                                    <StateIcon class="-ml-0.5 mr-1.5" :state-value="entityList.instanceStateValue(item)" />
                                                                    {{ entityList.instanceStateValue(item)?.label }}
                                                                </Badge>
                                                            </Button>
                                                        </DropdownMenuTrigger>
                                                        <DropdownMenuContent align="start" :align-offset="-16">
                                                            <SelectTrigger>

                                                            </SelectTrigger>
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
                                            <template v-if="!reordering && entityList.instanceHasActions(item, showEntityState)">
                                                <TableCell class="relative">
                                                    <EntityActions v-slot="{ menuOpened, stateSubmenuOpened, requestedStateMenu, openStateMenu }">
                                                        <div class="flex items-center">
                                                            <DropdownMenu v-model:open="menuOpened.value">
                                                                <DropdownMenuTrigger as-child>
                                                                    <Button variant="ghost" size="icon">
                                                                        <MoreHorizontal class="h-4" />
                                                                    </Button>
                                                                </DropdownMenuTrigger>
                                                                <DropdownMenuContent :align="requestedStateMenu.value ? 'end' : 'center'">
                                                                    <template v-if="entityList.config.state && showEntityState">
                                                                        <DropdownMenuGroup>
                                                                            <DropdownMenuSub v-model:open="stateSubmenuOpened.value">
                                                                                <DropdownMenuSubTrigger :disabled="!entityList.instanceCanUpdateState(item)">
                                                                                    <StateIcon class="mr-1" :state-value="entityList.instanceStateValue(item)" />
                                                                                    <div>
                                                                                        <template v-if="entityList.instanceCanUpdateState(item)">
                                                                                            {{ __('sharp::modals.entity_state.edit.title') }} :
                                                                                        </template>
                                                                                        {{ entityList.instanceStateValue(item)?.label ?? entityList.instanceState(item) }}
                                                                                    </div>
                                                                                </DropdownMenuSubTrigger>
                                                                                <DropdownMenuPortal>
                                                                                    <DropdownMenuSubContent>
                                                                                        <template v-for="stateValue in entityList.config.state.values" :key="stateValue.value">
                                                                                            <DropdownMenuCheckboxItem
                                                                                                :checked="stateValue.value == entityList.instanceState(item)"
                                                                                                @update:checked="(checked) => checked && onInstanceStateChange(stateValue.value, entityList.instanceId(item))"
                                                                                            >
                                                                                                <StateIcon class="mr-1" :state-value="stateValue" />
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
                    <CardFooter>
                        <template v-if="entityList.meta?.last_page > 1">
                            <div class="mt-12">
                                <Pagination
                                    :paginator="entityList"
                                    :links-openable="!inline"
                                    @change="onPageChange"
                                />
                            </div>
                        </template>
                    </CardFooter>
                </Card>
            </div>
        </div>
    </WithCommands>
</template>
