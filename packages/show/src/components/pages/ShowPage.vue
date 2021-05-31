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
                    :breadcrumb="breadcrumbItems"
                    :show-breadcrumb="breadcrumb.visible"
                    @command="handleCommandRequested"
                    @state-change="handleStateChanged"
                />

                <div class="mt-4 pt-2">
                    <template v-for="section in layout.sections">
                        <Section
                            class="ShowPage__section"
                            v-show="isSectionVisible(section)"
                            :section="section"
                            :layout="sectionLayout(section)"
                            :fields-row-class="fieldsRowClass"
                            :collapsable="isSectionCollapsable(section)"
                            v-slot="{ fieldLayout }"
                        >
                            <template v-if="fieldOptions(fieldLayout)">
                                <ShowField
                                    :options="fieldOptions(fieldLayout)"
                                    :value="fieldValue(fieldLayout)"
                                    :config-identifier="fieldLayout.key"
                                    :layout="fieldLayout"
                                    :collapsable="section.collapsable"
                                    @visible-change="handleFieldVisibilityChanged(fieldLayout.key, $event)"
                                    :key="refreshKey"
                                />
                            </template>
                            <template v-else>
                                <UnknownField :name="fieldLayout.key" />
                            </template>
                        </Section>
                    </template>
                </div>
            </template>
            <template v-else>
                <ActionBarShow />
            </template>
        </div>

        <CommandFormModal :command="currentCommand" ref="commandForm" />
        <CommandViewPanel :content="commandViewContent" @close="handleCommandViewPanelClosed" />
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import { formUrl, getBackUrl, lang, showAlert, handleNotifications, withLoadingOverlay } from 'sharp';
    import { CommandFormModal, CommandViewPanel } from 'sharp-commands';
    import { Grid } from 'sharp-ui';
    import { UnknownField } from 'sharp/components';
    import { withCommands } from 'sharp/mixins';
    import ActionBarShow from "../ActionBar";

    import ShowField from '../Field';
    import Section from "../Section";

    export default {
        mixins: [withCommands],

        components: {
            Section,
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
                refreshKey: 0,
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
                const formKey = this.config.multiformAttribute
                    ? this.data[this.config.multiformAttribute]
                    : null;
                const entityKey = formKey
                    ? `${this.entityKey}:${formKey}`
                    : this.entityKey

                return formUrl({
                    entityKey,
                    instanceId: this.instanceId,
                }, { append: true });
            },
            backUrl() {
                return getBackUrl(this.breadcrumb.items);
            },
            showBackButton() {
                return !!this.backUrl;
            },
            breadcrumbItems() {
                return this.breadcrumb.items;
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
            isSectionCollapsable(section) {
                return section.collapsable && !this.sectionHasField(section, 'entityList');
            },
            sectionLayout(section) {
                if(this.sectionHasField(section, 'entityList')) {
                    return 'contents';
                }
                return 'card';
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
                            showAlert(data.message, {
                                title: lang('modals.state.422.title'),
                                isError: true,
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

                const show = await withLoadingOverlay(
                    this.$store.dispatch('show/get')
                        .catch(error => {
                            this.$emit('error', error);
                            return Promise.reject(error);
                        })
                );

                handleNotifications(show.notifications);

                this.ready = true;
                this.refreshKey++;
            }
        },

        beforeMount() {
            this.init();
            this.initCommands();
        },
    }
</script>
