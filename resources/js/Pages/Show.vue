<script setup lang="ts">
    import { onMounted, ref } from "vue";
    import { BreadcrumbData, ShowData } from "@/types";
    import { CommandFormModal, CommandViewPanel, CommandsDropdown } from '@sharp/commands';
    import ActionBarShow from "@sharp/show/src/components/ActionBar.vue";
    import ShowField from '@sharp/show/src/components/Field.vue';
    import Section from "@sharp/show/src/components/Section.vue";
    import { GlobalMessage, Breadcrumb, Dropdown, DropdownItem, DropdownSeparator, StateIcon } from '@sharp/ui';
    import UnknownField from "@/components/dev/UnknownField.vue";
    import Layout from "../Layouts/Layout.vue";
    import { LocaleSelect } from "@sharp/form";
    import { config } from "@/utils/config";
    import { __ } from "@/utils/i18n";
    import { getAppendableUri } from "@/utils/url";

    const props = defineProps<{
        show: ShowData,
        breadcrumb: BreadcrumbData,
    }>();

    const authorizedCommands = props.show.config.commands?.instance
        ?.map(group => group.filter(command => command.authorization));

    const formUrl = (() => {
        const formKey = props.show.config.multiformAttribute
            ? props.show.data[props.show.config.multiformAttribute]
            : null;
        const entityKey = formKey
            ? `${route().params.entityKey}:${formKey}`
            : route().params.entityKey;

        if(route().params.instanceId) {
            return route('code16.sharp.form.edit', {
                uri: '(uri)',
                entityKey,
                instanceId: route().params.instanceId,
            }).replace('(uri)', getAppendableUri());
        }

        return route('code16.sharp.form.create', {
            uri: '(uri)',
            entityKey,
        }).replace('(uri)', getAppendableUri());
    })();

    const locale = ref(props.show.locales?.[0]);
</script>

<template>
    <Layout>
        <div class="ShowPage" :class="classes">
            <div class="container">
                <template v-if="ready">
                    <div class="action-bar mt-4 mb-3">
                        <div class="row align-items-center gx-3">
                            <div class="col">
                                <template v-if="config('sharp.display_breadcrumb')">
                                    <Breadcrumb :items="breadcrumb.items" />
                                </template>
                            </div>
                            <template v-if="show.locales?.length">
                                <div class="col-auto">
                                    <LocaleSelect
                                        outline
                                        right
                                        :locale="currentLocale"
                                        :locales="show.locales"
                                        @change="handleLocaleChanged"
                                    />
                                </div>
                            </template>
                            <template v-if="show.config.state">
                                <div class="col-auto">
                                    <Dropdown
                                        :show-caret="show.config.state.authorization"
                                        outline
                                        right
                                        :disabled="!show.config.state.authorization"
                                    >
                                        <template v-slot:text>
                                            <StateIcon class="me-1" :color="stateOptions ? stateOptions.color : '#fff'" style="vertical-align: -.125em" />
                                            <span class="text-truncate">{{ stateOptions ? stateOptions.label : state }}</span>
                                        </template>
                                        <template v-for="stateValue in show.config.state.values" :key="stateValue.value">
                                            <DropdownItem :active="state === stateValue.value" @mouseup.prevent.native="handleStateChanged(stateValue.value)">
                                                <StateIcon class="me-1" :color="stateValue.color" style="vertical-align: -.125em" />
                                                <span class="text-truncate">{{ stateValue.label }}</span>
                                            </DropdownItem>
                                        </template>
                                    </Dropdown>
                                </div>
                            </template>
                            <template v-if="authorizedCommands?.flat().length || show.authorizations.delete && !show.config.isSingle">
                                <div class="col-auto">
                                    <CommandsDropdown outline :small="false" :commands="authorizedCommands" @select="handleCommandRequested">
                                        <template v-slot:text>
                                            {{ __('sharp::entity_list.commands.instance.label') }}
                                        </template>
                                        <template v-if="show.authorizations.delete && !show.config.isSingle" v-slot:append>
                                            <DropdownSeparator />
                                            <DropdownItem link-class="text-danger" @click="handleDeleteClicked">
                                                {{ __('sharp::action_bar.form.delete_button') }}
                                            </DropdownItem>
                                        </template>
                                    </CommandsDropdown>
                                </div>
                            </template>
                            <template v-if="show.authorizations.update">
                                <div class="col-auto">
                                    <Button :href="formUrl" :disabled="editDisabled">
                                        {{ __('sharp::action_bar.show.edit_button') }}
                                    </Button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <ActionBarShow
                        :commands="authorizedCommands"
                        :state="instanceState"
                        :state-options="instanceStateOptions"
                        :state-values="stateValues"
                        :form-url="formUrl"
                        :can-edit="canEdit"
                        :can-change-state="canChangeState"
                        :show-back-button="showBackButton"
                        :breadcrumb="breadcrumbItems"
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
                        <template v-if="title">
                            <div :class="title ? 'mb-3' : 'mb-4'">
                                <div class="row align-items-center gx-3 gx-md-4">
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
    </Layout>
</template>

<script lang="ts">
    import { mapGetters } from 'vuex';
    import { showAlert, handleNotifications, showDeleteConfirm } from 'sharp';
    import { withCommands } from 'sharp/mixins';
    import { router } from "@inertiajs/vue3";

    export default {
        mixins: [withCommands],

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
                'fields',
                'layout',
                'data',
                'config',
                'locales',
                'authorizations',
                'instanceState',
                'instanceStateOptions',
                'canEdit',
                'authorizedCommands',
                'stateValues',
                'canChangeState',
            ]),
            localized() {
                return this.locales?.length > 0;
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
                if(this.fields?.[layout.key]?.type === 'entityList') {
                    return this.entityLists?.[layout.key];
                }
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
                router.reload();
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
                this.$store.commit('show/SET_ENTITY_KEY', this.entityKey);
                this.$store.commit('show/SET_INSTANCE_ID', this.instanceId);
                this.$store.commit('show/SET_SHOW', this.show);

                handleNotifications(this.show.notifications);
                this.updateDocumentTitle(this.show);

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
