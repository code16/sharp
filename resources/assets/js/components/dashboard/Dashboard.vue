<template>
    <div class="SharpDashboard">
        <template v-if="ready">
            <sharp-grid :rows="layout.rows">
                <template slot-scope="widgetLayout">
                    <sharp-widget
                        :widget-type="widgets[widgetLayout.key].type"
                        :widget-props="widgets[widgetLayout.key]"
                        :value="data[widgetLayout.key]"
                    />
                </template>
            </sharp-grid>
        </template>
    </div>
</template>

<script>
    import SharpGrid from '../Grid';
    import SharpWidget from './Widget';
    import { withAxiosInterceptors } from '../DynamicViewMixin';

    import { mapState, mapGetters } from 'vuex';

    export default {
        name:'SharpDashboard',
        mixins: [withAxiosInterceptors],
        components: {
            SharpGrid,
            SharpWidget
        },
        props: {
            dashboardKey: String
        },

        data() {
            return {
                ready: false
            }
        },
        watch: {
            '$route': 'init'
        },
        computed: {
            ...mapState('dashboard', {
                data: state => state.data,
                widgets: state => state.widgets,
                layout: state => state.layout
            }),
            ...mapGetters('dashboard', {
                getFilterValuesFromQuery: 'filters/getValuesFromQuery',
            }),
        },
        methods: {
            async init() {
                await this.$store.dispatch('dashboard/get', {
                    dashboardKey: this.dashboardKey,
                    filterValues: this.getFilterValuesFromQuery(this.$route.query)
                });
                this.ready = true;
            },
        },
        created() {
            this.init();
        }
    }
</script>