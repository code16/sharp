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

        <CommandFormModal :command="currentCommand" ref="commandForm" />
        <CommandViewPanel :content="commandViewContent" @close="handleCommandViewPanelClosed" />
    </div>
</template>

<script>
    import { mapState, mapGetters } from 'vuex';
    import { Grid } from 'sharp-ui';
    import { CommandFormModal, CommandViewPanel } from 'sharp-commands';
    import { withAxiosInterceptors, withCommands } from "sharp/mixins";
    import Widget from '../Widget';
    import ActionBarDashboard from '../ActionBar';

    export default {
        name:'SharpDashboardPage',
        mixins: [withCommands],

        components: {
            Grid,
            Widget,
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
            commands() {
                return this.commandsForType('dashboard') || [];
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
                    getFormData: () => this.$store.dispatch('dashboard/getCommandFormData', { command, query }),
                });
            },
            init() {
                this.$store.dispatch('dashboard/setDashboardKey', this.$route.params.id);
                this.$store.dispatch('setLoading', true);
                this.$store.dispatch('dashboard/get', {
                    filtersValues: this.getFiltersValuesFromQuery(this.$route.query)
                })
                .then(() => {
                    this.ready = true;
                })
                .finally(() => {
                    this.$store.dispatch('setLoading', false);
                })
            },
        },
        created() {
            this.init();
        }
    }
</script>
