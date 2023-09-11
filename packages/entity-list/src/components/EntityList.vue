<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import MultiformDropdown from "./MultiformDropdown.vue";
    import { FilterManager } from "@sharp/filters/src/FilterManager";
    import { EntityList } from "../EntityList";
    import { CommandData, EntityListQueryParamsData, FilterData } from "@/types";
    import { WithCommands } from "@sharp/commands";
    import { CommandManager } from "@sharp/commands/src/CommandManager";
    import type { Ref } from "vue";
    import { computed, ref, watch } from "vue";
    import { showAlert, showDeleteConfirm } from "@/utils/dialogs";
    import { deleteEntityListInstance } from "../api";
    import { Instance, InstanceId } from "../types";
    import { getAppendableUri, route } from "@/utils/url";
    import { Dropdown, DropdownItem } from '@sharp/ui';

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

    function onSearchSubmit(search) {
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

    function onPageChange(page) {
        emit('update:query', {
            ...props.query,
            page,
        });
    }

    function onSortChange({ prop, dir }) {
        emit('update:query', {
            ...props.query,
            page: 1,
            sort: prop,
            dir,
        });
    }

    function onInstanceStateChange(value, instanceId: InstanceId) {
        const { commands, entityKey } = props;

        api.post(route('code16.sharp.api.list.state', { entityKey, instanceId }), { value })
            .then(response => {
                commands.handleCommandReturn(response.data);
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
                commands.handleCommandReturn({ action: 'reload' });
            }
        } finally {
            deletingItem.value = null;
        }
    }

    const reorderedItems: Ref<InstanceId[] | null> = ref(null);
    const reordering = computed(() => !!reorderedItems.value);
    watch(reordering, reordering => emit('reordering', reordering));

    async function onReorderSubmit() {
        const { commands } = props;

        await api.post(route('code16.sharp.api.list.reorder'), {
            instances: reorderedItems.value,
        });
        commands.handleCommandReturn({ action: 'reload' });
        reorderedItems.value = null;
    }
    function onReorder(items: Instance[]) {
        reorderedItems.value = items.map(item => props.entityList.instanceId(item));
    }
</script>

<template>
    <WithCommands :commands="commands">
        <div class="SharpEntityList">
            <div class="flex">
                <div class="flex-1">
                    <slot name="title" :count="entityList.count" />
                </div>
                <template v-if="ready">
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
                                    <Button outline @click="reorderedItems = []">
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
                                                :href="route('code16.sharp.form.create', { uri: getAppendableUri(), entityKey: `${entityKey}:${form.key}` })"
                                            >
                                                {{ form.label }}
                                            </DropdownItem>
                                        </template>
                                    </Dropdown>
                                </template>
                                <template v-else>
                                    <Button
                                        :disabled="reordering || selecting"
                                        :href="route('code16.sharp.form.create', { uri: getAppendableUri(), entityKey })"
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
                <template v-if="entityList.config.globalMessage">
                    <GlobalMessage
                        :options="entityList.config.globalMessage"
                        :data="entityList.data"
                        :fields="entityList.fields"
                    />
                </template>

                <DataList
                    :items="entityList.data.list.items"
                    :columns="columns"
                    :page="entityList.data.list.page"
                    :paginated="entityList.config.paginated"
                    :total-count="entityList.count"
                    :page-size="entityList.data.list.pageSize"
                    :reordering="reordering"
                    :sort="query.sort ?? entityList.config.defaultSort"
                    :dir="query.dir ?? entityList.config.defaultSortDir"
                    @change="onReorder"
                    @sort-change="onSortChange"
                    @page-change="onPageChange"
                >
                    <template v-slot:empty>
                        {{ __('sharp::entity_list.empty_text') }}
                    </template>

                    <template v-if="showSearchField && entityList.config.searchable || entityList.visibleFilters?.length" v-slot:prepend>
                        <div class="p-3">
                            <div class="row gy-3 gx-4">
                                <template v-if="showSearchField && entityList.config.searchable">
                                    <div class="col-md-auto">
                                        <Search
                                            class="h-100 mw-100"
                                            style="--width: 150px; --focused-width: 250px;"
                                            :value="query.search"
                                            :placeholder="__('sharp::action_bar.list.search.placeholder')"
                                            :disabled="reordering"
                                            @submit="onSearchSubmit"
                                        />
                                    </div>
                                </template>
                                <template v-if="entityList.visibleFilters?.length">
                                    <div class="col-md">
                                        <div class="row gx-2 gy-2">
                                            <template v-for="filter in entityList.visibleFilters" :key="filter.key">
                                                <div class="col-auto">
                                                    <SharpFilter
                                                        :filter="filter"
                                                        :value="filters.values[filter.key]"
                                                        :disabled="reordering"
                                                        @input="onFilterChanged(filter, $event)"
                                                    />
                                                </div>
                                            </template>
                                            <template v-if="filters.isValuated(entityList.visibleFilters) || query.search">
                                                <div class="col-auto d-flex">
                                                    <button class="btn btn-link d-inline-flex align-items-center btn-sm fs-8" @click="onResetAll">
                                                        {{ __('sharp::filters.reset_all') }}
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>


                    <template v-slot:item="{ item }">
                        <DataListRow
                            :url="entityList.instanceUrl(item)"
                            :columns="columns"
                            :highlight="selectedItems?.includes(entityList.instanceId(item))"
                            :selecting="selecting"
                            :deleting="deletingItem ? entityList.instanceId(item) === entityList.instanceId(deletingItem) : false"
                            :row="item"
                        >
                            <template v-if="selecting" v-slot:prepend>
                                <input
                                    :id="`check-${entityKey}-${entityList.instanceId(item)}`"
                                    class="form-check-input d-block mt-0 me-4"
                                    type="checkbox"
                                    v-model="selectedItems"
                                    :name="entityKey"
                                    :value="entityList.instanceId(item)"
                                />
                                <label class="d-block position-absolute start-0 top-0 w-100 h-100" style="z-index: 3" :for="`check-${entityKey}-${entityList.instanceId(item)}`">
                                    <span class="visually-hidden">Select</span>
                                </label>
                            </template>
                            <template v-if="hasActionsColumn" v-slot:append="props">
                                <EntityActions
                                    :item="item"
                                    :entity-list="entityList"
                                    :show-entity-state="showEntityState"
                                    :selecting="selecting"
                                    @command="onInstanceCommand($event, entityList.instanceId(item))"
                                    @state-change="onInstanceStateChange($event, entityList.instanceId(item))"
                                    @delete="onDelete(entityList.instanceId(item))"
                                />
                            </template>
                        </DataListRow>
                    </template>

                    <template v-slot:append-body>
                        <template v-if="inline && loading">
                            <LoadingOverlay small absolute fade />
                        </template>
                    </template>
                </DataList>
            </div>
            <template v-if="loading && inline">
                <Loading small fade />
            </template>
        </div>
    </WithCommands>
</template>

<script lang="ts">
    import { showAlert, api, showDeleteConfirm } from 'sharp';
    import { __ } from "@/utils/i18n";
    import {  DynamicView, withCommands } from 'sharp/mixins';
    import {
        DataList,
        DataListRow,
        StateIcon,
        Button,
        Loading,
        LoadingOverlay,
        Modal,
        ModalSelect,
        DropdownItem,
        DropdownSeparator,
        GlobalMessage, Search,
    } from '@sharp/ui';

    import {
        CommandsDropdown,
    } from '@sharp/commands';

    import EntityActions from "./EntityActions.vue";
    import {SharpFilter} from "@sharp/filters";
    import {deleteEntityListInstance} from "../api";
    import { route, getAppendableUri } from "@/utils/url";

    export default {
        name: 'SharpEntityList',
        mixins: [DynamicView, withCommands],
        components: {
            Search, SharpFilter,
            EntityActions,

            DataList,
            DataListRow,

            StateIcon,
            CommandsDropdown,

            Button,
            Modal,
            ModalSelect,

            GlobalMessage,

            DropdownItem,
            DropdownSeparator,

            Loading,
            LoadingOverlay,
        },
        data() {
            return {
                ready: false,
            }
        },
        computed: {
            /**
             * Data list props
             */
            columns() {
                return this.layout.map(columnLayout => ({
                    ...columnLayout,
                    ...this.containers[columnLayout.key]
                }));
            },

            hasActionsColumn() {
                if(this.reordering) {
                    return false;
                }
                return this.items.some(instance =>
                    this.instanceHasState(instance) ||
                    this.instanceHasCommands(instance) ||
                    this.instanceCanDelete(instance)
                );
            },
        },
        methods: {
            async init() {
                if(!this.visible) {
                    return;
                }
                this.loading = true;
                await this.storeDispatch('setEntityKey', this.entityKey);
                if(this.entityList) {
                    this.mount(this.entityList);
                } else {
                    await this.get()
                        .catch(error => {
                            this.$emit('error', error);
                            return Promise.reject(error);
                        });
                }
                this.bindParams(this.query);

                await this.storeDispatch('update', {
                    config: this.config,
                    filtersValues: this.getFiltersValuesFromQuery(this.query),
                });

                this.$emit('change', {
                    data: this.data,
                    layout: this.layout,
                    config: this.config,
                    containers: this.containers,
                    authorizations: this.authorizations,
                    forms: this.forms,
                });

                this.ready = true;
                this.loading = false;
            },
        },
        beforeMount() {
            // this.init();
            // this.initCommands();
        },
    }
</script>
