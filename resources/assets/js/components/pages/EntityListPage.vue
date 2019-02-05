<template>
    <div>
        <SharpActionBarList
            :results-count="resultsCount"
            :search="search"
            :filters="filters"
            :filters-value="filtersValue"
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

            @change="handleReorderedItemsChanged"
            @sort-change="handleSortChanged"
        >
            <template slot="empty">
                {{ l('entity_list.empty_text') }}
            </template>
            <template slot="row-extra" slot-scope="{ item }">
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
                                    {{ state.label }}
                                </SharpDropdownItem>
                            </SharpDropdown>
                        </div>
                    </template>
                    <template v-if="instanceHasCommands(item)">
                        <div class="col-auto col-md-12 pl-2 pl-md-0 my-1">
                            <SharpCommandsDropdown
                                class="SharpEntityList__commands-dropdown"
                                :commands="instanceCommands(item)"
                                @select="handleInstanceCommandRequested($event, item)"
                            >
                                <template slot="text">
                                    {{ l('entity_list.commands.instance.label') }}
                                </template>
                            </SharpCommandsDropdown>
                        </div>
                    </template>
                </div>
            </template>
        </SharpDataList>
    </div>
</template>

<script>
    import SharpActionBarList from '../action-bar/ActionBarList.vue';
    import SharpDataList from '../list/DataList.vue';
    import SharpStateIcon from '../list/StateIcon.vue';
    import SharpCommandsDropdown from '../list/CommandsDropdown.vue';
    import { SharpDropdown, SharpDropdownItem } from "../ui";

    import { Localization } from '../../mixins';
    import DynamicViewMixin from '../DynamicViewMixin';

    import { BASE_URL } from "../../consts";

    export default {
        mixins: [DynamicViewMixin, Localization],
        components: {
            SharpActionBarList,
            SharpDataList,

            SharpStateIcon,
            SharpCommandsDropdown,

            SharpDropdown,
            SharpDropdownItem,
        },
        data() {
            return {
                filtersValue: null,

                page: 0,
                search: '',
                sortedBy: null,
                sortDir: null,
                sortDirs: {},

                reorderActive: false,
                reorderedItems: null,
            }
        },
        watch: {
            '$route': 'init',
        },
        computed: {
            entityKey() {
                return this.$route.params.id;
            },

            /**
             * Action bar computed data
             */
            resultsCount() {
                return (this.data.items || []).length;
            },
            filters() {
                return this.config.filters;
            },
            allowedEntityCommands() {
                return (this.config.commands.entity || [])
                    .map(group => group.filter(command => command.authorization))
            },
            multiforms() {
                return this.config.forms;
            },
            canCreate() {
                return this.authorizations.create;
            },
            canReorder() {
                return this.config.reorderable
                    && this.authorizations.update
                    && this.data.items.length > 1;
            },
            canSearch() {
                return this.config.searchable;
            },

            /**
             * Data list props
             */
            columns() {
                return this.layout.map(columnLayout => ({
                    ...columnLayout,
                    ...this.containers[columnLayout.key]
                }));
            },
            paginated() {
                return this.config.paginated;
            },
            totalCount() {
                return this.data.totalCount;
            },
            pageSize() {
                return this.data.pageSize;
            }
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
            handleFilterChanged() {
                this.$router.push({ query: { ...this.$route.query, ...this.filterParams }});
            },
            handleReorderButtonClicked() {
                this.reorderActive = !this.reorderActive;
                if(this.reorderActive) {
                    this.reorderedItems = [ ...this.data.items ];
                }
            },
            handleReorderSubmitted() {
                this.axiosInstance.post(`${this.apiPath}/reorder`, {
                    instances: this.reorderedItems.map(item => item[this.idAttr])
                }).then(() => {
                    this.$set(this.data, 'items', [ ...this.reorderedItems ]);
                    this.reorderedItems = null;
                    this.reorderActive = false;
                });
            },
            handleEntityCommandRequested(command) {
                this.sendCommand(command);
            },
            handleCreateButtonClicked(multiform) {
                const formUrl = multiform
                    ? this.formUrl(multiform.key)
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

            filterByKey(key) {
                return this.config.filters.find(filter => filter.key === key);
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
            async sendCommand({ key, form, confirmation, fetch_initial_data }, instance) {
                if(form) {
                    this.selectedInstance = instance;
                    this.currentFormData = fetch_initial_data ? await this.getCommandFormData(key, instance) : {};
                    this.$set(this.showFormModal,key,true);
                    return;
                }
                if(confirmation) {
                    await new Promise(resolve => {
                        this.actionsBus.$emit('showMainModal', {
                            title: this.l('modals.command.confirm.title'),
                            text: confirmation,
                            okCallback: resolve,
                        });
                    });
                }
                try {
                    let endpoint = this.commandEndpoint(key, instance);
                    let response = await this.axiosInstance.post(endpoint, { query: this.apiParams }, { responseType: 'blob' });
                    await this.handleCommandResponse(response);
                } catch(e) {
                    console.error(e);
                }
            },


            /**
             * [Data list] events
             */
            handleInstanceStateChanged(instance, state) {
                this.setState(instance, state);
            },
            handleInstanceCommandRequested(instance, command) {
                this.sendCommand(instance, command);
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

            /**
             * Helpers
             */
            formUrl(multiformKey) {
                return `${BASE_URL}/form/${this.entityKey}${multiformKey ? `:${multiformKey}` : ''}`
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

                this.filtersValue = this.config.filters.reduce((res, filter) => {
                    res[filter.key] = this.filterValueOrDefault(this.filtersValue[filter.key], filter);
                    return res;
                }, {});
            },
            bindParams(params) {
                let { search, page, sort, dir, ...dynamicParams } = params;
                this.actionsBus.$emit('searchChanged', search, { isInput: false });

                page && (this.page = parseInt(page));
                sort && (this.sortedBy = sort);
                dir && (this.sortDir = dir);

                for(let paramKey of Object.keys(dynamicParams)) {
                    let paramValue = dynamicParams[paramKey];
                    if(paramKey.startsWith('filter_')) {
                        const filterKey = paramKey.replace('filter_', '');
                        const filter = this.filterByKey(filterKey);

                        if((filter || {}).multiple && paramValue && !Array.isArray(paramValue)) {
                            paramValue = [paramValue];
                        }
                        this.filtersValue[filterKey] = this.filterValueOrDefault(paramValue, filter);
                    }
                }
            },
            init() {
                this.get().then(() => {
                    this.bindParams(this.$route.query);
                });
            },
        },
        created() {
            this.init();
        }
    }
</script>