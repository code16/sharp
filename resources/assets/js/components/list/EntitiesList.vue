<template>
    <div class="SharpEntitiesList">
        <template v-if="ready">
            <div class="SharpEntitiesList__table SharpEntitiesList__table--border">
                <div class="SharpEntitiesList__thead">
                    <div class="SharpEntitiesList__row SharpEntitiesList__row--header row">
                        <div class="SharpEntitiesList__th" :class="colClasses(contLayout)" v-for="contLayout in layout">
                            <span>{{ containers[contLayout.key].label }}</span>
                            <template v-if="containers[contLayout.key].sortable">
                                <svg class="SharpEntitiesList__carret"
                                     :class="{'SharpEntitiesList__carret--selected':sortedBy === contLayout.key,
                                              'SharpEntitiesList__carret--ascending':sortedBy === contLayout.key && sortDir==='asc'}"
                                     width="10" height="5" viewBox="0 0 10 5" fill-rule="evenodd" @click="sortToggle(contLayout.key)">
                                    <path d="M10 0L5 5 0 0z"></path>
                                </svg>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="SharpEntitiesList__tbody">

                    <div v-for="item in data.items" class="SharpEntitiesList__row" :class="{'SharpEntitiesList__row--disabled':!rowHasLink(item)}">
                        <div class="SharpEntitiesList__cols">
                            <div class="row">
                                <div class="SharpEntitiesList__td" :class="colClasses(contLayout)" v-for="contLayout in layout">
                                    <span v-if="containers[contLayout.key].html" v-html="item[contLayout.key]" class="SharpEntitiesList__td-html-container"></span>
                                    <template v-else>
                                        {{ item[contLayout.key] }}
                                    </template>
                                </div>
                            </div>
                            <a class="SharpEntitiesList__row-link" v-if="rowHasLink(item)" :href="rowLink(item)"></a>
                        </div>
                        <div class="SharpEntitiesList__row-actions">
                            <sharp-dropdown v-if="config.state" class="SharpEntitiesList__state-dropdown" :show-arrow="false" :disabled="!hasStateAuthorization(item)">
                                <sharp-state-icon slot="text" :class="stateClasses(item.state)" :style="stateStyle(item.state)"></sharp-state-icon>
                                <sharp-dropdown-item v-for="state in config.state.values" @click="setState(item,state)" :key="state.value">
                                    <sharp-state-icon :class="stateClasses(state.value)" :style="stateStyle(state.value)"></sharp-state-icon>
                                    {{ state.label }}
                                </sharp-dropdown-item>
                            </sharp-dropdown>
                            <sharp-dropdown v-if="instanceCommands(item).length" class="SharpEntitiesList__commands-dropdown" :show-arrow="false">
                                <div slot="text" class="SharpEntitiesList__command-icon">
                                    <i class="fa fa-plus"></i>
                                </div>
                                <sharp-dropdown-item v-for="command in instanceCommands(item)" @click="sendCommand(command, item)" :key="command.key">
                                    {{ command.label }}
                                </sharp-dropdown-item>
                            </sharp-dropdown>
                        </div>

                    </div>
                </div>
            </div>
            <div class="SharpEntitiesList__pagination-container">
                <sharp-pagination v-if="data.totalCount/data.pageSize > 1."
                                  class="SharpPagination"
                                  :total-rows="data.totalCount"
                                  :per-page="data.pageSize"
                                  :min-page-end-buttons="3"
                                  :limit="7"
                                  :value="page"
                                  @change="pageChanged">
                </sharp-pagination>
            </div>
            <sharp-modal v-for="form in commandForms"
                         :visible="showFormModal[form.key]"
                         :key="form.key"
                         @ok="postCommandForm(form.key, $event)"
                         @hidden="hideCommandModal(form.key)">
                <sharp-form :props="form"
                            :entity-key="form.key"
                            independant
                            ignore-authorizations
                            @submitted="commandFormSubmitted(form.key, $event)">
                </sharp-form>
            </sharp-modal>
            <sharp-view-panel v-model="showViewPanel" :content="viewPanelContent"></sharp-view-panel>
        </template>
    </div>
</template>

<script>
    import DynamicView from '../DynamicViewMixin';
    import Pagination from './Pagination';
    import Dropdown from '../dropdown/Dropdown';
    import DropdownItem from '../dropdown/DropdownItem';
    import Modal from '../Modal';
    import Form from '../form/Form';
    import ViewPanel from './ViewPanel';
    import StateIcon from './StateIcon';

    import { API_PATH } from '../../consts';
    import * as util from '../../util';

    import { ActionEvents, Localization } from '../../mixins';

    import * as qs from '../../helpers/querystring';

    import axios from 'axios';

    export default {
        name:'SharpEntitiesList',
        extends: DynamicView,

        inject: [
            'actionsBus',
            'params' // querystring params as an object
        ],

        mixins: [ ActionEvents, Localization ],

        components: {
            [Pagination.name]: Pagination,
            [Dropdown.name]: Dropdown,
            [DropdownItem.name]: DropdownItem,
            [Modal.name]: Modal,
            [Form.name]: Form,
            [ViewPanel.name]: ViewPanel,
            [StateIcon.name]: StateIcon,
        },

        props: {
            entityKey: {
                type: String,
                required: true
            }
        },

        data() {
            return {
                containers: null,
                config: null,
                authorizations: null,

                page: 0,
                search: '',
                sortedBy: null,
                sortDir: null,

                filtersValue: {},
                showFormModal: {},
                selectedInstance: null,
                showViewPanel: false,
                viewPanelContent: null
            }
        },
        computed: {
            apiPath() {
                return `${API_PATH}/list/${this.entityKey}`;
            },
            apiParams() {
                if(!this.ready) {
                    return {
                        ...this.params
                    }
                }

                let params = {};
                if(this.config.paginated) params.page = this.page;
                if(this.config.searchable) params.search = (this.search||'');
                if(this.sortedBy) {
                    params.sort = this.sortedBy;
                    params.dir = this.sortDir;
                }
                params = {
                    ...params,
                    ...this.filterParams
                };
                return params;
            },
            filterParams() {
                return Object.keys(this.filtersValue).reduce((res, filterKey)=>{
                    if(this.filtersValue[filterKey] != null)
                        res[`filter_${filterKey}`] = this.filtersValue[filterKey];
                    return res;
                },{});
            },
            idAttr() {
                return this.config.instanceIdAttribute;
            },

            //// Getters
            filterByKey() {
                return this.config.filters.reduce((res, filter)=>{
                    res[filter.key] = filter ;
                    return res;
                },{});
            },
            stateByValue() {
                if(!this.config.state)
                    return null;
                return this.config.state.values.reduce((res, stateData) => {
                    res[stateData.value] = stateData;
                    return res;
                }, {})
            },
            indexByInstanceId() {
                return this.data.items.reduce((res, {[this.idAttr]:id}, index) => {
                    res[id] = index;
                    return res;
                }, {});
            },
            authorizationsByInstanceId() {

                return this.data.items.reduce((res, {[this.idAttr]:id}) => {
                    res[id] = {
                        view: this.getAuthorizations({ type:'view', id }),
                        update: this.getAuthorizations({ type:'update', id })
                    };
                    return res;
                }, {});
            },
            commandsByInstanceId() {
                let instCmds = this.config.commands.filter(c=>c.type==='instance');
                return this.data.items.reduce((res, {[this.idAttr]:id}) => {
                    res[id] = instCmds.filter(c=>c.authorization.indexOf(id) !== -1);
                    return res;
                }, {});
            },
            commandForms() {
                return this.config.commands.filter(({form})=>form).map(({form, key}) => ({
                    ...form, key,
                    layout: { tabs: [{ columns: [{fields:form.layout}]}] },
                }));
            }
        },
        methods: {
            /**
             * Initialization
             */
            mount({ containers, layout, data={}, config={}, authorizations }) {
                this.containers = containers;
                this.layout = layout;
                this.data = data;
                this.config = config;
                this.authorizations = authorizations;

                this.config.commands = config.commands || [];
                this.config.filters = config.filters || [];

                this.page = this.data.page;
                !this.sortDir && (this.sortDir = this.config.defaultSortDir);
                !this.sortedBy && (this.sortedBy = this.config.defaultSort);


                this.filtersValue = this.config.filters.reduce((res, filter) => {
                    res[filter.key] = this.filterValueOrDefault(this.filtersValue[filter.key], filter);
                    return res;
                }, {});

                this.ready = true;
                history.replaceState(this.apiParams, null);
            },
            verify() {
                for(let contLayout of this.layout) {
                    if(!(contLayout.key in this.containers)) {
                        util.error(`EntitiesList: unknown container "${contLayout.key}" (in layout)`);
                        this.ready = false;
                    }
                }
            },
            setupActionBar() {
                this.actionsBus.$emit('setup', {
                    itemsCount: this.data.totalCount || this.data.items.length,
                    filters: this.config.filters,
                    filtersValue: this.filtersValue,
                    commands: this.config.commands.filter(c=>c.authorization && c.type==='entity'),
                    showCreateButton:this.authorizations.create
                });
            },

            /**
             * Getters
             */
            colClasses({ sizeXS, size, hideOnXS}) {
                return [
                    `col-${sizeXS}`,
                    `col-sm-${size}`,
                    {'hidden-xs-down':hideOnXS}
                ]
            },
            isStateClass(color) {
                return color.indexOf('sharp_') === 0;
            },
            stateClasses(state) {
                let { color } = this.stateByValue[state];
                return this.isStateClass(color) ? [color] : [];
            },
            stateStyle(state) {
                let { color } = this.stateByValue[state];
                return !this.isStateClass(color) ? {
                    fill: color,
                    stroke: color,
                } : '';
            },
            hasStateAuthorization({[this.idAttr]:instanceId}) {
                if(!this.config.state) return false;

                let { authorization:auth } = this.config.state;
                return Array.isArray(auth) ? auth.indexOf(instanceId) !== -1 : auth;
            },
            filterValueOrDefault(val, filter) {
                return val || filter.default || (filter.multiple?[]:null);
            },
            instanceCommands({[this.idAttr]:instanceId}) {
                return this.commandsByInstanceId[instanceId];
            },
            rowHasLink({[this.idAttr]:instanceId}) {
                return this.authorizationsByInstanceId[instanceId].view;
            },
            rowLink({[this.idAttr]:instanceId}) {
                return `/sharp/form/${this.entityKey}/${instanceId}`;
            },
            getAuthorizations({ type, id }) {
                return typeof this.authorizations[type] === 'boolean'
                    ? this.authorizations[type]
                    : this.authorizations[type].indexOf(id) !== -1;
            },

            /**
             * Events
             */
            pageChanged(page) {
                this.page = page;
                this.update({ resetPage: false });
            },
            /**
             * Data operations
             */
            sortToggle(contKey) {
                if(this.sortedBy === contKey)
                    this.sortDir = this.sortDir==='asc'? 'desc': 'asc';
                this.sortedBy = contKey;

                this.page = 1;
                this.update();
            },
            /* (Instance, State) */
            setState({ [this.idAttr]:instanceId }, { value }) {
                axios.post(`${this.apiPath}/state/${instanceId}`, {
                        attribute: this.config.state.attribute,
                        value
                    })
                    .then(({data:{ action, items }})=>{
                        if(action === 'refresh') this.actionRefresh(items);
                        else if(action === 'reload') this.actionReload();
                    })
                    .catch(({error:{response:{data, status}}}) => {
                        if(status === 417) {
                            alert(data.message);
                        }
                        else if(status === 422) {
                            this.actionsBus.$emit('showMainModal', {
                                title: this.l('modals.state.422.title'),
                                text: data.message,
                                isError: true,
                                okCloseOnly: true
                            });
                        }
                    })
            },
            update({ resetPage=true }={}) {
                if(resetPage && this.page>1)
                    this.page=1;
                this.updateData();
                this.updateHistory();
            },
            updateData() {
                this.get().then(({data:{ data }})=>{
                    this.data = data;
                    this.setupActionBar();
                });
            },

            /**
             * Commands
             */
            commandEnpoint(key, { [this.idAttr]:instanceId }={}) {
                return `${this.apiPath}/command/${key}${instanceId?`/${instanceId}`:''}`;
            },

            /* (Command, Instance)
             * Display a form in a modal if the command require a form, else send API request
             */
            sendCommand({ key, form, confirmation }, instance) {
                if(form) {
                    this.selectedInstance = instance;
                    this.$set(this.showFormModal,key,true);
                    return;
                }
                axios.post(this.commandEnpoint(key, instance), {query: this.apiParams})
                    .then(({data})=> {
                        if (confirmation) {
                            return new Promise((resolve) => {
                                this.actionsBus.$emit('showMainModal', {
                                    title: this.l('modals.command.confirm.title'),
                                    text: confirmation,
                                    closeTitle: 'Cancel',
                                    okCallback: e => resolve(data),
                                });
                            })
                        }
                        return Promise.resolve(data);
                    })
                    .then(this.handleCommandResponse);
            },

            /* (CommandAPIResponse)
            * Execute the required command action
            */
            handleCommandResponse({action, items, message, html}) {
                if(action === 'refresh') this.actionRefresh(items);
                else if(action === 'reload') this.actionReload();
                else if(action === 'info') {
                    this.actionsBus.$emit('showMainModal', {
                        title: this.l('modals.command.info.title'),
                        text: message,
                        okCloseOnly: true
                    });
                }
                else if(action === 'view') {
                    this.showViewPanel = true;
                    this.viewPanelContent = html;
                }
            },

            /* (CommandKey, BHideModalEvent)
            * Execute form submission
            */
            postCommandForm(key, event) {
                this.actionsBus.$emit('submit', {
                    entityKey: key,
                    endpoint: this.commandEnpoint(key, this.selectedInstance),
                    dataFormatter:form=>({ query:this.apiParams, data:form.data })
                });
                return event.cancel();
            },

            /* (CommandKey, FormData)
            * Hide the current form modal after data correctly sent, handle actions
            */
            commandFormSubmitted(key, data) {
                this.selectedInstance = null;
                this.hideCommandModal(key);
                this.handleCommandResponse(data);
            },

            hideCommandModal(key) {
                this.actionsBus.$emit('reset', { entityKey: key });
                this.$set(this.showFormModal,key, false);
            },

            /**
             * Actions
             */
            actionReload() {
                this.updateData();
            },
            actionRefresh(items) {
                items.forEach(item => this.$set(this.data.items,this.indexByInstanceId[item[this.idAttr]],item))
            },

            /**
             * History & URL
             */
            updateHistory() {
                history.pushState(this.apiParams, null, qs.serialize(this.apiParams));
            },
            bindParams(params = this.params) {
                let { search, page, sort, dir, ...dynamicParams } = params;
                this.actionsBus.$emit('searchChanged', search, { isInput: false });

                page && (this.page = parseInt(page));
                sort && (this.sortedBy = sort);
                dir && (this.sortDir = dir);

                for(let paramKey of Object.keys(dynamicParams)) {
                    let paramValue = dynamicParams[paramKey];
                    if(paramKey.indexOf('filter_') === 0) {
                        let [ _, filterKey ] = paramKey.split('_');
                        if((this.filterByKey[filterKey]||{}).multiple && paramValue && !Array.isArray(paramValue)) {
                            paramValue = [paramValue];
                        }
                        this.filtersValue[filterKey] = this.filterValueOrDefault(paramValue,this.filterByKey[filterKey]);
                    }
                }
            },
        },
        actions: {
            searchChanged(input, {isInput=true}={}) {
                //console.log('entities list search changed', input, isInput);

                this.search = input;
                if(isInput) {
                    this.update();
                }
            },
            filterChanged(key, value) {
                this.filtersValue[key] = value;
                this.update();
            },
            command: 'sendCommand',
            create() {
                location.href=`/sharp/form/${this.entityKey}`;
            }
        },
        created() {
            this.get().then(_=>{
                this.verify();
                this.bindParams();
                this.setupActionBar();
            });

            window.onpopstate = event => {
                this.bindParams(event.state);
                this.updateData();
            };
        }
    }
</script>