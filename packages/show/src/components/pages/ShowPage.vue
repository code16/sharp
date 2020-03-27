<template>
    <div class="ShowPage">
        <div class="container">
            <template v-if="ready">
                <ActionBarShow
                    :commands="authorizedCommands"
                    :state="instanceState"
                    :state-values="stateValues"
                    :form-url="formUrl"
                    :back-url="backUrl"
                    :can-edit="canEdit"
                    :can-change-state="canChangeState"
                    :show-back-button="showBackButton"
                    @command="handleCommandRequested"
                    @state-change="handleStateChanged"
                />

                <template v-for="section in layout.sections">
                    <div class="ShowPage__section" :class="sectionClasses(section)" v-show="isSectionVisible(section)">
                        <template v-if="section.title">
                            <h2 class="ShowPage__section-title mb-2">{{ section.title }}</h2>
                        </template>
                        <div class="ShowPage__section-content mb-4">
                            <Grid class="ShowPage__section-grid" :rows="[section.columns]" :col-class="sectionColClass">
                                <template slot-scope="fieldsLayout">
                                    <Grid class="ShowPage__fields-grid"
                                        :rows="fieldsLayout.fields"
                                        :row-class="fieldsRowClass"
                                    >
                                        <template slot-scope="fieldLayout">
                                            <template v-if="fieldOptions(fieldLayout)">
                                                <ShowField
                                                    :options="fieldOptions(fieldLayout)"
                                                    :value="fieldValue(fieldLayout)"
                                                    :config-identifier="fieldLayout.key"
                                                    :layout="fieldLayout"
                                                    @visible-change="handleFieldVisibilityChanged(fieldLayout.key, $event)"
                                                />
                                            </template>
                                            <template v-else>
                                                <UnknownField :name="fieldLayout.key" />
                                            </template>
                                        </template>
                                    </Grid>
                                </template>
                            </Grid>
                        </div>
                    </div>
                </template>

            </template>
        </div>

        <CommandFormModal :form="commandCurrentForm" ref="commandForm" />
        <CommandViewPanel :content="commandViewContent" @close="handleCommandViewPanelClosed" />
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import { formUrl, getBackUrl } from 'sharp';
    import { CommandFormModal, CommandViewPanel } from 'sharp-commands';
    import { Grid } from 'sharp-ui';
    import { UnknownField } from 'sharp/components';
    import { withCommands } from 'sharp/mixins';
    import ActionBarShow from "../ActionBar";

    import ShowField from '../Field';

    export default {
        mixins: [withCommands],

        components: {
            ActionBarShow,
            Grid,
            ShowField,
            UnknownField,
            CommandFormModal,
            CommandViewPanel,
        },

        data() {
            return {
                ready: false,
                fieldsVisible: null,
            }
        },

        computed: {
            ...mapGetters('show', [
                'entityKey',
                'instanceId',
                'fields',
                'layout',
                'data',
                'config',
                'breadcrumb',
                'instanceState',
                'canEdit',
                'authorizedCommands',
                'stateValues',
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
                const options = this.fields
                    ? this.fields[layout.key]
                    : null;
                if(!options) {
                    console.error(`Show page: unknown field "${layout.key}"`);
                }
                return options;
            },
            fieldValue(layout) {
                return this.data
                    ? this.data[layout.key]
                    : null;
            },
            isFieldVisible(layout) {
                return !this.fieldsVisible || this.fieldsVisible[layout.key] !== false;
            },
            fieldsRowClass(row) {
                const fieldsTypeClasses = row.map(fieldLayout => {
                    const field = this.fieldOptions(fieldLayout);
                    return `ShowPage__fields-row--${field.type}`;
                });
                return [
                    'ShowPage__fields-row',
                    ...fieldsTypeClasses,
                ]
            },
            sectionClasses(section) {
                return {
                    'ShowPage__section--no-container': this.sectionHasField(section, 'entityList'),
                }
            },
            sectionColClass() {
                return 'ShowPage__section-col';
            },
            sectionFields(section) {
                return section.columns.reduce((res, column) => [...res, ...column.fields.flat()], []);
            },
            isSectionVisible(section) {
                const sectionFields = this.sectionFields(section);
                return sectionFields.some(fieldLayout => this.isFieldVisible(fieldLayout));
            },
            sectionHasField(section, type) {
                const sectionFields = this.sectionFields(section);
                return sectionFields.some(fieldLayout => {
                    const options = this.fieldOptions(fieldLayout);
                    return options && options.type === type;
                });
            },
            handleFieldVisibilityChanged(key, visible) {
                this.fieldsVisible = {
                    ...this.fieldsVisible,
                    [key]: visible,
                }
            },
            handleCommandRequested(command) {
                this.sendCommand(command, {
                    postCommand: () => this.$store.dispatch('show/postCommand', { command }),
                    postForm: data => this.$store.dispatch('show/postCommand', { command, data }),
                    getFormData: () => this.$store.dispatch('show/getCommandFormData', { command }),
                });
            },
            handleStateChanged(state) {
                return this.$store.dispatch('show/postState', state)
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

        beforeMount() {
            this.init();
            this.initCommands();
        },
    }
</script>