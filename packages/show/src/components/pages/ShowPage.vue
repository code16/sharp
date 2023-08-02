<template>
    <div class="ShowPage" :class="classes">
        <div class="container">
            <template v-if="ready">
                <ActionBarShow
                    :commands="authorizedCommands"
                    :state="instanceState"
                    :state-options="instanceStateOptions"
                    :state-values="stateValues"
                    :form-url="formUrl"
                    :back-url="backUrl"
                    :can-edit="canEdit"
                    :can-change-state="canChangeState"
                    :show-back-button="showBackButton"
                    :breadcrumb="breadcrumbItems"
                    :show-breadcrumb="breadcrumb.visible"
                    :edit-disabled="isReordering"
                    :locales="locales"
                    :current-locale="locale"
                    :can-delete="canDelete"
                    @command="handleCommandRequested"
                    @state-change="handleStateChanged"
                    @locale-change="handleLocaleChanged"
                    @delete="handleDeleteClicked"
                />

                <template v-if="config.globalMessage">
                    <GlobalMessage
                        :options="config.globalMessage"
                        :data="data"
                        :fields="fields"
                    />
                </template>

                <div class="ShowPage__content">
                    <template v-if="title || localized">
                        <div :class="title ? 'mb-3' : 'mb-4'">
                            <div class="row align-items-center gx-3 gx-md-4">
                                <template v-if="localized">
<!--                                    <div class="col-auto">-->
<!--                                        <LocaleSelect-->
<!--                                            :locales="locales"-->
<!--                                            :locale="locale"-->
<!--                                            @change="handleLocaleChanged"-->
<!--                                        />-->
<!--                                    </div>-->
                                </template>
                                <template v-if="title">
                                    <div class="col" style="min-width: 0">
                                        <h1 class="mb-0 text-truncate h2" data-top-bar-title v-html="title"></h1>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template v-for="section in layout.sections">
                        <Section
                            class="ShowPage__section"
                            v-show="isSectionVisible(section)"
                            :section="section"
                            :layout="sectionLayout(section)"
                            :fields-row-class="fieldsRowClass"
                            :collapsable="isSectionCollapsable(section)"
                            :commands="sectionCommands(section)"
                            @command="handleCommandRequested"
                            v-slot="{ fieldLayout }"
                        >
                            <template v-if="fieldOptions(fieldLayout)">
                                <ShowField
                                    :options="fieldOptions(fieldLayout)"
                                    :value="fieldValue(fieldLayout)"
                                    :locale="locale"
                                    :locales="locales"
                                    :config-identifier="fieldLayout.key"
                                    :layout="fieldLayout"
                                    :collapsable="section.collapsable"
                                    @visible-change="handleFieldVisibilityChanged(fieldLayout.key, $event)"
                                    @reordering="handleReordering(fieldLayout.key, $event)"
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

        <CommandFormModal
            :command="currentCommand"
            :entity-key="entityKey"
            :instance-id="instanceId"
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
    import { mapGetters } from 'vuex';
    import { formUrl, getBackUrl, showAlert, handleNotifications, withLoadingOverlay, showDeleteConfirm } from 'sharp';
    import { CommandFormModal, CommandViewPanel } from '@sharp/commands';
    import { Grid, GlobalMessage } from '@sharp/ui';
    import { __ } from "@/util/i18n";
    import { LocaleSelect } from "@sharp/form";
    import { UnknownField } from 'sharp/components';
    import { withCommands } from 'sharp/mixins';
    import ActionBarShow from "../ActionBar.vue";

    import ShowField from '../Field.vue';
    import Section from "../Section.vue";

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
            GlobalMessage,
            LocaleSelect,
        },

        props: {
            entityKey: String,
            instanceId: String,
            show: Object,
        },

        data() {
            return {
                ready: false,
                fieldsVisible: null,
                locale: null,
                refreshKey: 0,
                reorderingLists: {},
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
                'locales',
                'breadcrumb',
                'authorizations',
                'instanceState',
                'instanceStateOptions',
                'canEdit',
                'authorizedCommands',
                'stateValues',
                'canChangeState',
            ]),
            classes() {
                return {
                    'ShowPage--localized': this.localized,
                    'ShowPage--title': this.title,
                }
            },
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
            localized() {
                return this.locales?.length > 0;
            },
            isSingle() {
                return !!this.config.isSingle;
            },
            canDelete() {
                return this.authorizations?.delete && !this.isSingle;
            },
            title() {
                if(!this.ready || !this.config.titleAttribute) {
                    return null;
                }
                if(this.fields[this.config.titleAttribute]?.localized) {
                    return this.data[this.config.titleAttribute]?.[this.locale];
                }
                return this.data[this.config.titleAttribute];
            },
            isReordering() {
                return Object.values(this.reorderingLists).some(reordering => reordering);
            },
        },

        methods: {
            fieldOptions(layout) {
                const options = this.fields?.[layout.key];
                if(!options) {
                    console.error(`Show page: unknown field "${layout.key}"`);
                }
                return options;
            },
            fieldValue(layout) {
                return this.data?.[layout.key];
            },
            isFieldVisible(layout) {
                return this.fieldsVisible?.[layout.key] !== false;
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
            sectionCommands(section) {
                if(!section.key) {
                    return null;
                }
                return (this.config.commands[section.key] ?? [])
                    .map(group => group.filter(command => command.authorization));
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
                return sectionFields.some(fieldLayout => this.isFieldVisible(fieldLayout))
                    || this.sectionCommands(section)?.flat().length;
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
            handleLocaleChanged(locale) {
                this.locale = locale;
            },
            handleReordering(key, reordering) {
                this.reorderingLists = {
                    [key]: reordering,
                };
            },
            handleCommandRequested(command) {
                this.sendCommand(command, {
                    postCommand: data => this.$store.dispatch('show/postCommand', { command, data }),
                    getForm: commandQuery => this.$store.dispatch('show/getCommandForm', { command, query: { ...commandQuery } }),
                });
            },
            handleStateChanged(state) {
                return this.$store.dispatch('show/postState', state)
                    .then(data => {
                        this.handleCommandActionRequested(data.action, data);
                    })
                    .catch(error => {
                        const data = error.response?.data;
                        if(error.response?.status === 422) {
                            showAlert(data.message, {
                                title: __('sharp::modals.state.422.title'),
                                isError: true,
                            });
                        }
                    });
            },
            async handleDeleteClicked() {
                if(await showDeleteConfirm(this.config.deleteConfirmationText)) {
                    await this.$store.dispatch('show/delete');
                    location.replace(this.backUrl ?? '/');
                }
            },
            handleRefreshCommand() {
                this.init();
            },
            initCommands() {
                this.addCommandActionHandlers({
                    'refresh': this.handleRefreshCommand,
                });
            },
            updateDocumentTitle(show) {
                const label = show.breadcrumb?.items[show.breadcrumb.items.length - 1]?.documentTitleLabel;
                if(label) {
                    document.title = `${label}, ${document.title}`;
                }
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
                this.updateDocumentTitle(show);
                if(this.localized) {
                    this.locale = this.locales?.[0];
                }

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
