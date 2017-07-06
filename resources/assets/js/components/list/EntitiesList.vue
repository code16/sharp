<template>
    <div class="SharpEntitiesList SharpEntitiesList--border">
        <template v-if="ready">
            <div class="SharpEntitiesList__thead">
                <div class="SharpEntitiesList__row SharpEntitiesList__row--header">
                    <div class="SharpEntitiesList__th" v-for="contLayout in layout">
                        {{ containers[contLayout.key].label }}
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
        </template>
    </div>
</template>

<script>
    import DynamicView from '../DynamicViewMixin';
    import { API_PATH } from '../../consts';
    import * as util from '../../util';

    import { ActionEvents } from '../../mixins';

    import * as qs from '../../helpers/querystring';

    import axios from 'axios';

    export default {
        name:'SharpEntitiesList',
        extends: DynamicView,

        inject: ['actionsBus', 'glasspane', 'params'],

        mixins: [ ActionEvents ],

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
                if(this.config.searchable) params.search = this.search;
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
                    itemsCount: this.data.items.length
                });
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

                if(updateData) {
                    axios.get(`/sharp/api/list/${this.entityKey}`, {
                            params:this.apiParams
                        })
                        .then(({data:{ data }})=>{
                            this.data = data;
                            this.setupActionBar();
                        });
                }
                if(updateHistory) {
                    history.pushState(null, null, qs.serialize(this.apiParams));
                }
            }
        },
        created() {
            this.get().then(_=>{
                this.verify();
                this.setupActionBar();
            });

            let { search, page } = this.params;
            this.actionsBus.$emit('searchChanged', search, {updateData: false, updateHistory: false});

            this.page = page;
        }
    }
</script>