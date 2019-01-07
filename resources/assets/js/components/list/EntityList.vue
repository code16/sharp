<template>
    <div class="SharpEntityList" :class="{ 'SharpEntityList--reorder': reorderActive }">
        <template v-if="ready">
            <template v-if="!data.items.length">
                {{ l('entity_list.empty_text') }}
            </template>
            <div v-else="" class="SharpEntityList__table SharpEntityList__table--border">
                <div class="SharpEntityList__thead">
                    <div class="SharpEntityList__row SharpEntityList__row--header container">
                        <div class="SharpEntityList__cols">
                            <div class="row">
                                <div class="SharpEntityList__th"
                                     :class="colClasses(contLayout)"
                                     v-for="contLayout in layout">
                                    <span>{{ containers[contLayout.key].label }}</span>
                                    <template v-if="containers[contLayout.key].sortable">
                                        <svg class="SharpEntityList__carret"
                                             :class="{'SharpEntityList__carret--selected':sortedBy === contLayout.key,
                                              'SharpEntityList__carret--ascending': sortedBy === contLayout.key && sortDir==='asc'}"
                                             width="10" height="5" viewBox="0 0 10 5" fill-rule="evenodd">
                                            <path d="M10 0L5 5 0 0z"></path>
                                        </svg>
                                    </template>
                                    <a class="SharpEntityList__sort-link" v-if="containers[contLayout.key].sortable" @click.prevent="sortToggle(contLayout.key)" href=""></a>
                                </div>
                            </div>
                        </div>
                        <div class="d-none d-md-block" :style="headerAutoPadding">&nbsp;</div>
                    </div>
                </div>
                <div class="SharpEntityList__tbody">
                    <draggable :options="dragOptions" :list="reorderedItems">
                        <div v-for="item in (reorderActive ? reorderedItems : data.items)"
                             class="SharpEntityList__row container"
                             :class="{'SharpEntityList__row--disabled':!rowHasLink(item), 'SharpEntityList__row--reorder':reorderActive}">
                            <div class="SharpEntityList__cols">
                                <div class="row">
                                    <div class="SharpEntityList__td" :class="colClasses(contLayout)" v-for="contLayout in layout">
                                        <div v-if="containers[contLayout.key].html" v-html="item[contLayout.key]"
                                             class="SharpEntityList__td-html-container">
                                        </div>
                                        <template v-else>
                                            {{ item[contLayout.key] }}
                                        </template>
                                    </div>
                                </div>
                                <a class="SharpEntityList__row-link" v-if="rowHasLink(item)" :href="rowLink(item)"></a>
                            </div>
                            <div v-show="!reorderActive" class="SharpEntityList__row-actions" ref="actionsCol">
                                <sharp-dropdown v-if="config.state" class="SharpEntityList__state-dropdown" :disabled="!hasStateAuthorization(item)">
                                    <template slot="text"><sharp-state-icon :class="stateClasses({item})" :style="stateStyle({item})" />
                                        <span class="text-truncate">
                                            {{ stateLabel(item) }}
                                        </span>
                                    </template>
                                    <sharp-dropdown-item v-for="state in config.state.values" @click="setState(item,state)" :key="state.value">
                                        <sharp-state-icon :class="stateClasses({ value:state.value })" :style="stateStyle({ value:state.value })" />&nbsp;
                                        {{ state.label }}
                                    </sharp-dropdown-item>
                                </sharp-dropdown>
                                <sharp-dropdown v-if="!noInstanceCommands"
                                                class="SharpEntityList__commands-dropdown mt-2"
                                                :class="{'SharpEntityList__commands-dropdown--placeholder':!instanceCommands(item)}">
                                    <template slot="text">
                                        <div class="text-left">
                                            <small>ACTIONS</small>
                                        </div>
                                    </template>
                                    <sharp-dropdown-item v-for="command in instanceCommands(item)" @click="sendCommand(command, item)" :key="command.key">
                                        {{ command.label }}
                                    </sharp-dropdown-item>
                                </sharp-dropdown>
                            </div>
                            <div v-show="reorderActive" class="SharpEntityList__row-actions" ref="actionsCol">
                                <i class="fa fa-ellipsis-v SharpEntityList__reorder-icon"></i>
                            </div>
                        </div>
                    </draggable>
                </div>
            </div>
            <div class="SharpEntityList__pagination-container">
                <sharp-pagination v-if="data.totalCount/data.pageSize > 1. && config.paginated"
                                  class="SharpPagination"
                                  :total-rows="data.totalCount"
                                  :per-page="data.pageSize"
                                  :min-page-end-buttons="3"
                                  :limit="7"
                                  :value="page"
                                  @change="pageChanged">
                </sharp-pagination>
            </div>
            <sharp-modal
                v-for="form in commandForms"
                :visible.sync="showFormModal[form.key]"
                @ok="postCommandForm(form.key, $event)"
                :key="form.key"
            >
                <transition>
                    <sharp-form
                        v-if="showFormModal[form.key]"
                        :props="formInitialProps(form)"
                        :entity-key="form.key"
                        independant
                        ignore-authorizations
                        style="transition-duration: 300ms"
                        @submitted="commandFormSubmitted(form.key, $event)"
                    />
                </transition>
            </sharp-modal>
            <sharp-view-panel v-model="showViewPanel" :content="viewPanelContent"></sharp-view-panel>
        </template>
    </div>
</template>

<script>
    import DynamicView from '../DynamicViewMixin';
    import SharpPagination from './Pagination';
    import SharpDropdown from '../dropdown/Dropdown';
    import SharpDropdownItem from '../dropdown/DropdownItem';
    import SharpModal from '../Modal';
    import SharpForm from '../form/Form';
    import SharpViewPanel from './ViewPanel';
    import SharpStateIcon from './StateIcon';

    import Draggable from 'vuedraggable';

    import { API_PATH } from '../../consts';
    import * as util from '../../util';

    import { ActionEvents, Localization } from '../../mixins';

    import * as qs from '../../helpers/querystring';
    import { parseBlobJSONContent, getFileName } from "../../util";


    export default {
        name:'SharpEntityList',
        extends: DynamicView,

        inject: [
            'axiosInstance',
            'actionsBus',
            'params' // querystring params as an object
        ],

        mixins: [ ActionEvents, Localization ],

        components: {
            SharpPagination,
            SharpDropdown,
            SharpDropdownItem,
            SharpModal,
            SharpForm,
            SharpViewPanel,
            SharpStateIcon,
            Draggable
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
                forms: null,

                page: 0,
                search: '',
                sortedBy: null,
                sortDir: null,
                sortDirs: {},

                reorderActive: false,
                reorderedItems: [],
                filtersValue: {},
                showFormModal: {},
                currentFormData: {},
                selectedInstance: null,
                showViewPanel: false,
                viewPanelContent: null,

                headerAutoPadding: {}
            }
        },
        watch: {
            'data.items'(items) {
                if(items.length) {
                    this.$nextTick(() => this.updateHeaderAutoPadding());
                }
            },
            reorderActive() {
                this.$nextTick(() => this.updateHeaderAutoPadding());
            },
            page() {
                document.body.scrollTop = document.documentElement.scrollTop = 0;
            }
        },
        computed: {
            dragOptions() {
                return {
                    disabled: !this.reorderActive
                }
            },

            apiPath() {
                return `list/${this.entityKey}`;
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
            stateAttr() {
                if(!this.config.state)
                    return null;
                return this.config.state.attribute;
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

                return instCmds.length ? this.data.items.reduce((res, {[this.idAttr]:id}) => {
                    let authorizedCmds = instCmds.filter(c=>c.authorization.indexOf(id) !== -1);
                    if(authorizedCmds.length)
                        res[id] = authorizedCmds;
                    return res;
                }, {}) : {};
            },
            multiforms() {
                return Object.values(this.forms);
            },
            multiformKeyByInstanceId() {
                return this.data.items.reduce((res,{[this.idAttr]:id})=>{
                    let multiform = this.multiforms.find(form => form.instances.includes(id)) || {};
                    res[id] = multiform.key;
                    return res;
                }, {})
            },
            noInstanceCommands() {
                return !Object.keys(this.commandsByInstanceId).length;
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
            mount({ containers, layout, data={}, config={}, authorizations, forms }) {
                this.containers = containers;
                this.layout = layout;
                this.data = data;
                this.config = config;
                this.authorizations = authorizations;
                this.forms = forms;

                this.config.commands = config.commands || [];
                this.config.filters = config.filters || [];

                this.page = this.data.page;
                !this.sortDir && (this.sortDir = this.config.defaultSortDir);
                !this.sortedBy && (this.sortedBy = this.config.defaultSort);

                this.sortDirs[this.sortedBy] = this.sortDir;

                this.reorderedItems = [...this.data.items];

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
                        util.error(`EntityList: unknown container "${contLayout.key}" (in layout)`);
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
                    showCreateButton:this.authorizations.create,
                    searchable: this.config.searchable,
                    showReorderButton: this.config.reorderable && this.authorizations.update && this.data.items.length>1,
                    forms: this.forms
                });
            },

            /**
             * Getters
             */
            colClasses({ sizeXS, size, hideOnXS }, extraClasses) {
                return [
                    ...(extraClasses ? [extraClasses] : []),
                    `col-${sizeXS}`,
                    `col-md-${size}`,
                    ...(hideOnXS? ['d-none d-md-flex'] : [])
                ]
            },
            isStateClass(color) {
                return color.indexOf('sharp_') === 0;
            },
            stateClasses({ item, value }) {
                let state = item ? item[this.stateAttr] : value;
                let { color } = this.stateByValue[state];
                return this.isStateClass(color) ? [color] : [];
            },
            stateStyle({ item, value }) {
                let state = item ? item[this.stateAttr] : value;
                let { color } = this.stateByValue[state];
                return !this.isStateClass(color) ? {
                    background: color
                } : '';
            },
            stateLabel(item) {
                const state = item[this.stateAttr];
                return this.stateByValue[state].label;
            },
            hasStateAuthorization({[this.idAttr]:instanceId}) {
                if(!this.config.state) return false;

                let { authorization:auth } = this.config.state;
                return Array.isArray(auth) ? auth.indexOf(instanceId) !== -1 : auth;
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
            instanceCommands({[this.idAttr]:instanceId}) {
                return this.commandsByInstanceId[instanceId]// || [];
            },
            rowHasLink({[this.idAttr]:instanceId}) {
                return this.authorizationsByInstanceId[instanceId].view;
            },
            rowLink({[this.idAttr]:instanceId}) {
                let multiformKey;
                if(this.forms) {
                    multiformKey = this.multiformKeyByInstanceId[instanceId];
                }
                return `${this.formEndpoint(multiformKey)}/${instanceId}`;
            },
            getAuthorizations({ type, id }) {
                return typeof this.authorizations[type] === 'boolean'
                    ? this.authorizations[type]
                    : this.authorizations[type].indexOf(id) !== -1;
            },
            formEndpoint(multiformKey) {
                return `/sharp/form/${this.entityKey}${multiformKey ? `:${multiformKey}` : ''}`
            },
            formInitialProps(form) {
                return {
                    ...form,
                    data: this.currentFormData
                }
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
                if(contKey === this.sortedBy)
                    this.sortDir = this.sortDir === 'asc' ? 'desc': 'asc';
                else
                    this.sortDir = 'asc';
                this.sortedBy = contKey;

                this.page = 1;
                this.update();
            },
            /* (Instance, State) */
            setState({ [this.idAttr]:instanceId }, { value }) {
                this.axiosInstance.post(`${this.apiPath}/state/${instanceId}`, {
                        attribute: this.config.state.attribute,
                        value
                    })
                    .then(({data:{ action, items }})=>{
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
            update({ resetPage=true }={}) {
                if(resetPage && this.page>1)
                    this.page=1;
                this.updateData();
                this.updateHistory();
            },
            updateData() {
                this.get().then(({data:{ data, config }})=>{
                    this.data = data;
                    this.config = config;
                    this.setupActionBar();
                });
            },

            /**
             * Commands
             */
            commandEndpoint(key, { [this.idAttr]:instanceId }={}) {
                return `${this.apiPath}/command/${key}${instanceId?`/${instanceId}`:''}`;
            },
            getCommandFormData(commandKey, instance) {
                return this.axiosInstance.get(`${this.commandEndpoint(commandKey, instance)}/data`, {
                    params: this.apiParams
                }).then(response => response.data.data)
            },

            /* (Command, Instance)
             * Display a form in a modal if the command require a form, else send API request
             */
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

            /* (CommandAPIResponse)
            * Execute the required command action
            */
            async handleCommandResponse(response) {
                if(response.data.type === 'application/json') {
                    const data = await parseBlobJSONContent(response.data);
                    const { action, items, message, html, link } = data;

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
                    else if(action === 'link') {
                        window.location.href = link;
                    }
                } else {
                    this.actionDownload(response);
                }
            },

            /* (CommandKey, BHideModalEvent)
            * Execute form submission
            */
            postCommandForm(key, event) {
                this.actionsBus.$emit('submit', {
                    entityKey: key,
                    endpoint: this.commandEndpoint(key, this.selectedInstance || {}),
                    dataFormatter: form=>({ query:this.apiParams, data:form.data }),
                    postConfig: {
                        responseType: 'blob'
                    }
                });
                event.preventDefault();
                this.$set(this.showFormModal,key,true);
            },

            /* (CommandKey, FormData)
            * Hide the current form modal after data correctly sent, handle actions
            */
            async commandFormSubmitted(key, response) {
                this.selectedInstance = null;
                await this.handleCommandResponse(response);
                await this.$nextTick();
                this.$set(this.showFormModal,key, false);
            },

            /**
             * Actions
             */
            actionReload() {
                this.updateData();
            },
            actionRefresh(items) {
                items.forEach(item => this.$set(this.data.items, this.indexByInstanceId[item[this.idAttr]],item))
            },
            actionDownload({ data:blob, headers }) {
                let $link = document.createElement('a');
                this.$el.appendChild($link);
                $link.href = URL.createObjectURL(blob);
                $link.download = getFileName(headers);
                $link.click();
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

            updateHeaderAutoPadding() {
                if(!this.$refs.actionsCol) return;
                this.headerAutoPadding = {
                    width: `${this.$refs.actionsCol[0].offsetWidth}px`
                }
            }
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
                if(this.filterByKey[key].master) {
                    this.filtersValue = Object.keys(this.filtersValue).reduce((res,key)=>({
                        ...res, [key]:null
                    }), {});
                }
                this.filtersValue[key] = value;
                this.update();
            },
            command: 'sendCommand',
            create(form) {
                location.href=this.formEndpoint(form && form.key);
            },
            toggleReorder({ apply }={}) {
                if(apply) {
                    this.axiosInstance.post(`${this.apiPath}/reorder`, {
                        instances : this.reorderedItems.map(i => i[this.idAttr])
                    }).then(()=>{
                        this.$set(this.data,'items',[...this.reorderedItems]);
                        this.reorderActive = !this.reorderActive;
                    });
                }
                else {
                    this.reorderActive = !this.reorderActive;
                    if(!this.reorderActive) {
                        this.reorderedItems = [...this.data.items];
                    }
                }
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

            window.addEventListener('resize', this.updateHeaderAutoPadding);
        },
        destroyed() {
            window.removeEventListener('resize', this.updateHeaderAutoPadding);
        }
    }
</script>
