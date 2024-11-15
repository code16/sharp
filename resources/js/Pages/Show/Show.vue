<script setup lang="ts">
    import { provide, ref } from "vue";
    import { BreadcrumbData, CommandData, ShowData } from "@/types";
    import WithCommands from "@/commands/components/WithCommands.vue";
    import Section from "@/show/components/Section.vue";
    import { Button } from '@/components/ui/button';
    import StateIcon from '@/components/ui/StateIcon.vue';
    import UnknownField from "@/components/UnknownField.vue";
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

    const props = defineProps<{
        show: ShowData,
        breadcrumb: BreadcrumbData,
    }>();

    const { entityKey, instanceId } = route().params as { entityKey: string, instanceId?: string };
    const show = new Show(props.show, entityKey, instanceId);
    const locale = ref(show.locales?.[0]);
    const { isReordering, onEntityListReordering } = useReorderingLists();
    const entityListNeedsTopBar = ref(false);
    const commands = useCommands();

    provide('show', show);

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
            <StickyTop
                class="group container mb-4 pointer-events-none top-3.5 data-[stuck=true]:z-20"
                :class="[
                    { 'lg:sticky': !entityListNeedsTopBar }
                ]"
            >
                <div class="flex flex-wrap flex-row-reverse gap-3 pointer-events-auto lg:ml-[--sticky-safe-left-offset]">
                    <div class="flex-1 order-1">
                        <template v-if="show.locales?.length">
                            <Select v-model="locale">
                                <LocaleSelectTrigger />
                                <SelectContent>
                                    <template v-for="locale in show.locales" :key="locale">
                                        <SelectItem :value="locale">
                                            <span class="uppercase text-xs">{{ locale }}</span>
                                        </SelectItem>
                                    </template>
                                </SelectContent>
                            </Select>
                        </template>
                    </div>

                    <div class="flex gap-3 lg:mr-[--sticky-safe-right-offset]">
                        <template v-if="show.config.state">
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button class="h-8 -mx-2 disabled:opacity-100" variant="ghost" size="sm" :disabled="!show.config.state.authorization">
                                        <Badge variant="outline">
                                            <StateIcon class="-ml-0.5 mr-1.5" :state-value="show.instanceStateValue" />
                                            {{ show.instanceStateValue?.label }}
                                        </Badge>
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
                        <template v-if="show.allowedInstanceCommands?.flat().length || show.authorizations.delete || show.config.state">
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button class="h-8" variant="outline" size="sm">
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
                                        <DropdownMenuSeparator />
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
                            <Button class="h-8" size="sm" :disabled="isReordering" as-child>
                                <Link :as="isReordering ? 'button' : 'a'" :href="show.formUrl">
                                    {{ props.show.config.editButtonLabel || __('sharp::action_bar.show.edit_button') }}
                                </Link>
                            </Button>
                        </template>
                    </div>
                </div>
            </StickyTop>

            <template v-if="show.pageAlert">
                <div class="container">
                    <PageAlert
                        class="mb-3"
                        :page-alert="show.pageAlert"
                    />
                </div>
            </template>

            <div class="grid gap-6 md:gap-8">
                <template v-if="show.getTitle(locale) && show.layout.sections[0] && (show.sectionHasField(show.layout.sections[0], 'entityList') || show.layout.sections[0].title)">
                    <Card>
                        <CardHeader>
                            <CardTitle>
                                {{ show.getTitle(locale) }}
                            </CardTitle>
                        </CardHeader>
                    </Card>
                </template>
                <template v-for="(section, i) in show.layout.sections">
                    <Section
                        class="min-w-0"
                        v-show="show.sectionShouldBeVisible(section, locale)"
                        v-slot="{ collapsed, onToggle }"
                    >
                        <template v-if="show.sectionHasField(section, 'entityList')">
                            <template v-for="column in section.columns">
                                <template v-for="row in column.fields">
                                    <template v-for="fieldLayout in row">
                                        <template v-if="show.fields[fieldLayout.key]">
                                            <EntityList
                                                :field="show.fields[fieldLayout.key]"
                                                :collapsable="section.collapsable"
                                                :value="null"
                                                @reordering="onEntityListReordering(fieldLayout.key, $event)"
                                                @needs-topbar="entityListNeedsTopBar = $event"
                                            />
                                        </template>
                                        <template v-else>
                                            <UnknownField :name="fieldLayout.key" />
                                        </template>
                                    </template>
                                </template>
                            </template>
                        </template>
                        <template v-else>
                            <template v-if="show.sectionCommands(section)?.flat().length">
                                <div class="container flex justify-end mb-4" :class="{ 'invisible': collapsed }">
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
                            <RootCard>
                                <template v-if="section.title || (!i && show.getTitle(locale)) || section.collapsable || show.sectionCommands(section)?.flat().length">
                                    <CardHeader>
                                        <div class="flex gap-4">
                                            <template v-if="section.title || (!i && show.getTitle(locale))">
                                                <CardTitle v-html="section.title || (!i && show.getTitle(locale))">
                                                </CardTitle>
                                            </template>
                                            <template v-if="section.collapsable">
                                                <Button variant="ghost" size="sm" class="w-9 p-0 -my-1.5" @click="onToggle">
                                                    <ChevronsUpDown class="w-4 h-4" />
                                                </Button>
                                            </template>
                                        </div>
                                    </CardHeader>
                                </template>
                                <template v-else>
                                    <CardHeader class="pb-0" />
                                </template>
                                <CardContent v-show="!collapsed">
                                    <div class="flex flex-wrap gap-y-4 -mx-4">
                                        <template v-for="(column, columnIndex) in section.columns">
                                            <div class="min-w-0 w-full sm:w-[calc(var(--size)/12*100%)] px-4" :style="{ '--size': `${column.size}` }">
                                                <FieldGrid class="gap-x-4 gap-y-6">
                                                    <template v-for="row in column.fields">
                                                        <FieldGridRow>
                                                            <template v-for="fieldLayout in row">
                                                                <FieldGridColumn
                                                                    :layout="fieldLayout"
                                                                    v-show="show.fieldShouldBeVisible(show.fields[fieldLayout.key], show.data[fieldLayout.key], locale)"
                                                                >
                                                                    <template v-if="show.fields[fieldLayout.key]">
                                                                        <SharpShowField
                                                                            :field="show.fields[fieldLayout.key]"
                                                                            :field-layout="fieldLayout"
                                                                            :value="show.data[fieldLayout.key]"
                                                                            :locale="locale"
                                                                            :collapsable="section.collapsable"
                                                                            :entity-key="entityKey"
                                                                            :instance-id="instanceId"
                                                                            :is-right-col="columnIndex === section.columns.length - 1"
                                                                            @reordering="onEntityListReordering(fieldLayout.key, $event)"
                                                                        />
                                                                    </template>
                                                                    <template v-else>
                                                                        <UnknownField :name="fieldLayout.key" />
                                                                    </template>
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
        </WithCommands>
    </Layout>
</template>
