<script setup lang="ts">
    import { ref } from "vue";
    import { BreadcrumbData, CommandData, ShowData } from "@/types";
    import { WithCommands, CommandsDropdown } from '@sharp/commands';
    import ShowField from '@sharp/show/src/components/Field.vue';
    import Section from "@sharp/show/src/components/Section.vue";
    import { Dropdown, DropdownItem, DropdownSeparator, StateIcon, SectionTitle, Button } from '@sharp/ui';
    import UnknownField from "@/components/dev/UnknownField.vue";
    import Layout from "@/Layouts/Layout.vue";
    import { LocaleSelect } from "@sharp/form";
    import { config } from "@/utils/config";
    import { __ } from "@/utils/i18n";
    import { Show } from '@sharp/show/src/Show';
    import { showAlert, showDeleteConfirm } from "@/utils/dialogs";
    import Title from "@/components/Title.vue";
    import { useReorderingLists } from "@/Pages/Show/useReorderingLists";
    import { useCommands } from "@sharp/commands/src/useCommands";
    import Breadcrumb from "@/components/Breadcrumb.vue";
    import { api } from "@/api";
    import { router } from "@inertiajs/vue3";
    import { parseQuery } from "@/utils/querystring";
    import PageAlert from "@/components/PageAlert.vue";

    const props = defineProps<{
        show: ShowData,
        breadcrumb: BreadcrumbData,
    }>();

    const show = new Show(props.show);
    const locale = ref(show.locales?.[0]);
    const { entityKey, instanceId } = route().params;
    const { isReordering, onEntityListReordering } = useReorderingLists();
    const commands = useCommands();

    function onCommand(command: CommandData) {
        commands.send(command, {
            postCommand: route('code16.sharp.api.show.command.instance', { entityKey, instanceId, commandKey: command.key }),
            getForm: instanceId
                ? route('code16.sharp.api.show.command.instance.form', { entityKey, instanceId, commandKey: command.key })
                : route('code16.sharp.api.show.command.singleInstance.form', { entityKey, commandKey: command.key }),
            entityKey,
            instanceId,
            query: parseQuery(location.search),
        });
    }

    function onStateChange(value) {
        api.post(route('code16.sharp.api.show.state', { entityKey, instanceId }), { value })
            .then(response => {
                commands.handleCommandReturn(response.data);
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
    }

    async function onDelete() {
        if(await showDeleteConfirm(show.config.deleteConfirmationText)) {
            router.delete(route('code16.sharp.show.delete', { uri: route().params.uri, entityKey, instanceId }));
        }
    }
</script>

<template>
    <Layout>
        <Title :breadcrumb="breadcrumb" />

        <WithCommands :commands="commands">
            <div class="container">
                <div class="action-bar mt-4 mb-3">
                    <div class="row align-items-center gx-3">
                        <div class="col">
                            <template v-if="config('sharp.display_breadcrumb')">
                                <Breadcrumb :breadcrumb="breadcrumb" />
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
                                    :show-caret="!!show.config.state.authorization"
                                    outline
                                    right
                                    :disabled="!show.config.state.authorization"
                                >
                                    <template v-slot:text>
                                        <StateIcon class="me-1" :color="show.instanceStateValue ? show.instanceStateValue.color : '#fff'" style="vertical-align: -.125em" />
                                        <span class="text-truncate">{{ show.instanceStateValue ? show.instanceStateValue.label : show.instanceState }}</span>
                                    </template>
                                    <template v-for="stateValue in show.config.state.values" :key="stateValue.value">
                                        <DropdownItem :active="show.instanceState === stateValue.value" @click="onStateChange(stateValue.value)">
                                            <StateIcon class="me-1" :color="stateValue.color" style="vertical-align: -.125em" />
                                            <span class="text-truncate">{{ stateValue.label }}</span>
                                        </DropdownItem>
                                    </template>
                                </Dropdown>
                            </div>
                        </template>
                        <template v-if="show.allowedInstanceCommands?.flat().length || show.authorizations.delete">
                            <div class="col-auto">
                                <CommandsDropdown outline :small="false" :commands="show.allowedInstanceCommands" @select="onCommand">
                                    <template v-slot:text>
                                        {{ __('sharp::entity_list.commands.instance.label') }}
                                    </template>
                                    <template v-if="show.authorizations.delete" v-slot:append>
                                        <DropdownSeparator />
                                        <DropdownItem link-class="text-danger" @click="onDelete">
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

                <template v-if="show.pageAlert">
                    <PageAlert
                        class="mb-3"
                        :page-alert="show.pageAlert"
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
                        <Section v-slot="{ collapsed, onToggle }">
                            <div v-show="show.sectionShouldBeVisible(section, locale)">
                                <div class="flex">
                                    <template v-if="section.collapsable && !show.sectionHasField(section, 'entityList') || section.title">
                                        <SectionTitle
                                            class="flex-1"
                                            :section="section"
                                            :collapsable="section.collapsable && !show.sectionHasField(section, 'entityList')"
                                            :collapsed="collapsed"
                                            @toggle="onToggle"
                                        />
                                    </template>
                                    <template v-if="show.sectionCommands(section)?.flat().length && !collapsed">
                                        <CommandsDropdown :commands="show.sectionCommands(section)" @select="onCommand">
                                            <template v-slot:text>
                                                {{ __('sharp::entity_list.commands.instance.label') }}
                                            </template>
                                        </CommandsDropdown>
                                    </template>
                                </div>

                                <template v-if="!collapsed">
                                    <div :class="!show.sectionHasField(section, 'entityList') ? 'p-4 bg-white border rounded' : ''">
                                        <div class="flex -mx-4">
                                            <template v-for="column in section.columns">
                                                <div class="w-[calc(12/var(--size)*100%)] px-4" :style="{ '--size': `${column.size}` }">
                                                    <template v-for="row in column.fields">
                                                        <div class="flex -mx-4">
                                                            <template v-for="fieldLayout in row">
                                                                <div class="w-[calc(12/var(--size)*100%)] px-4" :style="{ '--size': `${fieldLayout.size}` }"
                                                                    x-show="show.fieldShouldBeVisible(show.fields[fieldLayout.key], show.data[fieldLayout.key], locale)"
                                                                >
                                                                    <template v-if="show.fields[fieldLayout.key]">
                                                                        <ShowField
                                                                            :field="show.fields[fieldLayout.key]"
                                                                            :value="show.data[fieldLayout.key]"
                                                                            :locale="locale"
                                                                            :config-identifier="fieldLayout.key"
                                                                            :layout="fieldLayout"
                                                                            :collapsable="section.collapsable"
                                                                            :entity-key="entityKey"
                                                                            :instance-id="instanceId"
                                                                            @reordering="onEntityListReordering(fieldLayout.key, $event)"
                                                                        />
                                                                    </template>
                                                                    <template v-else>
                                                                        <UnknownField :name="fieldLayout.key" />
                                                                    </template>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </Section>
                    </template>
                </div>
            </div>
        </WithCommands>
    </Layout>
</template>
