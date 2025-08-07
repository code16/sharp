<script setup lang="ts">
    import { computed, provide, ref } from "vue";
    import { BreadcrumbData, CommandData, ShowData, ShowEntityListFieldData } from "@/types";
    import WithCommands from "@/commands/components/WithCommands.vue";
    import Section from "@/show/components/Section.vue";
    import { Button } from '@/components/ui/button';
    import StateIcon from '@/components/ui/StateIcon.vue';
    import Layout from "@/Layouts/Layout.vue";
    import { config } from "@/utils/config";
    import { __ } from "@/utils/i18n";
    import { Show } from '@/show/Show';
    import { showAlert, showDeleteConfirm } from "@/utils/dialogs";
    import Title from "@/components/Title.vue";
    import { useReorderingLists } from "@/Pages/Show/useReorderingLists";
    import { useCommands } from "@/commands/useCommands";
    import PageBreadcrumb from "@/components/PageBreadcrumb.vue";
    import { api } from "@/api/api";
    import { router, Link } from "@inertiajs/vue3";
    import { parseQuery } from "@/utils/querystring";
    import PageAlert from "@/components/PageAlert.vue";
    import { route } from "@/utils/url";
    import FieldGrid from "@/components/ui/FieldGrid.vue";
    import FieldGridRow from "@/components/ui/FieldGridRow.vue";
    import FieldGridColumn from "@/components/ui/FieldGridColumn.vue";
    import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
    import EntityList from "@/show/components/fields/entity-list/EntityList.vue";
    import { ChevronsUpDown } from "lucide-vue-next";
    import StickyTop from "@/components/StickyTop.vue";
    import { Select, SelectContent, SelectItem } from "@/components/ui/select";
    import {
        DropdownMenu,
        DropdownMenuCheckboxItem,
        DropdownMenuContent, DropdownMenuGroup, DropdownMenuItem, DropdownMenuSeparator,
        DropdownMenuSub, DropdownMenuSubContent,
        DropdownMenuSubTrigger,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import { Badge } from "@/components/ui/badge";
    import CommandDropdownItems from "@/commands/components/CommandDropdownItems.vue";
    import { DropdownMenuPortal } from "reka-ui";
    import RootCard from "@/components/ui/RootCard.vue";
    import LocaleSelectTrigger from "@/components/LocaleSelectTrigger.vue";
    import DropdownChevronDown from "@/components/ui/DropdownChevronDown.vue";
    import { useEntityListHighlightedItem } from "@/composables/useEntityListHighlightedItem";
    import RootCardHeader from "@/components/ui/RootCardHeader.vue";
    import StateBadge from "@/components/ui/StateBadge.vue";
    import { sanitize } from "@/utils/sanitize";

    const props = defineProps<{
        show: ShowData,
        breadcrumb: BreadcrumbData,
    }>();

    const { entityKey, instanceId } = route().params as { entityKey: string, instanceId?: string };
    const show = new Show(props.show, entityKey, instanceId);
    const locale = ref(show.locales?.[0]);
    const { isReordering, onEntityListReordering } = useReorderingLists();
    const commands = useCommands('show');
    const { highlightedEntityKey, highlightedInstanceId } = useEntityListHighlightedItem();

    provide('show', show);

    const sectionsWithAlwaysFirst = computed<typeof show.layout.sections>(() => {
        if(show.layout.sections[0] && (
                show.sectionHasField(show.layout.sections[0], 'entityList')
                || show.layout.sections[0].title
                || show.sectionCommands(show.layout.sections[0])?.flat().length
            )
            || !show.layout.sections.length) {
            return [
                {
                    key: null,
                    title: '',
                    collapsable: false,
                    columns: [],
                },
                ...show.layout.sections,
            ];
        }
        return show.layout.sections;
    })

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

    function onStateChange(value: string | number) {
        api.post(route('code16.sharp.api.show.state', { entityKey, instanceId }), { value })
            .then(response => {
                commands.handleCommandResponse(response.data);
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
            router.delete(
                route('code16.sharp.show.delete', {
                    parentUri: route().params.parentUri as string,
                    entityKey,
                    instanceId
                })
            );
        }
    }
</script>

<template>
    <Layout>
        <Title :breadcrumb="breadcrumb" />

        <template #breadcrumb>
            <template v-if="config('sharp.display_breadcrumb')">
                <PageBreadcrumb :breadcrumb="breadcrumb" />
            </template>
        </template>

        <WithCommands :commands="commands">
            <div class="@container">
                <div :class="show.pageAlert ? 'pt-4' : 'pt-6 @3xl:pt-10'">
                    <template v-if="show.pageAlert">
                        <div class="container">
                            <PageAlert
                                class="mb-4"
                                :page-alert="show.pageAlert"
                            />
                        </div>
                    </template>

                    <div class="grid gap-6 @3xl:gap-10">
                        <template v-for="(section, i) in sectionsWithAlwaysFirst">
                            <Section
                                class="min-w-0"
                                :section="section"
                                :aria-labelledby="`section-${i}-title`"
                                v-show="show.sectionShouldBeVisible(section, locale) || i == 0"
                                v-slot="{ collapsed, onCollapseToggle }"
                            >
                                <template v-if="show.sectionHasField(section, 'entityList')">
                                    <template v-for="column in section.columns">
                                        <template v-for="row in column.fields">
                                            <template v-for="fieldLayout in row">
                                                <template v-if="show.fields[fieldLayout.key]">
                                                    <EntityList
                                                        :field="show.fields[fieldLayout.key] as ShowEntityListFieldData"
                                                        :collapsable="section.collapsable"
                                                        :value="null"
                                                        :highlighted-instance-id="highlightedEntityKey === (show.fields[fieldLayout.key] as ShowEntityListFieldData).entityListKey ? highlightedInstanceId : null"
                                                        :aria-labelledby="`section-${i}-title`"
                                                        @reordering="onEntityListReordering(fieldLayout.key, $event)"
                                                    />
                                                </template>
                                                <template v-else>
                                                    Undefined EntityList <span class="font-mono">{{ fieldLayout.key }}</span>
                                                </template>
                                            </template>
                                        </template>
                                    </template>
                                </template>
                                <template v-else>
                                    <RootCard>
                                        <template v-if="section.title || i == 0 || section.collapsable || show.sectionCommands(section)?.flat().length">
                                            <RootCardHeader class="data-overflowing-viewport:sticky" :collapsed="collapsed">
                                                <div class="flex flex-wrap gap-4">
                                                    <div class="flex gap-4">
                                                        <template v-if="section.title || (i == 0 && show.getTitle(locale))">
                                                            <CardTitle
                                                                :id="`section-${i}-title`"
                                                                class="py-1 -my-1"
                                                                v-html="section.title || (i == 0 && sanitize(show.getTitle(locale)))"
                                                            >
                                                            </CardTitle>
                                                        </template>
                                                        <template v-if="section.collapsable">
                                                            <Button variant="ghost" size="sm" class="w-9 p-0 -my-1.5" @click="onCollapseToggle">
                                                                <ChevronsUpDown class="w-4 h-4" />
                                                            </Button>
                                                        </template>
                                                    </div>
                                                    <template v-if="show.sectionCommands(section)?.flat().length">
                                                        <div class="ml-auto flex -my-1 justify-end" :class="{ 'invisible': collapsed }"
                                                            role="group"
                                                            :aria-label="__('sharp::show.section_menu.aria_label', { title: section.title })"
                                                        >
                                                            <DropdownMenu>
                                                                <DropdownMenuTrigger as-child>
                                                                    <Button class="h-8" size="sm" variant="outline">
                                                                        {{ __('sharp::entity_list.commands.instance.label') }}
                                                                        <DropdownChevronDown />
                                                                    </Button>
                                                                </DropdownMenuTrigger>
                                                                <DropdownMenuContent>
                                                                    <CommandDropdownItems
                                                                        :commands="show.sectionCommands(section)"
                                                                        @select="onCommand"
                                                                    />
                                                                </DropdownMenuContent>
                                                            </DropdownMenu>
                                                        </div>
                                                    </template>
                                                    <template v-if="i == 0">
                                                        <div class="ml-auto flex flex-wrap -my-1 justify-end gap-3"
                                                            :class="{ 'invisible': collapsed }"
                                                            role="group"
                                                            :aria-label="__('sharp::show.section_menu.aria_label', { title: show.getTitle(locale) })"
                                                        >
                                                            <template v-if="show.config.state">
                                                                <DropdownMenu>
                                                                    <DropdownMenuTrigger as-child>
                                                                        <Button class="pointer-events-auto h-8 -mx-2 disabled:opacity-100 hover:bg-transparent aria-expanded:bg-transparent" variant="ghost" size="sm" :disabled="!show.config.state.authorization"
                                                                            :aria-label="__('sharp::show.state_dropdown.aria_label')"
                                                                        >
                                                                            <StateBadge :state-value="show.instanceStateValue">
                                                                                {{ show.instanceStateValue?.label }}
                                                                            </StateBadge>
                                                                        </Button>
                                                                    </DropdownMenuTrigger>
                                                                    <DropdownMenuContent>
                                                                        <template v-for="stateValue in show.config.state.values" :key="stateValue.value">
                                                                            <DropdownMenuCheckboxItem
                                                                                :model-value="stateValue.value === show.instanceState"
                                                                                @update:model-value="(checked) => checked && onStateChange(stateValue.value)"
                                                                            >
                                                                                <StateIcon class="mr-1.5" :state-value="stateValue" />
                                                                                <span class="truncate">{{ stateValue.label }}</span>
                                                                            </DropdownMenuCheckboxItem>
                                                                        </template>
                                                                    </DropdownMenuContent>
                                                                </DropdownMenu>
                                                            </template>
                                                            <template v-if="show.locales?.length">
                                                                <Select v-model="locale">
                                                                    <LocaleSelectTrigger class="pointer-events-auto mr-auto" />
                                                                    <SelectContent>
                                                                        <template v-for="locale in show.locales" :key="locale">
                                                                            <SelectItem :value="locale">
                                                                                <span class="uppercase text-xs">{{ locale }}</span>
                                                                            </SelectItem>
                                                                        </template>
                                                                    </SelectContent>
                                                                </Select>
                                                            </template>
                                                            <template v-if="(show.allowedInstanceCommands?.flat().length || show.authorizations.delete || show.config.state && show.config.state.authorization) || show.authorizations.update">
                                                                <div class="flex gap-3">
                                                                    <template v-if="show.allowedInstanceCommands?.flat().length || show.authorizations.delete || show.config.state && show.config.state.authorization">
                                                                        <DropdownMenu>
                                                                            <DropdownMenuTrigger as-child>
                                                                                <Button class="pointer-events-auto h-8" variant="outline" size="sm">
                                                                                    {{ __('sharp::entity_list.commands.instance.label') }}
                                                                                    <DropdownChevronDown />
                                                                                </Button>
                                                                            </DropdownMenuTrigger>
                                                                            <DropdownMenuContent>
                                                                                <template v-if="show.config.state && show.config.state.authorization">
                                                                                    <DropdownMenuGroup>
                                                                                        <DropdownMenuSub>
                                                                                            <DropdownMenuSubTrigger>
                                                                                                {{ __('sharp::modals.entity_state.edit.title') }}
                                                                                            </DropdownMenuSubTrigger>
                                                                                            <DropdownMenuPortal>
                                                                                                <DropdownMenuSubContent>
                                                                                                    <template v-for="stateValue in show.config.state.values" :key="stateValue.value">
                                                                                                        <DropdownMenuCheckboxItem
                                                                                                            :model-value="stateValue.value === show.instanceState"
                                                                                                            @update:model-value="(checked) => checked && onStateChange(stateValue.value)"
                                                                                                        >
                                                                                                            <StateIcon class="mr-1.5" :state-value="stateValue" />
                                                                                                            <span class="truncate">{{ stateValue.label }}</span>
                                                                                                        </DropdownMenuCheckboxItem>
                                                                                                    </template>
                                                                                                </DropdownMenuSubContent>
                                                                                            </DropdownMenuPortal>
                                                                                        </DropdownMenuSub>
                                                                                    </DropdownMenuGroup>
                                                                                    <DropdownMenuSeparator class="last:hidden" />
                                                                                </template>

                                                                                <CommandDropdownItems
                                                                                    :commands="show.allowedInstanceCommands"
                                                                                    @select="onCommand"
                                                                                />
                                                                                <template v-if="show.authorizations.delete">
                                                                                    <template v-if="show.allowedInstanceCommands?.flat().length">
                                                                                        <DropdownMenuSeparator />
                                                                                    </template>
                                                                                    <DropdownMenuItem class="text-destructive" @click="onDelete">
                                                                                        {{ __('sharp::action_bar.form.delete_button') }}
                                                                                    </DropdownMenuItem>
                                                                                </template>
                                                                            </DropdownMenuContent>
                                                                        </DropdownMenu>
                                                                    </template>
                                                                    <template v-if="show.authorizations.update">
                                                                        <Button class="h-8 pointer-events-auto" size="sm" :disabled="isReordering" as-child>
                                                                            <Link :as="isReordering ? 'button' : 'a'" :href="show.formUrl">
                                                                                {{ props.show.config.editButtonLabel || __('sharp::action_bar.show.edit_button') }}
                                                                            </Link>
                                                                        </Button>
                                                                    </template>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </RootCardHeader>
                                        </template>
                                        <CardContent v-show="section.columns.map((column) => column.fields).flat(2).length && !collapsed">
                                            <div class="grid grid-cols-1 gap-6 @3xl/root-card:grid-cols-12">
                                                <template v-for="(column, columnIndex) in section.columns">
                                                    <div class="@3xl/root-card:col-[span_var(--size)]" :style="{ '--size': `${column.size}` }">
                                                        <FieldGrid class="gap-x-4 gap-y-6">
                                                            <template v-for="row in column.fields">
                                                                <FieldGridRow v-show="show.fieldRowShouldBeVisible(row, locale)">
                                                                    <template v-for="fieldLayout in row">
                                                                        <FieldGridColumn
                                                                            :layout="fieldLayout"
                                                                            v-show="show.fieldShouldBeVisible(fieldLayout, locale)"
                                                                        >
                                                                            <SharpShowField
                                                                                :field="show.fields[fieldLayout.key]"
                                                                                :field-layout="fieldLayout"
                                                                                :value="show.data[fieldLayout.key]"
                                                                                :locale="locale"
                                                                                :collapsable="section.collapsable"
                                                                                :entity-key="entityKey"
                                                                                :instance-id="instanceId"
                                                                                :is-right-col="columnIndex > 0 && columnIndex === section.columns.length - 1"
                                                                                :row="row"
                                                                                @reordering="onEntityListReordering(fieldLayout.key, $event)"
                                                                            />
                                                                        </FieldGridColumn>
                                                                    </template>
                                                                </FieldGridRow>
                                                            </template>
                                                        </FieldGrid>
                                                    </div>
                                                </template>
                                            </div>
                                        </CardContent>
                                    </RootCard>
                                </template>
                            </Section>
                        </template>
                    </div>
                </div>
            </div>
        </WithCommands>
    </Layout>
</template>
