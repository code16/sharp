<template>
    <div>

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