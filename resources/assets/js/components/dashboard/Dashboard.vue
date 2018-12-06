<template>
    <div class="SharpDashboard">
        <template v-if="ready">
            <sharp-grid :rows="layout.rows">
                <template slot-scope="widgetLayout">
                    <sharp-widget :widget-type="widgets[widgetLayout.key].type"
                                  :widget-props="widgets[widgetLayout.key]"
                                  :value="data[widgetLayout.key]">
                    </sharp-widget>
                </template>
            </sharp-grid>
        </template>
    </div>
</template>

<script>
    import SharpGrid from '../Grid';
    import SharpWidget from './Widget';
    import DynamicView from '../DynamicViewMixin';

    import { API_PATH } from '../../consts';
    import { mapState, mapGetters } from 'vuex';

    export default {
        name:'SharpDashboard',
        extends: DynamicView,

        components: {
            SharpGrid,
            SharpWidget
        },
        props: {
            dashboardKey: String
        },

        data() {
            return {
                widgets: null
            }
        },
        watch: {
            '$route': 'init'
        },
        computed: {
            apiPath() {
                return `${API_PATH}/dashboard/${this.dashboardKey}`
            },
            apiParams() {
                return {
                    ...this.filtersQuery,
                }
            },
            ...mapGetters('dashboard', {
                filtersQuery: 'filters/queryParams',
                getFilterValuesFromQuery: 'filters/getValuesFromQuery',
            }),
        },
        methods: {
            mount({ layout, data, widgets }) {
                this.layout = layout;
                this.data = data || {};
                this.widgets = widgets;
            },
            async init() {
                const { data } = await this.get();

                await this.$store.dispatch('dashboard/filters/update', {
                    filters: [{"key":"type","multiple":true,"required":false,"default":[],"values":[{"id":3,"label":"blanditiis"},{"id":4,"label":"officiis"},{"id":1,"label":"reiciendis"},{"id":2,"label":"ut"},{"id":5,"label":"velit"}],"label":"Ship type","master":false,"searchable":false,"searchKeys":["label"],"template":"{{label}}"}],
                    values: this.getFilterValuesFromQuery(this.$route.query)
                });
            },
        },
        created() {
            this.init();
        }
    }
</script>