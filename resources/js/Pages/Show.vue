<script setup lang="ts">
    import { computed, ref } from "vue";
    import { BreadcrumbData, ShowData, ShowLayoutSectionData } from "@/types";
    import { CommandFormModal, CommandViewPanel, CommandsDropdown } from '@sharp/commands';
    import ShowField from '@sharp/show/src/components/Field.vue';
    import Section from "@sharp/show/src/components/Section.vue";
    import { GlobalMessage, Breadcrumb, Dropdown, DropdownItem, DropdownSeparator, StateIcon, SectionTitle } from '@sharp/ui';
    import UnknownField from "@/components/dev/UnknownField.vue";
    import Layout from "../Layouts/Layout.vue";
    import { LocaleSelect } from "@sharp/form";
    import { config } from "@/utils/config";
    import { __ } from "@/utils/i18n";
    import { Show } from '@sharp/show/src/Show';

    const props = defineProps<{
        show: ShowData,
        breadcrumb: BreadcrumbData,
    }>();

    const show = new Show(props.show);
    const locale = ref(show.locales?.[0]);

    const reorderingEntityLists = ref({});
    const isReordering = computed(() => Object.values(reorderingEntityLists.value).some(reordering => reordering));
    function onEntityListReordering(key: string, reordering: boolean) {
        reorderingEntityLists.value[key] = reordering;
    }

    const fieldsVisibility = ref<{ [key:string]: boolean }>({});
    const isFieldVisible = (key: string) => fieldsVisibility.value[key] !== false;
    const isSectionVisible = (section: ShowLayoutSectionData) => {
        return show.sectionFields(section).some((field) => isFieldVisible(field?.key));
    }
</script>

<template>
    <Layout>
        <div class="ShowPage">
            <div class="container">
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
                                    :locale="locale"
                                    :locales="show.locales"
                                    @change="locale = $event"
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
                                        <StateIcon class="me-1" :color="show.instanceStateValue ? show.instanceStateValue.color : '#fff'" style="vertical-align: -.125em" />
                                        <span class="text-truncate">{{ show.instanceStateValue ? show.instanceStateValue.label : show.instanceState }}</span>
                                    </template>
                                    <template v-for="stateValue in show.config.state.values" :key="stateValue.value">
                                        <DropdownItem :active="show.instanceState === stateValue.value" @mouseup.prevent.native="handleStateChanged(stateValue.value)">
                                            <StateIcon class="me-1" :color="stateValue.color" style="vertical-align: -.125em" />
                                            <span class="text-truncate">{{ stateValue.label }}</span>
                                        </DropdownItem>
                                    </template>
                                </Dropdown>
                            </div>
                        </template>
                        <template v-if="show.authorizedCommands?.flat().length || show.canDelete">
                            <div class="col-auto">
                                <CommandsDropdown outline :small="false" :commands="show.authorizedCommands" @select="handleCommandRequested">
                                    <template v-slot:text>
                                        {{ __('sharp::entity_list.commands.instance.label') }}
                                    </template>
                                    <template v-if="show.canDelete" v-slot:append>
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
                                <Button :href="show.formUrl" :disabled="isReordering">
                                    {{ __('sharp::action_bar.show.edit_button') }}
                                </Button>
                            </div>
                        </template>
                    </div>
                </div>

                <template v-if="show.config.globalMessage">
                    <GlobalMessage
                        :options="show.config.globalMessage"
                        :data="show.data"
                        :fields="show.fields"
                    />
                </template>

                <div class="ShowPage__content">
                    <template v-if="show.getTitle(locale)">
                        <div class="mb-4">
                            <div class="row align-items-center gx-3 gx-md-4">
                                <div class="col" style="min-width: 0">
                                    <h1 class="mb-0 text-truncate h2" data-top-bar-title v-html="show.getTitle(locale)"></h1>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template v-for="section in show.layout.sections">
<!--                        <div class="ShowSection" :class="classes">-->
<!--                            <div class="row">-->
<!--                                <template v-if="section.collapsable && !show.sectionHasField(section, 'entityList') || section.title">-->
<!--                                    <div class="col">-->
<!--                                        <SectionTitle -->
<!--                                            :section="section" -->
<!--                                            :collapsable="section.collapsable && !show.sectionHasField(section, 'entityList')" -->
<!--                                            :collapsed="collapsed" -->
<!--                                        />-->
<!--                                    </div>-->
<!--                                </template>-->
<!--                                <template v-if="hasCommands && !collapsed">-->
<!--                                    <div class="col-auto align-self-end mb-2">-->
<!--                                        <CommandsDropdown :commands="commands" @select="handleCommandSelected">-->
<!--                                            <template v-slot:text>-->
<!--                                                {{ __('sharp::entity_list.commands.instance.label') }}-->
<!--                                            </template>-->
<!--                                        </CommandsDropdown>-->
<!--                                    </div>-->
<!--                                </template>-->
<!--                            </div>-->

<!--                            <template v-if="!collapsed">-->
<!--                                <div class="ShowSection__content">-->
<!--                                    <Grid class="ShowSection__grid"-->
<!--                                        :rows="[section.columns]"-->
<!--                                        :col-class="() => 'ShowSection__col'"-->
<!--                                        v-slot="{ itemLayout:column }"-->
<!--                                    >-->
<!--                                        <Grid class="ShowPage__fields-grid"-->
<!--                                            :rows="column.fields"-->
<!--                                            :row-class="fieldsRowClass"-->
<!--                                            v-slot="{ itemLayout:fieldLayout }"-->
<!--                                        >-->
<!--                                            <slot :field-layout="fieldLayout" />-->
<!--                                        </Grid>-->
<!--                                    </Grid>-->
<!--                                </div>-->
<!--                            </template>-->
<!--                        </div>-->
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
                            <template v-if="show.fields[fieldLayout.key]">
                                <ShowField
                                    :options="show.fields[fieldLayout.key]"
                                    :value="fieldValue(fieldLayout)"
                                    :locale="locale"
                                    :locales="show.locales"
                                    :config-identifier="fieldLayout.key"
                                    :layout="fieldLayout"
                                    :collapsable="section.collapsable"
                                    @visible-change="handleFieldVisibilityChanged(fieldLayout.key, $event)"
                                    @reordering="onEntityListReordering(fieldLayout.key, $event)"
                                    :key="refreshKey"
                                />
                            </template>
                            <template v-else>
                                <UnknownField :name="fieldLayout.key" />
                            </template>
                        </Section>
                    </template>
                </div>
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
                fieldsVisible: null,
            }
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
                // if(!section.key) {
                //     return null;
                // }
                // return (this.config.commands[section.key] ?? [])
                //     .map(group => group.filter(command => command.authorization));
            },
            sectionHasField(section, type) {
                // const sectionFields = this.sectionFields(section);
                // return sectionFields.some(fieldLayout => {
                //     const options = this.fieldOptions(fieldLayout);
                //     return options && options.type === type;
                // });
            },
            handleFieldVisibilityChanged(key, visible) {
                this.fieldsVisible = {
                    ...this.fieldsVisible,
                    [key]: visible,
                }
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
            }
        },

        beforeMount() {
            this.init();
            this.initCommands();
        },
    }
</script>
