<template>
    <div class="SharpEntityList" :class="classes">
        <slot
            name="action-bar"
            :props="actionBarProps"
            :listeners="actionBarListeners"
        />

        <template v-if="ready">
            <div v-show="visible">
                <template v-if="config.globalMessage">
                    <GlobalMessage
                        :options="config.globalMessage"
                        :data="data"
                        :fields="fields"
                    />
                </template>

                <DataList
                    :items="items"
                    :columns="columns"
                    :page="page"
                    :paginated="paginated"
                    :total-count="totalCount"
                    :page-size="pageSize"
                    :reorder-active="reorderActive"
                    :sort="sortedBy"
                    :dir="sortDir"
                    @change="handleReorderedItemsChanged"
                    @sort-change="handleSortChanged"
                    @page-change="handlePageChanged"
                >
                    <template v-slot:empty>
                        {{ l('entity_list.empty_text') }}
                    </template>

                    <template v-slot:append-head>
                        <template v-if="hasEntityCommands">
                            <div class="d-flex align-items-center justify-content-end">
                                <CommandsDropdown
                                    :commands="dropdownEntityCommands"
                                    :disabled="reorderActive"
                                    @select="handleCommandRequested"
                                >
                                    <template v-slot:text>
                                        {{ l('entity_list.commands.entity.label') }}
                                    </template>
                                </CommandsDropdown>
                            </div>
                        </template>
                    </template>

                    <template v-slot:item="{ item }">
                        <DataListRow :url="instanceUrl(item)" :columns="columns" :highlight="instanceIsFocused(item)" :row="item">
                            <template v-if="hasActionsColumn" v-slot:append="props">
                                <EntityActions
                                    :config="config"
                                    :has-state="instanceHasState(item)"
                                    :state="instanceState(item)"
                                    :state-options="instanceStateOptions(item)"
                                    :state-disabled="!instanceHasStateAuthorization(item)"
                                    :has-commands="instanceHasCommands(item)"
                                    :commands="instanceCommands(item)"
                                    @command="handleInstanceCommandRequested(item, $event)"
                                    @state-change="handleInstanceStateChanged(item, $event)"
                                    @selecting="props.toggleHighlight($event)"
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

        <CommandFormModal
            :command="currentCommand"
            :entity-key="entityKey"
            :instance-id="currentCommandInstanceId"
            ref="commandForm"
        />
        <CommandViewPanel
            :content="commandViewContent"
            @close="handleCommandViewPanelClosed"
        />
    </div>
</template>

<script>
    import isEqual from 'lodash/isEqual';
    import { formUrl, showUrl, lang, showAlert, api } from 'sharp';
    import { Localization, DynamicView, withCommands } from 'sharp/mixins';
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
        GlobalMessage,
    } from 'sharp-ui';

    import {
        CommandsDropdown,
        CommandFormModal,
        CommandViewPanel,
    } from 'sharp-commands';

    import EntityActions from "./EntityActions";


    export default {
        name: 'SharpEntityList',
        mixins: [DynamicView, Localization, withCommands],
        components: {
            EntityActions,

            DataList,
            DataListRow,

            StateIcon,
            CommandsDropdown,

            Button,
            Modal,
            ModalSelect,

            CommandFormModal,
            CommandViewPanel,
            GlobalMessage,

            DropdownItem,
            DropdownSeparator,

            Loading,
            LoadingOverlay,
        },
        props: {
            entityKey: String,
            module: String,
            inline: Boolean,

            showCreateButton: {
                type: Boolean,
                default: true,
            },
            showReorderButton: {
                type: Boolean,
                default: true,
            },
            showSearchField: {
                type: Boolean,
                default: true,
            },
            showEntityState: {
                type: Boolean,
                default: true,
            },
            hiddenCommands: Object,
            visible: {
                type: Boolean,
                default: true,
            },
            focusedItem: Number,
        },
        data() {
            return {
                ready: false,
                loading: false,

                page: 0,
                search: '',
                sortedBy: null,
                sortDir: null,
                sortDirs: {},

                reorderActive: false,
                reorderedItems: null,

                containers: null,
                layout: null,
                data: null,
                fields: null,
                config: null,
                authorizations: null,
                forms: null,

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
        },
        computed: {
            classes() {
                return {
                    'SharpEntityList--has-state-only': this.hasStateOnly,
                }
            },
            filters() {
                return this.storeGetter('filters/filters');
            },
            filtersValues() {
                return this.storeGetter('filters/values');
            },
            filterNextQuery() {
                return this.storeGetter('filters/nextQuery');
            },
            getFiltersValuesFromQuery() {
                return this.storeGetter('filters/getValuesFromQuery');
            },
            query() {
                return this.storeGetter('query');
            },
            commandsQuery() {
                const getFiltersQueryParams = this.storeGetter('filters/getQueryParams');
                return {
                    ...getFiltersQueryParams(this.filtersValues),
                    ...this.query,
                }
            },

            hasMultiforms() {
                return !!this.forms;
            },
            hasShowPage() {
                return !!this.config.hasShowPage;
            },
            apiParams() {
                return this.query;
            },
            apiPath() {
                return `list/${this.entityKey}`;
            },

            actionBarProps() {
                if(!this.ready) {
                    return {
                        ready: false,
                    }
                }
                return {
                    ready: true,
                    count: this.totalCount,
                    search: this.search,
                    filters: this.filters,
                    filtersValues: this.filtersValues,
                    forms: this.multiforms,
                    primaryCommand: this.allowedEntityCommands.flat().find(command => command.primary),
                    reorderActive: this.reorderActive,
                    canCreate: this.canCreate,
                    canReorder: this.canReorder,
                    canSearch: this.canSearch,
                }
            },
            actionBarListeners() {
                return {
                    'command': this.handleCommandRequested,
                    'search-submit': this.handleSearchSubmitted,
                    'filter-change': this.handleFilterChanged,
                    'reorder-click': this.handleReorderButtonClicked,
                    'reorder-submit': this.handleReorderSubmitted,
                    'create': this.handleCreateButtonClicked,
                }
            },

            /**
             * Action bar computed data
             */
            allowedEntityCommands() {
                return (this.config.commands.entity || [])
                    .map(group => group.filter(command => this.isEntityCommandAllowed(command)))
            },
            dropdownEntityCommands() {
                return this.allowedEntityCommands
                    .map(group => group.filter(command => !command.primary))
            },
            hasEntityCommands() {
                return this.dropdownEntityCommands.flat().length > 0;
            },
            multiforms() {
                return this.forms ? Object.values(this.forms) : null;
            },
            canCreate() {
                return this.showCreateButton && !!this.authorizations.create;
            },
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
                if(this.reorderActive) {
                    return false;
                }
                return this.items.some(instance =>
                    this.instanceHasState(instance) ||
                    this.instanceHasCommands(instance)
                );
            },
            hasStateOnly() {
                return this.items.some(instance =>
                    !this.instanceHasCommands(instance) &&
                    this.instanceHasState(instance) && !this.instanceHasStateAuthorization(instance)
                );
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
            handleFilterChanged(filter, value) {
                this.storeDispatch('setQuery', {
                    ...this.query,
                    ...this.filterNextQuery({ filter, value }),
                    page: 1,
                });
            },
            handleReorderButtonClicked() {
                this.reorderActive = !this.reorderActive;
                this.reorderedItems = this.reorderActive ? [...this.items] : null;
            },
            handleReorderSubmitted() {
                return this.storeDispatch('reorder', {
                    instances: this.reorderedItems.map(item => this.instanceId(item))
                }).then(() => {
                    this.data.list.items = [...this.reorderedItems];
                    this.reorderedItems = null;
                    this.reorderActive = false;
                });
            },
            handleCreateButtonClicked(multiform) {
                const formUrl = multiform
                    ? this.formUrl({ formKey:multiform.key })
                    : this.formUrl();

                location.href = formUrl;
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
            instanceUrl(instance) {
                const instanceId = this.instanceId(instance);
                if(!this.instanceHasViewAuthorization(instance)) {
                    return null;
                }
                if(this.hasShowPage) {
                    return this.showUrl({ instanceId });
                }
                if(this.hasMultiforms) {
                    const form = this.instanceForm(instance) || {};
                    return this.formUrl({ formKey:form.key, instanceId });
                }
                return this.formUrl({ instanceId });
            },
            instanceHasViewAuthorization(instance) {
                const instanceId = this.instanceId(instance);
                const viewAuthorizations = this.authorizations.view;
                return Array.isArray(viewAuthorizations)
                    ? viewAuthorizations.includes(instanceId)
                    : !!viewAuthorizations;
            },
            instanceIsFocused(instance) {
                const instanceId = this.instanceId(instance);
                return this.focusedItem && this.focusedItem === instanceId;
            },


            filterByKey(key) {
                return (this.config.filters || []).find(filter => filter.key === key);
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
                                title: lang('modals.state.422.title'),
                                isError: true,
                            });
                        }
                    })
            },


            /**
             * [Data list] events
             */
            handleInstanceStateChanged(instance, state) {
                this.setState(instance, state);
            },
            handleInstanceCommandRequested(instance, command) {
                const instanceId = this.instanceId(instance);
                this.handleCommandRequested(command, { instanceId });
            },
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

            /**
             * Helpers
             */
            formUrl({ formKey, instanceId }={}) {
                return formUrl({
                    entityKey: formKey ? `${this.entityKey}:${formKey}` : this.entityKey,
                    instanceId,
                }, { append: true });
            },
            showUrl({ instanceId }={}) {
                return showUrl({
                    entityKey: this.entityKey,
                    instanceId,
                }, { append: true });
            },
            tryParseNumber(val) {
                if(Array.isArray(val)) {
                    return val.map(this.tryParseNumber);
                }
                let n = Number(val);
                return isNaN(Number(n)) ? val : n;
            },
            filterValueOrDefault(val, filter) {
                return val != null && val !== '' ? this.tryParseNumber(val) : (filter.default || (filter.multiple?[]:null));
            },

            /**
             * Dynamic view overrides
             */
            showLoading() {
                if(!this.inline) {
                    this.$store.dispatch('setLoading', true);
                }
            },
            hideLoading() {
                if(!this.inline) {
                    this.$store.dispatch('setLoading', false);
                }
            },

            /**
             * Commands
             */
            initCommands() {
                this.addCommandActionHandlers({
                    'refresh': this.handleRefreshCommand
                });
            },
            handleCommandRequested(command, { instanceId } = {}) {
                const query = this.commandsQuery;
                const endpoint = this.commandEndpoint(command.key, instanceId);

                this.currentCommandInstanceId = instanceId;
                this.sendCommand(command, {
                    postCommand: () => this.axiosInstance.post(endpoint, { query }, { responseType:'blob' }),
                    postForm: data => api.post(endpoint, { query, data }, { responseType:'blob' }),
                    getForm: () => this.axiosInstance.get(`${endpoint}/form`, { params:query }).then(response => response.data),
                });
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
            isEntityCommandAllowed(command) {
                const hiddenCommands = this.hiddenCommands ? this.hiddenCommands.entity : null;
                return !!command.authorization && !(hiddenCommands || []).includes(command.key);
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
            mount({ containers, layout, data, fields, config, authorizations, forms }) {
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
                // legacy
                await this.get()
                    .catch(error => {
                        this.$emit('error', error);
                        return Promise.reject(error);
                    });
                this.bindParams(this.query);

                this.$emit('change', {
                    data: this.data,
                    layout: this.layout,
                    config: this.config,
                    containers: this.containers,
                    authorizations: this.authorizations,
                    forms: this.forms,
                });

                await this.storeDispatch('update', {
                    config: this.config,
                    filtersValues: this.getFiltersValuesFromQuery(this.query),
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
