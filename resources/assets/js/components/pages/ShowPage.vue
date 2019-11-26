<template>
    <div class="ShowPage">
        <div class="container">
            <template v-if="ready">
                <SharpActionBarShow
                    :commands="authorizedCommands"
                    :state="instanceState"
                    :state-values="config.state.values"
                    :form-url="formUrl"
                    :back-url="backUrl"
                    :can-edit="canEdit"
                    :can-change-state="canChangeState"
                    :show-back-button="showBackButton"
                    @command="handleCommandRequested"
                    @state-change="handleStateChanged"
                />

                <template v-for="section in layout.sections">
                    <template v-if="section.title">
                        <h2 class="ShowPage__section-title mb-2">{{ section.title }}</h2>
                    </template>
                    <div class="ShowPage__section mb-4" :class="sectionClasses(section)">
                        <SharpGrid :rows="[section.columns]">
                            <template slot-scope="fieldsLayout">
                                <SharpGrid class="ShowPage__fields-grid" :rows="fieldsLayout.fields">
                                    <template slot-scope="fieldLayout">
                                        <SharpShowField
                                            :options="fieldOptions(fieldLayout)"
                                            :value="fieldValue(fieldLayout)"
                                        />
                                    </template>
                                </SharpGrid>
                            </template>
                        </SharpGrid>
                    </div>
                </template>

            </template>
        </div>

        <SharpCommandFormModal :form="commandCurrentForm" ref="commandForm" />
        <SharpCommandViewPanel :content="commandViewContent" @close="handleCommandViewPanelClosed" />
    </div>
</template>

<script>
    import { mapState, mapGetters } from 'vuex';
    import SharpActionBarShow from "../action-bar/ActionBarShow";
    import SharpEntityList from '../list/EntityList';
    import SharpGrid from "../Grid";
    import SharpCommandFormModal from '../commands/CommandFormModal';
    import SharpCommandViewPanel from '../commands/CommandViewPanel';
    import SharpShowField from '../show/Field';
    import { formUrl, getBackUrl } from "../../util/url";
    import withCommands from '../../mixins/page/with-commands';

    export default {
        mixins: [withCommands],

        components: {
            SharpActionBarShow,
            SharpEntityList,
            SharpGrid,
            SharpShowField,
            SharpCommandFormModal,
            SharpCommandViewPanel,
        },

        data() {
            return {
                ready: false,
            }
        },

        computed: {
            ...mapState('show', {
                entityKey: state => state.entityKey,
                instanceId: state => state.instanceId,
            }),
            ...mapGetters('show', [
                'fields',
                'layout',
                'data',
                'config',
                'breadcrumb',
                'instanceState',
                'canEdit',
                'authorizedCommands',
                'canChangeState',
            ]),

            formUrl() {
                return formUrl({
                    entityKey: this.entityKey,
                    instanceId: this.instanceId,
                });
            },
            backUrl() {
                return getBackUrl(this.breadcrumb);
            },
            showBackButton() {
                return !!this.backUrl;
            },
        },

        methods: {
            fieldOptions(layout) {
                return this.fields[layout.key];
            },
            fieldValue(layout) {
                return this.data[layout.key];
            },
            sectionClasses(section) {
                return {
                    'ShowPage__section--no-container': this.sectionHasField(section, 'entityList'),
                }
            },
            sectionHasField(section, type) {
                const sectionFields = section.columns.reduce((res, column) => [...res, ...column.fields.flat()], []);
                return sectionFields.some(fieldLayout => {
                    const options = this.fieldOptions(fieldLayout);
                    return options.type === type;
                });
            },
            handleCommandRequested(command) {
                this.sendCommand(command, {
                    postCommand: () => this.$store.dispatch('show/postCommand', { command }),
                    postForm: data => this.$store.dispatch('show/postCommand', { command, data }),
                    getFormData: () => this.$store.dispatch('show/getCommandFormData', { command }),
                });
            },
            handleStateChanged(state) {
                this.$store.dispatch('show/postState', state)
                    .then(data => {
                        this.handleCommandActionRequested(data.action, data);
                    })
                    .catch(error => {
                        const data = error.response.data;
                        if(error.response.status === 422) {
                            this.actionsBus.$emit('showMainModal', {
                                title: this.l('modals.state.422.title'),
                                text: data.message,
                                isError: true,
                                okCloseOnly: true
                            });
                        }
                    });
            },
            handleRefreshCommand() {
                this.init();
            },
            initCommands() {
                this.addCommandActionHandlers({
                    'refresh': this.handleRefreshCommand,
                });
            },
            async init() {
                await this.$store.dispatch('show/setEntityKey', this.$route.params.entityKey);
                await this.$store.dispatch('show/setInstanceId', this.$route.params.instanceId);
                await this.$store.dispatch('show/get');

                this.ready = true;
            }
        },

        created() {
            this.init();
            this.initCommands();
        },
    }
</script>