<template>
    <Layout>
        <div class="SharpDashboardPage" data-popover-boundary>
            <template v-if="ready">
                <div class="container">
                    <ActionBarDashboard
                        :commands="commands"
                        :filters="rootFilters"
                        :show-reset="filterIsValuated(rootFilters)"
                        @command="handleCommandRequested"
                        @filter-change="handleFilterChanged"
                        @filters-reset="handleFiltersReset"
                    />
                    <template v-if="config.globalMessage">
                        <GlobalMessage
                            :options="config.globalMessage"
                            :data="data"
                            :fields="fields"
                        />
                    </template>
                    <div class="mb-n4.5">
                        <template v-for="section in layout.sections">
                            <Section class="mb-4.5"
                                :section="section"
                                :commands="commandsForType(section.key)"
                                :filters="sectionFilters(section)"
                                :show-reset="filterIsValuated(sectionFilters(section))"
                                @filter-change="handleFilterChanged"
                                @filters-reset="handleFiltersReset"
                                v-slot="{ widgetLayout }"
                            >
                                <Widget
                                    :widget-type="widgets[widgetLayout.key].type"
                                    :widget-props="widgets[widgetLayout.key]"
                                    :value="data[widgetLayout.key]"
                                />
                            </Section>
                        </template>
                    </div>
                </div>
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
    import { Grid, GlobalMessage, } from 'sharp-ui';
    import { CommandFormModal, CommandViewPanel } from 'sharp-commands';
    import { withCommands } from "sharp/mixins";
    import Widget from "sharp-dashboard/src/components/Widget.vue";
    import ActionBarDashboard from 'sharp-dashboard/src/components/ActionBar.vue';
    import { parseQuery } from "../util/querystring";
    import Layout from "../Layouts/Layout.vue";
    import Section from 'sharp-dashboard/src/components/Section.vue';

    export default {
        mixins: [withCommands],

        components: {
            Layout,
            Section,
            Grid,
            Widget,
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
                rootFilters: 'filters/rootFilters',
                filtersValues: 'filters/values',
                getFiltersQueryParams: 'filters/getQueryParams',
                getFiltersValuesFromQuery: 'filters/getValuesFromQuery',
                filterNextQuery: 'filters/nextQuery',
                filterDefaultQuery: 'filters/defaultQuery',
                filterIsValuated: 'filters/isValuated',
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
            sectionFilters(section) {
                return this.config.filters?.[section.key] ?? [];
            },
            handleCommandRequested(command) {
                const query = this.commandsQuery;
                this.sendCommand(command, {
                    postCommand: data => this.$store.dispatch('dashboard/postCommand', { command, query, data }),
                    getForm: commandQuery => this.$store.dispatch('dashboard/getCommandForm', { command, query: { ...query, ...commandQuery } }),
                });
            },
            handleFilterChanged(filter, value) {
                // todo update to inertia
                // this.$router.push({
                //     query: {
                //         ...this.$route.query,
                //         ...this.filterNextQuery({ filter, value }),
                //     }
                // });
            },
            handleFiltersReset(filters) {
                // todo update to inertia
                // this.$router.push({
                //     query: {
                //         ...this.$route.query,
                //         ...this.filterDefaultQuery(filters),
                //     }
                // });
            },
            async init() {
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
