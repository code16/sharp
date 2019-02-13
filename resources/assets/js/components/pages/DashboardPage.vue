<template>
    <div class="SharpDashboardPage">
        <template v-if="ready">
            <SharpActionBarDashboard :commands="commands" @command="handleCommandRequested" />
            <SharpGrid :rows="layout.rows">
                <template slot-scope="widgetLayout">
                    <SharpWidget
                        :widget-type="widgets[widgetLayout.key].type"
                        :widget-props="widgets[widgetLayout.key]"
                        :value="data[widgetLayout.key]"
                    />
                </template>
            </SharpGrid>
        </template>

        <SharpCommandFormModal :form="commandCurrentForm" ref="commandForm" />
        <SharpCommandViewPanel :content="commandViewContent" @close="handleCommandViewPanelClosed" />
    </div>
</template>

<script>
    import SharpGrid from '../Grid.vue';
    import SharpWidget from '../dashboard/Widget.vue';
    import SharpActionBarDashboard from '../action-bar/ActionBarDashboard.vue';
    import SharpCommandFormModal from '../commands/CommandFormModal.vue';
    import SharpCommandViewPanel from '../commands/CommandViewPanel.vue';

    import { withAxiosInterceptors } from "../DynamicViewMixin";
    import withCommands from '../../mixins/page/with-commands';

    import { mapState, mapGetters } from 'vuex';

    export default {
        name:'SharpDashboardPage',
        mixins: [withAxiosInterceptors, withCommands],

        components: {
            SharpGrid,
            SharpWidget,
            SharpActionBarDashboard,
            SharpCommandFormModal,
            SharpCommandViewPanel,
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
                getFiltersValuesFromQuery: 'filters/getValuesFromQuery',
                commandsForType: 'commands/forType',
            }),
            commands() {
                return this.commandsForType('dashboard') || [];
            },
        },
        methods: {
            handleCommandRequested(command) {
                const query = this.$route.query;
                this.sendCommand(command, {
                    postCommand: () => this.$store.dispatch('dashboard/postCommand', { command, query }),
                    postForm: data => this.$store.dispatch('dashboard/postCommand', { command, query, data }),
                    getFormData: () => this.$store.dispatch('dashboard/getCommandFormData', { command, query }),
                });
            },
            async init() {
                await this.$store.dispatch('dashboard/setDashboardKey', this.$route.params.id);
                await this.$store.dispatch('dashboard/get', {
                    filtersValues: this.getFiltersValuesFromQuery(this.$route.query)
                });
                this.ready = true;
            },
        },
        created() {
            this.init();
        }
    }
</script>