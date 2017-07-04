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
                <div class="SharpEntitiesList__row" v-for="item in data.items">
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
    import util from '../../util';

    export default {
        name:'SharpEntitiesList',
        extends: DynamicView,

        inject:['ActionsBus', 'glasspane'],

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
                    return;

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
            },
            verify() {
                for(let contLayout of this.layout) {
                    if(!(contLayout.key in this.containers)) {
                        util.error(`EntitiesList: unknown container "${contLayout.key}" (in layout)`);
                        this.ready = false;
                    }
                }
            }
        },
        created() {
            this.get().then(_=>this.verify());
        }
    }
</script>