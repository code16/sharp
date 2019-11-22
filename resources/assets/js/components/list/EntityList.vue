<template>
    <div class="SharpEntityList">
        <template v-if="ready">
            <slot
                name="action-bar"
                :props="actionBarProps"
                :listeners="actionBarListeners"
            />

            <SharpDataList
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
                <template slot="empty">
                    {{ l('entity_list.empty_text') }}
                </template>
                <template slot="item" slot-scope="{ item }">
                    <SharpDataListRow :url="instanceUrl(item)" :columns="columns" :row="item">
                        <template v-if="hasActionsColumn">
                            <template slot="append">
                                <div class="row justify-content-end justify-content-md-start mx-n2">
                                    <template v-if="instanceHasState(item)">
                                        <div class="col-auto col-md-12 my-1 px-2">
                                            <SharpDropdown class="SharpEntityList__state-dropdown" :disabled="!instanceHasStateAuthorization(item)">
                                                <template slot="text">
                                                    <SharpStateIcon :color="instanceStateIconColor(item)" />
                                                    <span class="text-truncate">
                                                        {{ instanceStateLabel(item) }}
                                                    </span>
                                                </template>
                                                <SharpDropdownItem
                                                    v-for="stateOptions in config.state.values"
                                                    @click="handleInstanceStateChanged(item, stateOptions.value)"
                                                    :key="stateOptions.value"
                                                >
                                                    <SharpStateIcon :color="stateOptions.color" />&nbsp;
                                                    {{ stateOptions.label }}
                                                </SharpDropdownItem>
                                            </SharpDropdown>
                                        </div>
                                    </template>
                                    <template v-if="instanceHasCommands(item)">
                                        <div class="col-auto col-md-12 my-1 px-2">
                                            <SharpCommandsDropdown
                                                class="SharpEntityList__commands-dropdown"
                                                :commands="instanceCommands(item)"
                                                @select="handleInstanceCommandRequested(item, $event)"
                                            >
                                                <template slot="text">
                                                    {{ l('entity_list.commands.instance.label') }}
                                                </template>
                                            </SharpCommandsDropdown>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </template>
                    </SharpDataListRow>
                </template>
            </SharpDataList>
        </template>

        <SharpCommandFormModal :form="commandCurrentForm" ref="commandForm" />
        <SharpCommandViewPanel :content="commandViewContent" @close="handleCommandViewPanelClosed" />
    </div>
</template>

<script>
    import SharpActionBarList from '../action-bar/ActionBarList.vue';
    import SharpDataList from '../list/DataList.vue';
    import SharpDataListRow from '../list/DataListRow.vue';
    import SharpStateIcon from '../list/StateIcon.vue';
    import SharpCommandsDropdown from '../commands/CommandsDropdown.vue';
    import SharpCommandFormModal from '../commands/CommandFormModal.vue';
    import SharpCommandViewPanel from '../commands/CommandViewPanel.vue';

    import { SharpDropdown, SharpDropdownItem } from "../ui";

    import { Localization } from '../../mixins';
    import DynamicViewMixin from '../DynamicViewMixin';
    import withCommands from '../../mixins/page/with-commands';

    import { formUrl, showUrl } from "../../util/url";

    export default {
        name: 'SharpEntityList',
        mixins: [DynamicViewMixin, Localization, withCommands],
        components: {
            SharpActionBarList,
            SharpDataList,
            SharpDataListRow,

            SharpStateIcon,
            SharpCommandsDropdown,

            SharpDropdown,
            SharpDropdownItem,

            SharpCommandFormModal,
            SharpCommandViewPanel,
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
            hiddenCommands: Object,
        },
        data() {
            return {
                ready: false,

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
                config: null,
                authorizations: null,
                forms: null,
            }
        },
        watch: {
            query: 'init',
        },
        computed: {
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
                return this.storeGetter('filters/getValuesFromQuery')
            },
            query() {
                return this.storeGetter('query');
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
                return {
                    count: this.totalCount,
                    search: this.search,
                    filters: this.filters,
                    filtersValues: this.filtersValues,
                    commands: this.allowedEntityCommands,
                    forms: this.multiforms,
                    reorderActive: this.reorderActive,
                    canCreate: this.canCreate,
                    canReorder: this.canReorder,
                    canSearch: this.canSearch,
                }
            },
            actionBarListeners() {
                return {
                    'search-change': this.handleSearchChanged,
                    'search-submit': this.handleSearchSubmitted,
                    'filter-change': this.handleFilterChanged,
                    'reorder-click': this.handleReorderButtonClicked,
                    'reorder-submit': this.handleReorderSubmitted,
                    'command': this.handleEntityCommandRequested,
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
                    && this.data.items.length > 1;
            },
            canSearch() {
                return this.showSearchField && !!this.config.searchable;
            },

            /**
             * Data list props
             */
            items() {
                return this.data.items || [];
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
                return this.data.totalCount || this.items.length;
            },
            pageSize() {
                return this.data.pageSize;
            },

            hasActionsColumn() {
                return this.items.some(instance =>
                    this.instanceHasState(instance) ||
                    this.instanceHasCommands(instance)
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
            handleSearchChanged(search) {
                this.search = search;
            },
            handleSearchSubmitted() {
                this.storeDispatch('setQuery', {
                    ...this.query,
                    search: this.search,
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
                this.reorderedItems = this.reorderActive ? [ ...this.data.items ] : null;
            },
            handleReorderSubmitted() {
                return this.storeDispatch('reorder', {
                    instances: this.reorderedItems.map(item => this.instanceId(item))
                }).then(() => {
                    this.$set(this.data, 'items', [ ...this.reorderedItems ]);
                    this.reorderedItems = null;
                    this.reorderActive = false;
                });
            },
            handleEntityCommandRequested(command) {
                this.handleCommandRequested(command, {
                    endpoint: this.commandEndpoint(command.key),
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
                return !!this.config.state;
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
            instanceStateIconColor(instance) {
                const state = this.instanceState(instance);
                const stateOptions = this.instanceStateOptions(state);
                return stateOptions.color;
            },
            instanceStateLabel(instance) {
                const state = this.instanceState(instance);
                const stateOptions = this.instanceStateOptions(state);
                return stateOptions.label;
            },
            instanceStateOptions(instanceState) {
                if(!this.config.state) {
                    return null;
                }
                return this.config.state.values.find(stateValue => stateValue.value === instanceState);
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
                        const { data } = error.response;
                        if(error.response.status === 422) {
                            this.actionsBus.$emit('showMainModal', {
                                title: this.l('modals.state.422.title'),
                                text: data.message,
                                isError: true,
                                okCloseOnly: true
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
                this.handleCommandRequested(command, {
                    endpoint: this.commandEndpoint(command.key, instanceId),
                });
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
                    instanceId
                });
            },
            showUrl({ instanceId }={}) {
                return showUrl({
                    entityKey: this.entityKey,
                    instanceId,
                });
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
             * Commands
             */
            initCommands() {
                this.addCommandActionHandlers({
                    'refresh': this.handleRefreshCommand
                });
            },
            handleCommandRequested(command, { endpoint }) {
                const query = this.query;

                this.sendCommand(command, {
                    postCommand: () => this.axiosInstance.post(endpoint, { query }, { responseType:'blob' }),
                    postForm: data => this.axiosInstance.post(endpoint, { query, data }, { responseType:'blob' }),
                    getFormData: () => this.axiosInstance.get(`${endpoint}/data`, { params:query }).then(response => response.data.data),
                });
            },
            handleRefreshCommand(data) {
                const findInstance = (list, instance) => list.find(item => this.instanceId(instance) === this.instanceId(item));
                this.data.items = this.data.items.map(item =>
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
            mount({ containers, layout, data={}, config={}, authorizations, forms }) {
                this.containers = containers;
                this.layout = layout;
                this.data = data;
                this.config = config;
                this.authorizations = authorizations;
                this.forms = forms;

                this.config.commands = config.commands || {};
                this.config.filters = config.filters || [];

                this.page = this.data.page;
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
                await this.storeDispatch('setEntityKey', this.entityKey);
                // legacy
                await this.get();
                this.bindParams(this.query);

                await this.storeDispatch('update', {
                    config: this.config,
                    filtersValues: this.getFiltersValuesFromQuery(this.query),
                });
                this.ready = true;
            },
        },
        beforeMount() {
            this.init();
            this.initCommands();
        },
    }
</script>