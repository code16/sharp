<template>
    <div class="SharpDashboardPage">
        <template v-if="ready">
            <div class="container">
                <ActionBarDashboard :commands="commands" @command="handleCommandRequested" />
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
            ref="commandForm"
        />
        <CommandViewPanel
            :content="commandViewContent"
            @close="handleCommandViewPanelClosed"
        />
    </div>
</template>

<script>
    import { mapState, mapGetters } from 'vuex';
    import { Grid, ActionBar } from 'sharp-ui';
    import { CommandFormModal, CommandViewPanel } from 'sharp-commands';
    import { withCommands } from "sharp/mixins";
    import { withLoadingOverlay } from "sharp";
    import Widget from '../Widget';
    import ActionBarDashboard from '../ActionBar';

    export default {
        name:'SharpDashboardPage',
        mixins: [withCommands],

        components: {
            Grid,
            Widget,
            ActionBar,
            ActionBarDashboard,
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
                layout: state => state.layout
            }),
            ...mapGetters('dashboard', {
                filtersValues: 'filters/values',
                getFiltersQueryParams: 'filters/getQueryParams',
                getFiltersValuesFromQuery: 'filters/getValuesFromQuery',
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
                    postCommand: () => this.$store.dispatch('dashboard/postCommand', { command, query }),
                    postForm: data => this.$store.dispatch('dashboard/postCommand', { command, query, data }),
                    getForm: () => this.$store.dispatch('dashboard/getCommandForm', { command, query }),
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
