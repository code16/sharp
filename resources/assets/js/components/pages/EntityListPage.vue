<template>
    <div class="SharpEntityListPage">
        <template v-if="ready">
            <SharpActionBarList
                :count="itemsCount"
                :search="search"
                :filters="filters"
                :filters-values="filtersValues"
                :commands="allowedEntityCommands"
                :forms="multiforms"
                :reorder-active="reorderActive"
                :can-create="canCreate"
                :can-reorder="canReorder"
                :can-search="canSearch"
                @search-change="handleSearchChanged"
                @search-submit="handleSearchSubmitted"
                @filter-change="handleFilterChanged"
                @reorder-click="handleReorderButtonClicked"
                @reorder-submit="handleReorderSubmitted"
                @command="handleEntityCommandRequested"
                @create="handleCreateButtonClicked"
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
                    <SharpDataListRow :url="instanceFormUrl(item)" :columns="columns" :row="item">
                        <template v-if="hasActionsColumn">
                            <template slot="append">
                                <div class="row">
                                    <template v-if="instanceHasState(item)">
                                        <div class="col-auto col-md-12 my-1">
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
                                        <div class="col-auto col-md-12 my-1">
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

    import { BASE_URL } from "../../consts";

    import { mapState, mapGetters } from 'vuex';

    export default {
        name: 'SharpEntityListPage',
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
            '$route': 'init',
        },
        computed: {
            ...mapState('entity-list', {
                entityKey: state => state.entityKey,
            }),
            ...mapGetters('entity-list', {
                filters: 'filters/filters',
                filtersValues: 'filters/values',
                filterNextQuery: 'filters/nextQuery',
                getFiltersValuesFromQuery: 'filters/getValuesFromQuery',
            }),
            hasMultiforms() {
                return !!this.forms;
            },
            apiParams() {
                return this.$route.query;
            },
            apiPath() {
                return `list/${this.entityKey}`;
            },

            /**
             * Action bar computed data
             */
            itemsCount() {
                return (this.data.items || []).length;
            },
            allowedEntityCommands() {
                return (this.config.commands.entity || [])
                    .map(group => group.filter(command => command.authorization))
            },
            multiforms() {
                return this.forms ? Object.values(this.forms) : null;
            },
            canCreate() {
                return !!this.authorizations.create;
            },
            canReorder() {
                return this.config.reorderable
                    && this.authorizations.update
                    && this.data.items.length > 1;
            },
            canSearch() {
                return !!this.config.searchable;
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
                return this.data.totalCount;
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
            /**
             * [Action bar] events
             */
            handleSearchChanged(search) {
                this.search = search;
            },
            handleSearchSubmitted() {
                this.$router.push({ query: { ...this.$route.query, search:this.search } });
            },
            handleFilterChanged(filter, value) {
                this.$router.push({
                    query: {
                        ...this.$route.query,
                        ...this.filterNextQuery({ filter, value }),
                    }
                });
            },
            handleReorderButtonClicked() {
                this.reorderActive = !this.reorderActive;
                this.reorderedItems = this.reorderActive ? [ ...this.data.items ] : null;
            },
            handleReorderSubmitted() {
                return this.$store.dispatch('entity-list/reorder', {
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
                const allInstanceCommands = this.config.commands.instance || [];
                const instanceId = this.instanceId(instance);

                return allInstanceCommands.reduce((res, group) => [
                    ...res, group.filter(command => command.authorization.includes(instanceId))
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
            instanceFormUrl(instance) {
                const instanceId = this.instanceId(instance);
                if(!this.instanceHasViewAuthorization(instance)) {
                    return null;
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
                this.axiosInstance.post(`${this.apiPath}/state/${instanceId}`, {
                    attribute: this.config.state.attribute,
                    value: state
                })
                .then(({ data:{ action, items } })=>{
                    if(action === 'refresh') this.actionRefresh(items);
                    else if(action === 'reload') this.actionReload();
                })
                .catch(({response:{data, status}}) => {
                    if(status === 422) {
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
                this.$router.push({
                    query: {
                        ...this.$router.query,
                        page: 1,
                        sort: prop,
                        dir: dir,
                    }
                });
            },
            handleReorderedItemsChanged(items) {
                this.reorderedItems = items;
            },
            handlePageChanged(page) {
                this.$router.push({
                    query: {
                        ...this.$router.query,
                        page
                    }
                });
            },

            /**
             * Helpers
             */
            formUrl({ formKey, instanceId }={}) {
                return `${BASE_URL}/form/${this.entityKey}${formKey?`:${formKey}`:''}${instanceId?`/${instanceId}`:''}`
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
                const query = this.$route.query;

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
                await this.$store.dispatch('entity-list/setEntityKey', this.$route.params.id);
                // legacy
                await this.get();
                this.bindParams(this.$route.query);

                await this.$store.dispatch('entity-list/update', {
                    config: this.config,
                    filtersValues: this.getFiltersValuesFromQuery(this.$route.query)
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