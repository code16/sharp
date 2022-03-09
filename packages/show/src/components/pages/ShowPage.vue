<template>
    <div class="ShowPage" :class="classes">
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

                <div class="ShowPage__content">
                    <template v-if="config.globalMessage">
                        <GlobalMessage
                            class="mb-3"
                            :options="config.globalMessage"
                            :data="data"
                            :fields="fields"
                        />
                    </template>

                    <template v-if="localized">
                        <div class="mb-4">
                            <LocaleSelect
                                :locales="locales"
                                :locale="currentLocale"
                                @change="handleLocaleChanged"
                            />
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
                            v-slot="{ fieldLayout }"
                        >
                            <template v-if="fieldOptions(fieldLayout)">
                                <ShowField
                                    :options="fieldOptions(fieldLayout)"
                                    :value="fieldValue(fieldLayout)"
                                    :locale="fieldLocale(fieldLayout)"
                                    :locales="locales"
                                    :config-identifier="fieldLayout.key"
                                    :layout="fieldLayout"
                                    :collapsable="section.collapsable"
                                    @visible-change="handleFieldVisibilityChanged(fieldLayout.key, $event)"
                                    @locale-change="handleFieldLocaleChanged(fieldLayout.key, $event)"
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
            ref="commandForm"
        />
        <CommandViewPanel
            :content="commandViewContent"
            @close="handleCommandViewPanelClosed"
        />
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import { formUrl, getBackUrl, lang, showAlert, handleNotifications, withLoadingOverlay } from 'sharp';
    import { CommandFormModal, CommandViewPanel } from 'sharp-commands';
    import { Grid, GlobalMessage } from 'sharp-ui';
    import { LocaleSelect } from "sharp-form";
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
            GlobalMessage,
            LocaleSelect,
        },

        data() {
            return {
                ready: false,
                fieldsVisible: null,
                fieldsLocale: null,
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
                'locales',
                'breadcrumb',
                'instanceState',
                'canEdit',
                'authorizedCommands',
                'stateValues',
                'canChangeState',
            ]),
            classes() {
                return {
                    'ShowPage--localized': this.localized,
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
            currentLocale() {
                const locales = [...new Set(Object.values(this.fieldsLocale ?? {}))];
                if(!locales.length) {
                    return this.locales?.[0]
                }
                return locales.length === 1 ? locales[0] : null;
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
            fieldLocale(layout) {
                const options = this.fieldOptions(layout);
                if(!options.localized) {
                    return null;
                }
                return this.fieldsLocale?.[layout.key] ?? this.locales?.[0];
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
            setLocale(locale) {
                this.fieldsLocale = Object.fromEntries(
                    Object.values(this.fields)
                        .filter(fieldOptions => fieldOptions.localized)
                        .map(fieldOptions => [fieldOptions.key, locale])
                );
            },
            handleFieldVisibilityChanged(key, visible) {
                this.fieldsVisible = {
                    ...this.fieldsVisible,
                    [key]: visible,
                }
            },
            handleFieldLocaleChanged(key, locale) {
                this.fieldsLocale = {
                    ...this.fieldsLocale,
                    [key]: locale,
                }
            },
            handleLocaleChanged(locale) {
                this.setLocale(locale);
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
                        const data = error.response?.data;
                        if(error.response?.status === 422) {
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
                    this.setLocale(this.locales[0]);
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
