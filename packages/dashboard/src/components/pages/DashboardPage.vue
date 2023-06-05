<template>
    <div class="SharpDashboardPage" data-popover-boundary>
        <template v-if="ready">
            <div class="container">
                <ActionBarDashboard
                    :commands="commands"
                    :filters="rootFilters"
                    @command="handleCommandRequested"
                    @filter-change="handleFilterChanged"
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
                            :filters="config.filters && config.filters[section.key] || []"
                            @filter-change="handleFilterChanged"
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
</template>

<script>
    import { mapState, mapGetters } from 'vuex';
    import { Grid, GlobalMessage, } from 'sharp-ui';
    import { CommandFormModal, CommandViewPanel } from 'sharp-commands';
    import { withCommands } from "sharp/mixins";
    import { withLoadingOverlay } from "sharp";
    import Widget from '../Widget';
    import ActionBarDashboard from '../ActionBar';
    import Section from "../Section.vue";

    export default {
        name:'SharpDashboardPage',
        mixins: [withCommands],

        components: {
            Section,
            Grid,
            Widget,
            ActionBarDashboard,
            GlobalMessage,
            CommandFormModal,
            CommandViewPanel,
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
                commandsForType: 'commands/forType',
            }),
            dashboardKey() {
                return this.$route.params.dashboardKey;
            },
            commands() {
                return this.commandsForType('dashboard') || [];
            },
            commandsQuery() {
                return {
                    ...this.getFiltersQueryParams(this.filtersValues),
                    ...this.$route.query,
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
            handleFilterChanged(filter, value) {
                this.$router.push({
                    query: {
                        ...this.$route.query,
                        ...this.filterNextQuery({ filter, value }),
                    }
                });
            },
            async init() {
                this.$store.commit('dashboard/setDashboardKey', this.dashboardKey);
                await withLoadingOverlay(
                    this.$store.dispatch('dashboard/get', {
                        filtersValues: this.getFiltersValuesFromQuery(this.$route.query)
                    })
                    .catch(error => {
                        this.$emit('error', error);
                        return Promise.reject(error);
                    })
                );
                this.ready = true;
            },
        },
        created() {
            this.init();
        }
    }
</script>
