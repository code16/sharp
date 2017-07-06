<template>
    <div class="SharpEntitiesList">
        <template v-if="ready">
            <div class="SharpEntitiesList__table SharpEntitiesList__table--border">
                <div class="SharpEntitiesList__thead">
                    <div class="SharpEntitiesList__row SharpEntitiesList__row--header">
                        <div class="SharpEntitiesList__th" v-for="contLayout in layout">
                            {{ containers[contLayout.key].label }}
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
                    <div class="SharpEntitiesList__row" v-for="item in data.items" @click="rowClicked(item.id)">
                        <div class="SharpEntitiesList__td" v-for="contLayout in layout">
                            <span v-if="containers[contLayout.key].html" v-html="item[contLayout.key]"></span>
                            <template v-else>
                                {{ item[contLayout.key] }}
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="SharpEntitiesList__pagination-container">
                <sharp-pagination class="SharpPagination"
                                  :total-rows="data.totalCount"
                                  :per-page="data.pageSize"
                                  :min-page-end-buttons="3"
                                  :limit="7"
                                  :value="page"
                                  @change="pageChanged">
                </sharp-pagination>
            </div>
        </template>
    </div>
</template>

<script>
    import DynamicView from '../DynamicViewMixin';
    import Pagination from './Pagination';
    import { API_PATH } from '../../consts';
    import * as util from '../../util';

    import { ActionEvents } from '../../mixins';

    import * as qs from '../../helpers/querystring';

    import axios from 'axios';

    export default {
        name:'SharpEntitiesList',
        extends: DynamicView,

        inject: [
            'actionsBus',
            'glasspane',
            'params' // querystring params as an object
        ],

        mixins: [ ActionEvents ],

        components: {
            [Pagination.name]: Pagination
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

                page: 0,
                search: '',
                sortedBy: null,
                sortDir: 'asc',
            }
        },
        computed: {
            apiPath() {
                return `${API_PATH}/list/${this.entityKey}`;
            },
            apiParams() {
                if(!this.ready)
                    return this.params;

                let params = {};
                if(this.config.paginated) params.page = this.page;
                if(this.config.searchable) params.search = (this.search||'');
                if(this.sortedBy) {
                    params.sort = this.sortedBy;
                    params.dir = this.sortDir;
                }
                return params;
            },
            instanceIdAttribute() {
                return (this.config||{}).instanceIdAttribute;
            },
        },
        methods: {
            mount({ containers, layout, data, config }) {
                this.containers = containers;
                this.layout = layout;
                this.data = data || {};
                this.config = config || {};

                this.page = this.data.page;
            },
            verify() {
                for(let contLayout of this.layout) {
                    if(!(contLayout.key in this.containers)) {
                        util.error(`EntitiesList: unknown container "${contLayout.key}" (in layout)`);
                        this.ready = false;
                    }
                }
            },
            rowClicked(instanceId) {
                location.href = `/sharp/form/${this.entityKey}/${instanceId}`;
            },
            setupActionBar() {
                this.actionsBus.$emit('setup', {
                    itemsCount: this.data.totalCount
                });
            },
            pageChanged(page) {
                this.page = page;
                this.update();
            },
            sortToggle(contKey) {
                if(this.sortedBy === contKey)
                    this.sortDir = this.sortDir==='asc'? 'desc': 'asc';
                this.sortedBy = contKey;

                this.page = 1;
                this.update();
            },
            update() {
                this.updateData();
                this.updateHistory();
            },
            updateData() {
                axios.get(`/sharp/api/list/${this.entityKey}`, {
                        params:this.apiParams
                    })
                    .then(({data:{ data }})=>{
                        this.data = data;
                        this.setupActionBar();
                    });
            },
            updateHistory() {
                history.pushState(null, null, qs.serialize(this.apiParams));
            }
        },
        actions: {
            searchChanged(input, {updateData=true, updateHistory=true}={}) {
                console.log('entities list search changed', input, updateData, updateHistory);

                this.search = input;
                if(this.page>1)  {
                    updateHistory = updateData = true;
                    this.page = 1;
                }

                updateData && this.updateData();
                updateHistory && this.updateHistory();
            }
        },
        created() {
            this.get().then(_=>{
                this.verify();
                this.setupActionBar();
            });

            let { search, page, sort, dir } = this.params;
            this.actionsBus.$emit('searchChanged', search, {updateData: false, updateHistory: false});

            this.page = page;

            this.sortedBy = sort;
            this.sortDir = dir;
        }
    }
</script>