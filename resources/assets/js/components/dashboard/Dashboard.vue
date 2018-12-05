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
        computed: {
            apiPath() {
                return `${API_PATH}/dashboard/${this.dashboardKey}`
            },
            ...mapState('dashboard/filters', {
                filtersValue: state => state.value,
            }),
            ...mapGetters('dashboard/filters', {
                filtersQuery: 'queryParams'
            })
        },
        watch: {
            filterValue: 'handleFiltersChanged'
        },
        methods: {
            mount({layout, data, widgets}) {
                this.layout = layout;
                this.data = data || {};
                this.widgets = widgets;
            },
            handleFiltersChanged() {
                // this.$router.push({ query: { ...this.filtersQuery } });
            }
        },
        created() {
            this.get();
            // this.$store.dispatch('dashboard/filters/setFilters', [
            //     {"key":"type","multiple":false,"required":true,"default":3,"values":[{"id":3,"label":"blanditiis"},{"id":4,"label":"officiis"},{"id":1,"label":"reiciendis"},{"id":2,"label":"ut"},{"id":5,"label":"velit"}],"label":"Ship type","master":false,"searchable":false,"searchKeys":["label"],"template":"{{label}}"}
            // ]);
        }
    }
</script>