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
    import CommandsDropdown from "@/commands/components/CommandsDropdown.vue";
    import WithCommands from "@/commands/components/WithCommands.vue";
    import { CommandManager } from "@/commands/CommandManager";
    import type { Ref } from "vue";
    import { computed, ref, watch } from "vue";
    import { showAlert, showDeleteConfirm } from "@/utils/dialogs";
    import { Instance, InstanceId } from "../types";
    import {getAppendableParentUri, route} from "@/utils/url";
    import { Dropdown, DropdownItem, DropdownSeparator, StateIcon,  Button,  Search } from '@/components/ui';
    import { ChevronDownIcon } from "@heroicons/vue/20/solid";
    import EntityActions from "./EntityActions.vue";
    import { api } from "@/api";
    import Pagination from "@/components/ui/Pagination.vue";
    import CaptureInternalLinks from "@/components/CaptureInternalLinks.vue";
    import SharpFilter from "@/filters/components/Filter.vue";
    import PageAlert from "@/components/PageAlert.vue";
    import Draggable from "vuedraggable";

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
    const selectedItems: Ref<InstanceId[] | null> = ref(null);
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
                ids: selectedItems.value,
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
    watch(reordering, reordering => emit('reordering', reordering));

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
                                    <Button outline @click="reorderedItems = null">
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
                                    <Button outline @click="reorderedItems = [...entityList.data]">
                                        {{ __('sharp::action_bar.list.reorder_button') }}
                                    </Button>
                                </div>
                            </template>
                        </template>

                        <template v-if="entityList.canSelect && !reordering">
                            <template v-if="selecting">
                                <div class="col-auto">
                                    <Button key="cancel" outline @click="selectedItems = null">
                                        {{ __('sharp::action_bar.list.reorder_button.cancel') }}
                                    </Button>
                                </div>
                            </template>
                            <template v-else>
                                <div class="col-auto">
                                    <Button key="select" outline @click="selectedItems = []">
                                        {{ __('sharp::action_bar.list.select_button') }}
                                    </Button>
                                </div>
                            </template>
                        </template>

                        <template v-if="entityList.dropdownEntityCommands(selecting)?.flat().length && !reordering">
                            <div class="col-auto">
                                <CommandsDropdown
                                    class="bg-white"
                                    :commands="entityList.dropdownEntityCommands(selecting)"
                                    :small="false"
                                    :outline="!selecting"
                                    :disabled="reordering"
                                    :selecting="selecting"
                                    @select="onEntityCommand"
                                >
                                    <template v-slot:text>
                                        {{ __('sharp::entity_list.commands.entity.label') }}
                                        <template v-if="selecting">
                                            ({{ selectedItems.length }} selected)
                                        </template>
                                    </template>
                                </CommandsDropdown>
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
                                    <button class="btn btn-link d-inline-flex align-items-center btn-sm fs-8" @click="onResetAll">
                                        {{ __('sharp::filters.reset_all') }}
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                <template v-if="entityList.data?.length > 0">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <template v-if="selecting">
                                        <th>
                                            <span class="sr-only">Select...</span>
                                        </th>
                                    </template>
                                    <template v-for="field in entityList.fields">
                                        <th scope="col"
                                            class="py-3.5 px-3 text-left text-sm w-[calc(var(--size)/12*100%)] font-semibold text-gray-900 first:pl-4 sm:first:pl-6"
                                            :style="{ '--size': field.size === 'fill' ? 12 / entityList.fields.length : field.size }"
                                        >
                                            <template v-if="field.sortable">
                                                <button class="inline-flex group" @click="onSortClick(field.key)">
                                                    {{ field.label }}
                                                    <span class="ml-2 flex-none rounded bg-gray-100 text-gray-900 group-hover:bg-gray-200"
                                                        :class="{ 'invisible': query.sort !== field.key }"
                                                    >
                                                        <ChevronDownIcon class="h-5 w-5" :class="{ 'rotate-180': query.dir === 'asc' }" aria-hidden="true" />
                                                    </span>
                                                </button>
                                            </template>
                                            <template v-else>
                                                {{ field.label }}
                                            </template>
                                        </th>
                                    </template>
                                    <template v-if="!reordering && entityList.data.some(item => entityList.instanceHasActions(item, showEntityState))">
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                            <span class="sr-only">Edit</span>
                                        </th>
                                    </template>
                                </tr>
                            </thead>
                            <Draggable
                                tag="tbody"
                                class="divide-y divide-gray-200 bg-white"
                                :options="{ disabled: !reordering }"
                                :modelValue="reorderedItems ?? entityList.data"
                                :item-key="entityList.config.instanceIdAttribute"
                                @update:modelValue="reorderedItems = $event"
                            >
                                <template v-slot:item="{ element: item }">
                                    <tr class="relative" :class="{ 'hover:bg-gray-50': entityList.instanceUrl(item) }">
                                        <template v-if="selecting">
                                            <td class="px-7 sm:w-12 sm:px-6">
                                                <input
                                                    :id="`check-${entityKey}-${entityList.instanceId(item)}`"
                                                    class=""
                                                    type="checkbox"
                                                    v-model="selectedItems"
                                                    :name="entityKey"
                                                    :value="entityList.instanceId(item)"
                                                />
                                                <label class="absolute inset-0 z-20" :for="`check-${entityKey}-${entityList.instanceId(item)}`">
                                                    <span class="sr-only">Select</span>
                                                </label>
                                            </td>
                                        </template>
                                        <template v-for="(field, i) in entityList.fields">
                                            <td class="py-4 px-3 text-sm font-medium text-gray-900 first:pl-4 sm:first:pl-6">
                                                <template v-if="i === 0 && entityList.instanceUrl(item) && !selecting && !reordering">
                                                    <a class="absolute inset-0" :href="entityList.instanceUrl(item)"></a>
                                                </template>
                                                <template v-if="field.html">
                                                    <CaptureInternalLinks>
                                                        <div class="[&_a]:relative [&_a]:z-10 [&_a]:underline [&_a]:text-indigo-600 [&_a:hover]:text-indigo-900"
                                                            v-html="item[field.key]"
                                                        ></div>
                                                    </CaptureInternalLinks>
                                                </template>
                                                <template v-else>
                                                    {{ item[field.key] }}
                                                </template>
                                            </td>
                                        </template>
                                        <template v-if="!reordering && entityList.instanceHasActions(item, showEntityState)">
                                            <td class="relative py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                <EntityActions v-slot="{ stateDropdownRef, openStateDropdown }">
                                                    <div class="SharpEntityList__actions">
                                                        <div class="flex items-center">
                                                            <template v-if="entityList.config.state && showEntityState">
                                                                <Dropdown
                                                                    small
                                                                    outline
                                                                    :show-caret="false"
                                                                    right
                                                                    :disabled="!entityList.instanceCanUpdateState(item)"
                                                                    :title="entityList.instanceStateValue(item)?.label ?? String(entityList.instanceState(item))"
                                                                    :ref="stateDropdownRef"
                                                                >
                                                                    <template v-slot:text>
                                                                        <StateIcon :state-value="entityList.instanceStateValue(item)" />
                                                                    </template>
                                                                    <template v-for="stateValue in entityList.config.state.values" :key="stateValue.value">
                                                                        <DropdownItem
                                                                            :active="entityList.instanceState(item) === stateValue.value"
                                                                            @click="onInstanceStateChange(stateValue.value, entityList.instanceId(item))"
                                                                        >
                                                                            <div class="flex items-center">
                                                                                <StateIcon class="me-1" :state-value="stateValue" />
                                                                                <span class="text-truncate">{{ stateValue.label }}</span>
                                                                            </div>
                                                                        </DropdownItem>
                                                                    </template>
                                                                </Dropdown>
                                                            </template>

                                                            <template v-if="entityList.instanceHasActions(item, showEntityState)">
                                                                <CommandsDropdown
                                                                    class="SharpEntityList__commands-dropdown"
                                                                    outline
                                                                    :commands="entityList.instanceCommands(item)"
                                                                    :toggle-class="{ 'opacity-50': selecting }"
                                                                    :show-caret="false"
                                                                    @select="onInstanceCommand($event, entityList.instanceId(item))"
                                                                >
                                                                    <template v-slot:text>
                                                                        <!-- EllipsisVerticalIcon -- @heroicons/vue/20 -->
                                                                        <svg class="d-block" width="22" height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                            <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
                                                                        </svg>
                                                                    </template>

                                                                    <template v-if="entityList.config.state && showEntityState" v-slot:prepend>
                                                                        <DropdownItem
                                                                            :disabled="!entityList.instanceCanUpdateState(item)"
                                                                            @click.prevent="openStateDropdown"
                                                                        >
                                                                            <div class="flex items-center">
                                                                                <StateIcon class="me-1" :state-value="entityList.instanceStateValue(item)" />
                                                                                <div>
                                                                                    <template v-if="entityList.instanceCanUpdateState(item)">
                                                                                        {{ __('sharp::modals.entity_state.edit.title') }} :
                                                                                    </template>
                                                                                    {{ entityList.instanceStateValue(item)?.label ?? entityList.instanceState(item) }}
                                                                                </div>
                                                                            </div>
                                                                        </DropdownItem>
                                                                        <DropdownSeparator />
                                                                    </template>

                                                                    <template v-if="!entityList.config.deleteHidden && entityList.instanceCanDelete(item)" v-slot:append>
                                                                        <template v-if="entityList.instanceCommands(item)?.flat().length">
                                                                            <DropdownSeparator />
                                                                        </template>
                                                                        <DropdownItem class="text-red-600" @click="onDelete(entityList.instanceId(item))">
                                                                            {{ __('sharp::action_bar.form.delete_button') }}
                                                                        </DropdownItem>
                                                                    </template>
                                                                </CommandsDropdown>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </EntityActions>
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                            </Draggable>
                        </table>
                    </div>

<!--                    <DataList-->
<!--                        :reordering="reordering"-->
<!--                        :sort="query.sort ?? entityList.config.defaultSort"-->
<!--                        :dir="query.dir ?? entityList.config.defaultSortDir"-->
<!--                        @change="onReorder"-->
<!--                        @sort-change="onSortChange"-->
<!--                    >-->

<!--                        <template v-slot:item="{ item }">-->
<!--                            <DataListRow-->
<!--                                :url="entityList.instanceUrl(item)"-->
<!--                                :columns="fields"-->
<!--                                :highlight="selectedItems?.includes(entityList.instanceId(item))"-->
<!--                                :deleting="deletingItem ? entityList.instanceId(item) === entityList.instanceId(deletingItem) : false"-->
<!--                                :row="item"-->
<!--                            >-->
<!--                            </DataListRow>-->
<!--                        </template>-->
<!--                    </DataList>-->
                </template>
                <template v-else>
                    {{ __('sharp::entity_list.empty_text') }}
                </template>

                <template v-if="entityList.meta?.last_page > 1">
                    <div class="mt-12">
                        <Pagination
                            :paginator="entityList"
                            :links-openable="!inline"
                            @change="onPageChange"
                        />
                    </div>
                </template>
            </div>
        </div>
    </WithCommands>
</template>
