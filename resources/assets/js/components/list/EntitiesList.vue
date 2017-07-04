<template>
    <div class="SharpEntitiesList">
        <template v-if="ready">
            <div class="SharpEntitiesList__head">
                <div class="SharpEntitiesList__row SharpEntitiesList__row--header">
                    <div class="SharpEntitiesList__th" v-for="container in containers">
                        {{ container.label }}
                    </div>
                </div>
            </div>
            <div class="SharpEntitiesList__tbody">
                <div class="SharpEntitiesList__row" v-for="item in data.items">
                    <div class="SharpEntitiesList__td" v-for="(container, contKey) in containers">
                        <span v-if="container.html" v-html="item[contKey]"></span>
                        <template v-else>
                            {{ item[contKey] }}
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import DynamicView from '../DynamicViewMixin';

    export default {
        name:'SharpEntitiesList',
        extends: DynamicView,

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
            }
        },
        methods: {
            mount({ containers, layout, data, config }) {
                this.containers = containers;
                this.layout = layout;
                this.data = data || {};
                this.config = config || {};
            }
        }
    }
</script>