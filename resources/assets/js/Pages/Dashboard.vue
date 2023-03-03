<template>
    <Layout>
        <div class="SharpDashboardPage" data-popover-boundary>
            <template v-if="ready">
                <div class="container">
                    <ActionBarDashboard
                        :commands="commands"
                        @command="handleCommandRequested"
                    />
                    <template v-if="config.globalMessage">
                        <GlobalMessage
                            :options="config.globalMessage"
                            :data="data"
                            :fields="fields"
                        />
                    </template>
                    <Grid :rows="layout.rows" row-class="gx-3" v-slot="{ itemLayout }">
                        <Widget
                            :widget-type="widgets[itemLayout.key].type"
                            :widget-props="widgets[itemLayout.key]"
                            :value="data[itemLayout.key]"
                        />
                    </Grid>
                </div>
            </template>
            <template v-else>
                <ActionBar />
            </template>

            <CommandFormModal
                :command="currentCommand"
                :entity-key="dashboardKey"
                v-bind="commandFormProps"
                v-on="commandFormListeners"
            />
            <CommandViewPanel
                :content="commandViewContent"
                @close="handleCommandViewPanelClosed"
            />
        </div>
    </Layout>
</template>

<script>
    import { mapState, mapGetters } from 'vuex';
    import { Grid, ActionBar, GlobalMessage, } from 'sharp-ui';
    import { CommandFormModal, CommandViewPanel } from 'sharp-commands';
    import { withCommands } from "sharp/mixins";
    import Widget from "sharp-dashboard/src/components/Widget.vue";
    import ActionBarDashboard from 'sharp-dashboard/src/components/ActionBar';
    import { parseQuery } from "../util/querystring";
    import Layout from "../Layouts/Layout.vue";

    export default {
        mixins: [withCommands],

        components: {
            Layout,
            Grid,
            Widget,
            ActionBar,
            ActionBarDashboard,
            GlobalMessage,
            CommandFormModal,
            CommandViewPanel,
        },

        props: {
            dashboard: Object,
        },

        data() {
            return {
                ready: false
            }
        },
        computed: {
            ...mapState('dashboard', {
                data: state => state.data,
                widgets: state => state.widgets,
                layout: state => state.layout,
                config: state => state.config,
                fields: state => state.fields,
            }),
            ...mapGetters('dashboard', {
                filtersValues: 'filters/values',
                getFiltersQueryParams: 'filters/getQueryParams',
                getFiltersValuesFromQuery: 'filters/getValuesFromQuery',
                commandsForType: 'commands/forType',
            }),
            dashboardKey() {
                return route().params.dashboardKey;
            },
            commands() {
                return this.commandsForType('dashboard') || [];
            },
            commandsQuery() {
                return {
                    ...this.getFiltersQueryParams(this.filtersValues),
                    ...parseQuery(location.search),
                }
            },
        },
        methods: {
            handleCommandRequested(command) {
                const query = this.commandsQuery;
                this.sendCommand(command, {
                    postCommand: data => this.$store.dispatch('dashboard/postCommand', { command, query, data }),
                    getForm: commandQuery => this.$store.dispatch('dashboard/getCommandForm', { command, query: { ...query, ...commandQuery } }),
                });
            },
            init() {
                this.$store.commit('dashboard/setDashboardKey', this.dashboardKey);
                this.$store.commit('dashboard/UPDATE', this.dashboard);
                this.ready = true;
            },
        },
        created() {
            this.init();
        }
    }
</script>
