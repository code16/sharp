<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import MultiformDropdown from "./MultiformDropdown.vue";
    import { FilterManager } from "@sharp/filters/src/FilterManager";
    import { EntityList } from "../EntityList";
    import { CommandData, FilterData } from "@/types";
    import { useCommands } from "@sharp/commands/src/useCommands";
    import { CommandManager } from "@sharp/commands/src/CommandManager";
    import { computed, ref } from "vue";
    import type { Ref } from "vue";
    import { showAlert } from "@/utils/dialogs";

    const props = defineProps<{
        entityKey: string,
        entityList: EntityList,
        inline: boolean,
        showCreateButton: boolean,
        showReorderButton: boolean,
        showSearchField: boolean,
        showEntityState: boolean,
        hiddenCommands: object,
        filters: FilterManager,
        commands: CommandManager,
        visible: boolean,
        query: object,
    }>();

    const emit = defineEmits(['update:query']);
    const selectedItems: Ref<Array<number|string> | null> = ref(null);
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

    function onStateChange(value, instanceId: string | number) {
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

    function onInstanceCommand(command: CommandData, instanceId: string | number) {
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
        const { commands, entityKey, query } = props;

        await commands.send(command, {
            postCommand: route('code16.sharp.api.list.command.entity', { entityKey, commandKey: command.key }),
            getForm: route('code16.sharp.api.list.command.entity.form', { entityKey, commandKey: command.key }),
            query: {
                ...query,
                ids: selectedItems.value,
            },
            entityKey,
        });

        selectedItems.value = null;
    }
</script>

<template>
    <div class="SharpEntityList">
        <div class="flex">
            <div class="flex-1">
                <slot name="title" :count="totalCount" />
            </div>
            <template v-if="ready">
                <div class="flex gap-3">
                    <template v-if="canReorder && !selecting">
                        <template v-if="reordering">
                            <div class="col-auto">
                                <Button outline @click="handleReorderButtonClicked">
                                    {{ __('sharp::action_bar.list.reorder_button.cancel') }}
                                </Button>
                            </div>
                            <div class="col-auto">
                                <Button @click="handleReorderSubmitted">
                                    {{ __('sharp::action_bar.list.reorder_button.finish') }}
                                </Button>
                            </div>
                        </template>
                        <template v-else>
                            <div class="col-auto">
                                <Button outline @click="handleReorderButtonClicked">
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
                                <MultiformDropdown
                                    :forms="entityList.forms"
                                    right
                                    @select="handleCreateButtonClicked($event)"
                                />
                            </template>
                            <template v-else>
                                <Button :disabled="reordering || selecting" @click="handleCreateButtonClicked">
                                    {{ __('sharp::action_bar.list.create_button') }}
                                </Button>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <template v-if="ready">
            <div v-show="visible">
                <template v-if="entityList.config.globalMessage">
                    <GlobalMessage
                        :options="entityList.config.globalMessage"
                        :data="entityList.data"
                        :fields="entityList.fields"
                    />
                </template>

                <DataList
                    :items="items"
                    :columns="columns"
                    :page="page"
                    :paginated="paginated"
                    :total-count="totalCount"
                    :page-size="pageSize"
                    :reordering="reordering"
                    :sort="sortedBy"
                    :dir="sortDir"
                    @change="handleReorderedItemsChanged"
                    @sort-change="handleSortChanged"
                    @page-change="handlePageChanged"
                >
                    <template v-slot:empty>
                        {{ __('sharp::entity_list.empty_text') }}
                    </template>

                    <template v-if="canSearch || entityList.visibleFilters?.length" v-slot:prepend>
                        <div class="p-3">
                            <div class="row gy-3 gx-4">
                                <template v-if="canSearch">
                                    <div class="col-md-auto">
                                        <Search
                                            class="h-100 mw-100"
                                            style="--width: 150px; --focused-width: 250px;"
                                            :value="search"
                                            :placeholder="__('sharp::action_bar.list.search.placeholder')"
                                            :disabled="reordering"
                                            @submit="handleSearchSubmitted"
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
                                            <template v-if="filters.isValuated(entityList.visibleFilters) || search">
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
                                    :value="instanceId(item)"
                                />
                                <label class="d-block position-absolute start-0 top-0 w-100 h-100" style="z-index: 3" :for="`check-${entityKey}-${entityList.instanceId(item)}`">
                                    <span class="visually-hidden">Select</span>
                                </label>
                            </template>
                            <template v-if="hasActionsColumn" v-slot:append="props">
                                <EntityActions
                                    :config="config"
                                    :has-state="instanceHasState(item)"
                                    :state="instanceState(item)"
                                    :state-options="instanceStateOptions(item)"
                                    :state-disabled="!instanceHasStateAuthorization(item)"
                                    :has-commands="instanceHasCommands(item)"
                                    :commands="instanceCommands(item)"
                                    :can-delete="instanceCanDelete(item)"
                                    :selecting="selecting"
                                    @command="onInstanceCommand($event, entityList.instanceId(item))"
                                    @state-change="handleInstanceStateChanged(item, $event)"
                                    @delete="handleInstanceDeleteClicked(item)"
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
        </template>
        <template v-else-if="visible && inline">
            <Loading small fade />
        </template>

<!--        <CommandFormModal-->
<!--            :command="currentCommand"-->
<!--            :entity-key="entityKey"-->
<!--            :instance-id="currentCommandInstanceId"-->
<!--            v-bind="commandFormProps"-->
<!--            v-on="commandFormListeners"-->
<!--        />-->
<!--        <CommandViewPanel-->
<!--            :content="commandViewContent"-->
<!--            @close="handleCommandViewPanelClosed"-->
<!--        />-->
    </div>
</template>

<script lang="ts">
    import isEqual from 'lodash/isEqual';
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
        // props: {
        //     entityKey: String,
        //     inline: Boolean,
        //
        //     showCreateButton: {
        //         type: Boolean,
        //         default: true,
        //     },
        //     showReorderButton: {
        //         type: Boolean,
        //         default: true,
        //     },
        //     showSearchField: {
        //         type: Boolean,
        //         default: true,
        //     },
        //     showEntityState: {
        //         type: Boolean,
        //         default: true,
        //     },
        //     hiddenCommands: Object,
        //     filters: Array,
        //     visible: {
        //         type: Boolean,
        //         default: true,
        //     },
        //     entityList: Object,
        // },
        data() {
            return {
                ready: false,
                loading: false,

                page: 0,
                search: '',
                sortedBy: null,
                sortDir: null,
                sortDirs: {},

                reordering: false,
                reorderedItems: null,

                selecting: false,

                deletingItem: null,

                containers: null,
                layout: null,
                data: null,
                fields: null,
                config: null,
                authorizations: null,
                forms: null,
                breadcrumb: null,

                currentCommandInstanceId: null,
            }
        },
        watch: {
            query(query, oldQuery) {
                if(!isEqual(query, oldQuery)) {
                    this.init();
                }
            },
            visible(visible) {
                if(visible && !this.ready) {
                    this.init();
                }
            },
            entityList() {
                this.init();
            },
        },
        computed: {

            /**
             * Action bar computed data
             */
            canReorder() {
                return this.showReorderButton
                    && this.config.reorderable
                    && this.authorizations.update
                    && this.items.length > 1;
            },
            canSearch() {
                return this.showSearchField && !!this.config.searchable;
            },

            /**
             * Data list props
             */
            items() {
                return this.data?.list.items ?? [];
            },
            columns() {
                return this.layout.map(columnLayout => ({
                    ...columnLayout,
                    ...this.containers[columnLayout.key]
                }));
            },
            paginated() {
                return !!this.config.paginated;
            },
            totalCount() {
                return this.data?.list.totalCount ?? this.items.length;
            },
            pageSize() {
                return this.data?.list.pageSize;
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
            synchronous() {
                return !!this.entityKey;
            },
        },
        methods: {
            storeGetter(name) {
                return this.$store.getters[`${this.module}/${name}`];
            },
            storeDispatch(name, payload) {
                return this.$store.dispatch(`${this.module}/${name}`, payload);
            },

            /**
             * [Action bar] events
             */
            handleSearchSubmitted(search) {
                this.search = search;
                this.storeDispatch('setQuery', {
                    ...this.query,
                    search,
                    page: 1,
                });
            },
            handleReorderButtonClicked() {
                this.reordering = !this.reordering;
                this.reorderedItems = this.reordering ? [...this.items] : null;
                this.$emit('reordering', this.reordering);
            },
            handleReorderSubmitted() {
                return this.storeDispatch('reorder', {
                    instances: this.reorderedItems.map(item => this.instanceId(item))
                }).then(() => {
                    this.data.list.items = [...this.reorderedItems];
                    this.reorderedItems = null;
                    this.reordering = false;
                    this.$emit('reordering', false);
                });
            },
            handleCreateButtonClicked(multiform) {
                const formUrl = multiform
                    ? this.formUrl({ formKey:multiform.key })
                    : this.formUrl();

                location.href = formUrl;
            },
            handleSelectButtonClicked() {
                this.selecting = true
            },
            handleSelectCancelled() {
               this.stopSelecting();
            },
            stopSelecting() {
                this.selecting = false;
                this.selectedItems = [];
            },

            /**
             * [Data list] getters
             */
            instanceId(instance) {
                const idAttribute = this.config.instanceIdAttribute;
                return idAttribute ? instance[idAttribute] : instance.id;
            },
            instanceState(instance) {
                if(!this.instanceHasState(instance)) {
                    return null;
                }
                const stateAttribute = this.config.state.attribute;
                return stateAttribute ? instance[stateAttribute] : instance.state;
            },
            instanceHasState(instance) {
                return !!this.config.state && this.showEntityState;
            },
            instanceHasCommands(instance) {
                const allCommands = this.instanceCommands(instance).flat();
                return allCommands.length > 0;
            },
            instanceHasStateAuthorization(instance) {
                if(!this.instanceHasState(instance)) {
                    return false;
                }
                const { authorization } = this.config.state;
                const instanceId = this.instanceId(instance);

                return Array.isArray(authorization)
                    ? authorization.includes(instanceId)
                    : !!authorization;
            },
            instanceCommands(instance) {
                return (this.config.commands.instance || []).reduce((res, group) => [
                    ...res, group.filter(command => this.isInstanceCommandAllowed(instance, command))
                ], []);
            },
            instanceStateOptions(instance) {
                if(!this.config.state) {
                    return null;
                }
                const state = this.instanceState(instance);
                return this.config.state.values.find(stateValue => stateValue.value === state);
            },
            instanceForm(instance) {
                const instanceId = this.instanceId(instance);
                return this.multiforms.find(form => form.instances.includes(instanceId));
            },
            instanceHasViewAuthorization(instance) {
                const instanceId = this.instanceId(instance);
                const viewAuthorizations = this.authorizations.view;
                return Array.isArray(viewAuthorizations)
                    ? viewAuthorizations.includes(instanceId)
                    : !!viewAuthorizations;
            },
            instanceCanDelete(instance) {
                const instanceId = this.instanceId(instance);
                const deleteAuthorized = Array.isArray(this.authorizations.delete)
                    ? this.authorizations.delete?.includes(instanceId)
                    : !!this.authorizations.delete;
                return !this.config.deleteHidden && deleteAuthorized;
            },

            /**
             * [Data list] actions
             */
            setState(instance, state) {
                const instanceId = this.instanceId(instance);
                return this.axiosInstance.post(`${this.apiPath}/state/${instanceId}`, {
                    attribute: this.config.state.attribute,
                    value: state
                })
                    .then(response => {
                        const { data } = response;
                        this.handleCommandActionRequested(data.action, data);
                    })
                    .catch(error => {
                        const data = error.response?.data;
                        if(error.response?.status === 422) {
                            showAlert(data.message, {
                                title: __('modals.state.422.title'),
                                isError: true,
                            });
                        }
                    })
            },


            /**
             * [Data list] events
             */
            handleSortChanged({ prop, dir }) {
                this.storeDispatch('setQuery', {
                    ...this.query,
                    page: 1,
                    sort: prop,
                    dir: dir,
                });
            },
            handleReorderedItemsChanged(items) {
                this.reorderedItems = items;
            },
            handlePageChanged(page) {
                this.storeDispatch('setQuery', {
                    ...this.query,
                    page
                });
            },
            async handleInstanceDeleteClicked(instance) {
                const instanceId = this.instanceId(instance);
                this.deletingItem = instance;
                try {
                    if(await showDeleteConfirm(this.config.deleteConfirmationText)) {
                        await deleteEntityListInstance({ entityKey: this.entityKey, instanceId });
                        this.init(); // todo handle with inertia
                    }
                } finally {
                     this.deletingItem = null;
                }
            },

            /**
             * Helpers
             */
            formUrl({ formKey, instanceId }={}) {
                const formEntityKey = formKey ? `${this.entityKey}:${formKey}` : this.entityKey;

                if(instanceId) {
                    return route('code16.sharp.form.edit', {
                        uri: getAppendableUri(),
                        entityKey: formEntityKey,
                        instanceId,
                    });
                }

                return route('code16.sharp.form.create', {
                    uri: getAppendableUri(),
                    entityKey: formEntityKey,
                });
            },
            showUrl({ instanceId }={}) {
                return route('code16.sharp.show.show', {
                    uri: getAppendableUri(),
                    entityKey: this.entityKey,
                    instanceId,
                });
            },

            /**
             * Commands
             */
            initCommands() {
                this.addCommandActionHandlers({
                    'refresh': this.handleRefreshCommand
                });
            },
            handleInstanceCommandRequested(instance, command) {
                const instanceId = this.instanceId(instance);
                this.handleCommandRequested(command, { instanceId });
            },
            handleEntityCommandRequested(command) {
                const selectedInstanceIds = this.selecting ? this.selectedItems : null;
                this.handleCommandRequested(command, { selectedInstanceIds });
            },
            async handleCommandRequested(command, { instanceId, selectedInstanceIds } = {}) {
                const query = this.commandsQuery;
                const endpoint = this.commandEndpoint(command.key, instanceId);

                this.currentCommandInstanceId = instanceId;
                await this.sendCommand(command, {
                    postCommand: data => api.post(endpoint, {
                        query: {
                            ...query,
                            ids: selectedInstanceIds,
                        },
                        ...data
                    }, { responseType:'blob' }),
                    getForm: commandQuery => api.get(`${endpoint}/form`, {
                        params: { ...query, ...commandQuery, ids: selectedInstanceIds } })
                        .then(response => response.data),
                });
                this.stopSelecting();
            },
            handleRefreshCommand(data) {
                const findInstance = (list, instance) => list.find(item => this.instanceId(instance) === this.instanceId(item));
                this.data.list.items = this.items.map(item =>
                    findInstance(data.items, item) || item
                );
            },
            commandEndpoint(commandKey, instanceId) {
                return `${this.apiPath}/command/${commandKey}${instanceId?`/${instanceId}`:''}`;
            },
            isInstanceCommandAllowed(instance, command) {
                const instanceId = this.instanceId(instance);
                const hiddenCommands = this.hiddenCommands ? this.hiddenCommands.instance : null;
                const hasAuthorization = Array.isArray(command.authorization)
                    ? command.authorization.includes(instanceId)
                    : !!command.authorization;
                return hasAuthorization && !(hiddenCommands || []).includes(command.key);
            },

            /**
             * Data
             */
            mount({ containers, layout, data, fields, config, authorizations, forms, breadcrumb }) {
                this.containers = containers;
                this.layout = layout;
                this.data = data ?? {};
                this.fields = fields ?? {};
                this.config = {
                    ...config,
                    commands: config?.commands ?? {},
                    filters: config?.filters ?? [],
                };
                this.authorizations = authorizations;
                this.forms = forms;
                this.breadcrumb = breadcrumb;

                this.page = this.data.list.page;
                !this.sortDir && (this.sortDir = this.config.defaultSortDir);
                !this.sortedBy && (this.sortedBy = this.config.defaultSort);
            },
            bindParams(params) {
                let { search, page, sort, dir } = params;

                this.search = search;
                page && (this.page = Number(page));
                sort && (this.sortedBy = sort);
                dir && (this.sortDir = dir);
            },
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
            this.init();
            this.initCommands();
        },
    }
</script>
