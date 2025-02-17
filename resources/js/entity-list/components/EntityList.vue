<script setup lang="ts">
    import { __, trans_choice } from "@/utils/i18n";
    import { FilterManager } from "@/filters/FilterManager";
    import { EntityList } from "../EntityList";
    import {
        CommandData, EntityListFieldData, EntityListMultiformData, EntityListQueryParamsData,
        EntityStateValueData,
        FilterData
    } from "@/types";
    import WithCommands from "@/commands/components/WithCommands.vue";
    import { CommandManager } from "@/commands/CommandManager";
    import { Ref } from "vue";
    import { computed, ref, watch } from "vue";
    import { showAlert, showDeleteConfirm } from "@/utils/dialogs";
    import { EntityListInstance, InstanceId } from "../types";
    import { getAppendableParentUri, route } from "@/utils/url";
    import { Button } from '@/components/ui/button';
    import { api } from "@/api/api";
    import EntityListPagination from "@/entity-list/components/EntityListPagination.vue";
    import Content from "@/components/Content.vue";
    import SharpFilter from "@/filters/components/Filter.vue";
    import PageAlert from "@/components/PageAlert.vue";
    import StateIcon from '@/components/ui/StateIcon.vue';
    import { useSortable } from '@vueuse/integrations/useSortable';
    import { CardContent, CardDescription, CardFooter, CardHeader } from "@/components/ui/card";
    import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
    import {
        MoreHorizontal,
        PlusCircle,
        Filter,
        ChevronsUpDown,
        ArrowUp,
        ArrowDown,
        GripVertical,
    } from "lucide-vue-next";
    import { Checkbox } from "@/components/ui/checkbox";
    import {
        DropdownMenu, DropdownMenuCheckboxItem,
        DropdownMenuContent,
        DropdownMenuGroup,
        DropdownMenuItem,
        DropdownMenuSeparator,
        DropdownMenuSub, DropdownMenuSubContent,
        DropdownMenuSubTrigger,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import { DropdownMenuPortal } from "reka-ui";
    import CommandDropdownItems from "@/commands/components/CommandDropdownItems.vue";
    import { Badge } from "@/components/ui/badge";
    import { Link, router } from "@inertiajs/vue3";
    import EntityListSearch from "@/entity-list/components/EntityListSearch.vue";
    import StickyTop from "@/components/StickyTop.vue";
    import { cn } from "@/utils/cn";
    import { ScrollArea, ScrollBar } from "@/components/ui/scroll-area";
    import { UseElementBounding, UseWindowSize } from '@vueuse/components';
    import {
        Dialog,
        DialogClose, DialogFooter,
        DialogHeader,
        DialogScrollContent,
        DialogTitle,
        DialogTrigger
    } from "@/components/ui/dialog";
    import RootCard from "@/components/ui/RootCard.vue";
    import DropdownChevronDown from "@/components/ui/DropdownChevronDown.vue";
    import { useBreakpoints } from "@/composables/useBreakpoints";
    import RootCardHeader from "@/components/ui/RootCardHeader.vue";
    import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";

    const props = withDefaults(defineProps<{
        entityKey: string,
        entityList: EntityList | null,
        filters: FilterManager,
        commands: CommandManager,
        title: string,
        inline?: boolean,
        showCreateButton?: boolean,
        showReorderButton?: boolean,
        showSearchField?: boolean,
        showEntityState?: boolean,
        collapsed?: boolean,
        highlightedInstanceId?: string | number,
    }>(), {
        inline: true,
        showCreateButton: true,
        showReorderButton: true,
        showSearchField: true,
        showEntityState: true,
    });

    const el = ref<HTMLElement>();
    const emit = defineEmits(['update:query', 'filter-change', 'reset', 'reordering']);
    const selectedItems: Ref<{ [key: InstanceId]: boolean } | null> = ref(null);
    const selecting = computed(() => !!selectedItems.value);
    const selectedItemsInPage = computed(() => props.entityList.data.filter(item => selectedItems.value?.[props.entityList.instanceId(item)]));

    function onSelectAll(select: boolean) {
        selectedItems.value = {
            ...selectedItems.value,
            ...Object.fromEntries(props.entityList.data.map(item => [
                props.entityList.instanceId(item),
                select
            ])),
        };
    }

    function onSelecting() {
        selectedItems.value = Object.fromEntries(props.entityList.data.map(item => [props.entityList.instanceId(item), false]));
    }

    function onFilterChange(filter: FilterData, value: FilterData['value']) {
        emit('filter-change', filter, value);
    }

    const searchExpanded = ref(false);
    function onSearchSubmit(search: string) {
        emit('update:query', {
            ...props.entityList.query,
            search,
        });
    }

    function onResetAll() {
        emit('reset');
    }

    function onPageChange(page: number) {
        emit('update:query', {
            ...props.entityList.query,
            page,
        });
    }

    function nextSortDir(field: EntityListFieldData): EntityListQueryParamsData['dir'] {
        return props.entityList.currentSort === field.key
            ? props.entityList.currentSortDir === 'desc'
                ? props.entityList.config.defaultSort === props.entityList.currentSort
                    ? 'asc'
                    : null
                : 'desc'
            : 'asc';
    }

    function onSortClick(field: EntityListFieldData) {
        const dir = nextSortDir(field);
        emit('update:query', {
            ...props.entityList.query,
            page: 1,
            sort: dir ? field.key : null,
            dir: dir || null,
        });
    }

    function onInstanceStateChange(value: EntityStateValueData['value'], instanceId: InstanceId) {
        const { commands, entityKey } = props;

        api.post(route('code16.sharp.api.list.state', { entityKey, instanceId }), { value })
            .then(response => {
                commands.handleCommandResponse(response.data);
            })
            .catch(error => {
                const data = error.response?.data;
                if(error.response?.status === 422) {
                    showAlert(data.message, {
                        title: __('modals.state.422.title'),
                        isError: true,
                    });
                }
            });
    }

    async function onCreate(event: MouseEvent, form?: EntityListMultiformData) {
        if(event.metaKey || event.ctrlKey || event.shiftKey) {
            return;
        }
        const { entityKey } = props;

        event.preventDefault();

        if(props.entityList.config.quickCreationForm) {
            await props.commands.send({ hasForm: true } as CommandData, {
                postCommand: route('code16.sharp.api.list.command.quick-creation-form.store', {
                    entityKey: form ? `${entityKey}:${form.key}` : entityKey,
                }),
                getForm: route('code16.sharp.api.list.command.quick-creation-form.create', {
                    entityKey: form ? `${entityKey}:${form.key}` : entityKey,
                }),
                query: props.entityList.query,
                entityKey: form ? `${entityKey}:${form.key}` : entityKey,
            });
        } else {
            router.visit(route('code16.sharp.form.create', {
                parentUri: getAppendableParentUri(),
                entityKey: form ? `${entityKey}:${form.key}` : entityKey,
            }));
        }
    }

    async function onInstanceCommand(command: CommandData, instanceId: InstanceId) {
        const { commands, entityKey } = props;

        if(command.confirmation) {
            el.value.querySelector('[data-reka-scroll-area-viewport]').scrollTo({ left: 0, behavior: 'smooth' });
        }

        await commands.send(command, {
            postCommand: route('code16.sharp.api.list.command.instance', { entityKey, instanceId, commandKey: command.key }),
            getForm: route('code16.sharp.api.list.command.instance.form', { entityKey, instanceId, commandKey: command.key }),
            query: props.entityList.query,
            entityKey,
            instanceId,
        }, {
            highlightElement: () => el.value?.querySelector(`[data-instance-row="${instanceId}"]`) as HTMLElement,
        });
    }

    async function onEntityCommand(command: CommandData) {
        const { commands, entityKey } = props;

        await commands.send(command, {
            postCommand: route('code16.sharp.api.list.command.entity', { entityKey, commandKey: command.key }),
            getForm: route('code16.sharp.api.list.command.entity.form', { entityKey, commandKey: command.key }),
            query: {
                ...props.entityList.query,
                ids: Object.entries(selectedItems.value ?? {})
                    .filter(([instanceId, selected]) => selected)
                    .map(([instanceId, selected]) => instanceId),
            },
            entityKey,
            onSuccess() {
                selectedItems.value = null;
            },
        });
    }

    async function onDelete(instanceId: InstanceId) {
        const { entityKey, entityList, commands } = props;

        el.value.querySelector('[data-reka-scroll-area-viewport]').scrollTo({ left: 0, behavior: 'smooth' });

        if(await showDeleteConfirm(entityList.config.deleteConfirmationText, {
            highlightElement: () => el.value?.querySelector(`[data-instance-row="${instanceId}"]`) as HTMLElement,
        })) {
            await api.delete(route('code16.sharp.api.list.delete', { entityKey, instanceId }));
            commands.handleCommandResponse({ action: 'reload' });
        }
    }

    const reorderedItems: Ref<EntityListInstance[] | null> = ref(null);
    const reordering = computed(() => !!reorderedItems.value);
    const sortableTableBody = ref<InstanceType<typeof TableBody>>();
    const sortable = useSortable<EntityListInstance>(
        () => reordering.value ? sortableTableBody.value.$el : null,
        computed<EntityListInstance[]>({
            get: () => reorderedItems.value ?? [],
            set: (newItems) => {
                reorderedItems.value = newItems;
            }
        }),
        {
            animation: 150,
        }
    );

    watch(reordering, () => {
        if(reordering.value) {
            sortable.start();
        } else {
            sortable.stop();
        }
        emit('reordering', reordering.value);
    });

    function onReordering() {
        reorderedItems.value = [...props.entityList.data]
    }

    async function onReorderSubmit() {
        const { entityKey, commands } = props;

        await api.post(route('code16.sharp.api.list.reorder', { entityKey }), {
            instances: reorderedItems.value.map(item => props.entityList.instanceId(item)),
        });
        await commands.handleCommandResponse({ action: 'reload' });
        reorderedItems.value = null;
    }

    const breakpoints = useBreakpoints();
    const visibleFields = computed(() => props.entityList.fields.filter(field => breakpoints.md ? true : !field.hideOnXS));

    watch([sortableTableBody, () => props.highlightedInstanceId], () => {
        if(sortableTableBody.value && props.highlightedInstanceId) {
            const link = el.value?.querySelector(`[data-instance-row="${props.highlightedInstanceId}"] a`) as HTMLAnchorElement;
            if(link) {
                // link.scrollIntoView({ block: "center" });
                link.focus();
            }
        }
    });
</script>

<template>
    <WithCommands :commands="commands">
        <div ref="el">
            <transition enter-active-class="transition" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <template v-if="reordering">
                    <div class="fixed inset-0 z-[11] bg-black/5"></div>
                </template>
            </transition>

            <template v-if="entityList?.pageAlert">
                <div class="container px-4 lg:px-6">
                    <PageAlert
                        class="mb-3"
                        :page-alert="entityList.pageAlert"
                    />
                </div>
            </template>

            <RootCard :class="reordering ? 'relative z-[12]' : ''">
                <RootCardHeader :class="reordering || selecting ? 'sticky' : 'data-[overflowing-viewport]:sticky'" :collapsed="collapsed || !entityList">
                    <div class="flex flex-wrap md:flex-nowrap gap-y-4 gap-x-2">
                        <div class="flex items-baseline min-w-0">
                            <slot name="card-header" />
                            <template v-if="entityList">
                                <CardDescription class="hidden @2xl/root-card:block text-xs text-muted-foreground ml-4 mr-2 lg:ml-6 whitespace-nowrap" :class="[inline ? 'lg:mr-9' : 'lg:mr-5']">
                                    <template v-if="entityList.query?.search">
                                        {{ trans_choice('sharp::action_bar.list.search.title', entityList.count, { count: entityList.count, search: entityList.query.search }) }}
                                    </template>
                                    <template v-else>
                                        {{ trans_choice('sharp::action_bar.list.items_count', entityList.count, { count: entityList.count }) }}
                                    </template>
                                </CardDescription>
                            </template>
                        </div>
                        <template v-if="entityList">
                            <template v-if="
                                showReorderButton && entityList.canReorder
                                    || entityList.canSelect
                                    || entityList.dropdownEntityCommands(selecting)?.flat().length
                                    || entityList.primaryCommand
                                    || showCreateButton && entityList.authorizations.create
                            ">
                                <div class="ml-auto self-start flex -my-1 justify-end pointer-events-auto gap-2" :class="inline ? '' : ''" v-show="!collapsed">
                                <template v-if="showReorderButton && entityList.canReorder && !selecting">
                                    <template v-if="reordering">
                                        <Button class="h-8" size="sm" variant="outline" @click="reorderedItems = null">
                                            {{ __('sharp::action_bar.list.reorder_button.cancel') }}
                                        </Button>
                                        <Button class="h-8" size="sm" @click="onReorderSubmit">
                                            {{ __('sharp::action_bar.list.reorder_button.finish') }}
                                        </Button>
                                    </template>
                                    <template v-else>
                                        <Button
                                            class="h-8"
                                            :class="entityList.dropdownEntityCommands(selecting)?.flat().length ? 'max-sm:hidden' : ''"
                                            size="sm"
                                            variant="outline"
                                            @click="onReordering"
                                        >
                                            {{ __('sharp::action_bar.list.reorder_button') }}
                                        </Button>
                                    </template>
                                </template>

                                <template v-if="entityList.canSelect && !reordering">
                                    <template v-if="selecting">
                                        <Button class="h-8" size="sm" variant="outline" @click="selectedItems = null">
                                            {{ __('sharp::action_bar.list.reorder_button.cancel') }}
                                        </Button>
                                    </template>
                                    <template v-else>
                                        <Button
                                            class="h-8"
                                            :class="entityList.dropdownEntityCommands(selecting)?.flat().length ? 'max-sm:hidden' : ''"
                                            size="sm"
                                            variant="outline"
                                            @click="onSelecting"
                                        >
                                            {{ __('sharp::action_bar.list.select_button') }}
                                        </Button>
                                    </template>
                                </template>

                                <template v-if="entityList.dropdownEntityCommands(selecting)?.flat().length && !reordering">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button class="h-8" :variant="selecting ? 'default' : 'outline'" size="sm" :disabled="reordering">
                                                <template v-if="selecting">
                                                    {{
                                                        trans_choice(
                                                            'sharp::entity_list.commands.entity.label.selected',
                                                            Object.values(selectedItems).filter(Boolean).length,
                                                            {
                                                                count: Object.values(selectedItems).filter(Boolean).length,
                                                            }
                                                        )
                                                    }}
                                                </template>
                                                <template v-else>
                                                    {{ __('sharp::entity_list.commands.entity.label') }}
                                                </template>
                                                <DropdownChevronDown />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent>
                                            <template v-if="showReorderButton && entityList.canReorder && !selecting">
                                                <DropdownMenuItem class="sm:hidden" @click="onReordering">
                                                    {{ __('sharp::action_bar.list.reorder_button') }}
                                                </DropdownMenuItem>
                                                <DropdownMenuSeparator class="sm:hidden" />
                                            </template>
                                            <template v-if="entityList.canSelect && !selecting">
                                                <DropdownMenuItem class="sm:hidden" @click="onSelecting">
                                                    {{ __('sharp::action_bar.list.select_button') }}
                                                </DropdownMenuItem>
                                                <DropdownMenuSeparator class="sm:hidden" />
                                            </template>
                                            <CommandDropdownItems
                                                :commands="entityList.dropdownEntityCommands(selecting)"
                                                :selecting="selecting"
                                                @select="onEntityCommand"
                                            />
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </template>

                                <template v-if="entityList.primaryCommand && !reordering && !selecting">
                                    <Button class="h-8" size="sm" @click="onEntityCommand(entityList.primaryCommand)">
                                        {{ entityList.primaryCommand.label }}
                                    </Button>
                                </template>

                                <template v-if="showCreateButton && entityList.authorizations.create && !reordering && !selecting">
                                    <template v-if="entityList.forms && Object.values(entityList.forms).length">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger as-child>
                                                <Button class="h-8" size="sm">
                                                    {{ props.entityList.config.createButtonLabel || __('sharp::action_bar.list.forms_dropdown') }}
                                                    <DropdownChevronDown class="opacity-75" />
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent>
                                                <template v-for="form in Object.values(entityList.forms).filter(f => !!f.label)">
                                                    <DropdownMenuItem
                                                        as="a"
                                                        :href="route('code16.sharp.form.create', { parentUri: getAppendableParentUri(), entityKey: `${entityKey}:${form.key}` })"
                                                        @click="onCreate($event, form)"
                                                    >
                                                        {{ form.label }}
                                                    </DropdownMenuItem>
                                                </template>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </template>
                                    <template v-else>
                                        <Button
                                            as="a"
                                            class="h-8 gap-1"
                                            size="sm"
                                            :disabled="reordering || selecting"
                                            :href="route('code16.sharp.form.create', { parentUri: getAppendableParentUri(), entityKey })"
                                            @click="onCreate"
                                        >
                                            {{ props.entityList.config.createButtonLabel || __('sharp::action_bar.list.create_button') }}
                                        </Button>
                                    </template>
                                </template>
                            </div>
                            </template>
                        </template>
                    </div>
                </RootCardHeader>
                <template v-if="entityList && (showSearchField && entityList.config.searchable || entityList.visibleFilters?.length)">
                    <div class="mb-4 flex flex-wrap items-center gap-2" v-show="!collapsed">
                        <template v-if="showSearchField && entityList.config.searchable">
                            <div class="self-center pointer-events-auto"
                                :class="{ 'hidden @2xl/root-card:block': entityList.visibleFilters?.length }"
                            >
                                <EntityListSearch
                                    inline
                                    v-model:expanded="searchExpanded"
                                    :entity-list="entityList"
                                    :disabled="reordering || selecting"
                                    @submit="onSearchSubmit"
                                />
                            </div>
                        </template>
                        <template v-if="entityList.visibleFilters?.length">
                            <div class="contents">
                                <div class="flex items-center @2xl/root-card:hidden">
                                    <Dialog>
                                        <DialogTrigger as-child>
                                            <Button class="h-8 gap-1" variant="outline" :disabled="reordering || selecting" size="sm">
                                                <Filter class="h-3.5 w-3.5" />
                                                <span>
                                                    {{ __('sharp::filters.popover_button') }}
                                                </span>
                                            </Button>
                                        </DialogTrigger>
                                        <DialogScrollContent @open-auto-focus.prevent>
                                            <DialogHeader>
                                                <DialogTitle>
                                                    {{ __('sharp::filters.popover_button') }} : {{ title }}
                                                </DialogTitle>
                                            </DialogHeader>
                                            <div class="flex flex-col flex-wrap gap-4">
                                                <template v-if="showSearchField && entityList.config.searchable">
                                                    <EntityListSearch
                                                        v-model:expanded="searchExpanded"
                                                        :entity-list="entityList"
                                                        @submit="onSearchSubmit"
                                                    />
                                                </template>
                                                <template v-for="filter in entityList.visibleFilters" :key="filter.key">
                                                    <SharpFilter
                                                        :filter="filter"
                                                        :value="filters.currentValues[filter.key]"
                                                        :disabled="reordering"
                                                        :valuated="filters.isValuated([filter])"
                                                        @input="onFilterChange(filter, $event)"
                                                    />
                                                </template>
                                            </div>
                                            <DialogFooter class="flex-row gap-2 mt-2">
                                                <DialogClose as-child>
                                                    <Button class="flex-1" variant="secondary" :disabled="!filters.isValuated(entityList.visibleFilters) && !entityList.query?.search" @click="onResetAll">
                                                        {{ __('sharp::filters.reset_all') }}
                                                    </Button>
                                                </DialogClose>
                                                <DialogClose as-child>
                                                    <Button class="flex-1">
                                                        {{ __('sharp::filters.dialog.submit') }}
                                                    </Button>
                                                </DialogClose>
                                            </DialogFooter>
                                        </DialogScrollContent>
                                    </Dialog>
                                    <template v-if="filters.isValuated(filters.rootFilters)">
                                        <Badge class="ml-2">{{ filters.valuatedCount(filters.rootFilters) }}</Badge>
                                    </template>
                                    <template v-if="entityList">
                                        <CardDescription class="text-xs text-muted-foreground ml-4 mr-2 lg:ml-6 whitespace-nowrap" :class="[inline ? 'lg:mr-9' : 'lg:mr-5']">
                                            <template v-if="entityList.query?.search">
                                                {{ trans_choice('sharp::action_bar.list.search.title', entityList.count, { count: entityList.count, search: entityList.query.search }) }}
                                            </template>
                                            <template v-else>
                                                {{ trans_choice('sharp::action_bar.list.items_count', entityList.count, { count: entityList.count }) }}
                                            </template>
                                        </CardDescription>
                                    </template>
                                </div>
                                <div class="hidden @2xl/root-card:contents"
                                    :class="{
                                        '*:opacity-0 *:pointer-events-none': searchExpanded,
                                    }"
                                >
                                    <template v-for="filter in entityList.visibleFilters" :key="filter.key">
                                        <SharpFilter
                                            :filter="filter"
                                            :value="filters.currentValues[filter.key]"
                                            :disabled="reordering || selecting"
                                            :valuated="filters.isValuated([filter])"
                                            inline
                                            @input="onFilterChange(filter, $event)"
                                        />
                                    </template>
                                    <template v-if="filters.isValuated(entityList.visibleFilters) || entityList.query?.search">
                                        <Button class="h-8 underline underline-offset-4 -ml-2" variant="ghost" size="sm" @click="onResetAll">
                                            {{ __('sharp::filters.reset_all') }}
                                        </Button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <template v-if="entityList">
                    <CardContent :class="entityList.count > 0 ? 'pb-2 !px-0' : ''" v-show="!collapsed">
                        <template v-if="entityList.data?.length > 0">
                            <ScrollArea class="w-full data-[scrollbar-x-visible]:pb-4" type="auto" touch-type="scroll">
                                <Table no-scroll class="w-max min-w-full max-w-[768px] md:max-w-[1024px] @3xl:w-full @3xl:max-w-none">
                                    <TableHeader
                                        :class="!visibleFields.some(field => field.label) ? 'collapse [&_tr]:border-0' : ''"
                                        role="rowgroup"
                                    >
                                        <TableRow class="hover:bg-transparent lg:first:*:pl-6 lg:last:*:pr-6">
                                            <template v-if="selecting">
                                                <TableHead scope="col" class="w-2" aria-label="Select...">
                                                    <TooltipProvider>
                                                        <Tooltip :delay-duration="0">
                                                            <TooltipTrigger as-child>
                                                                <Checkbox
                                                                    class="block"
                                                                    :model-value="selectedItemsInPage.length === entityList.data.length ? true : selectedItemsInPage.length > 0 ? 'indeterminate' : false"
                                                                    @update:model-value="onSelectAll"
                                                                    :aria-label="__('sharp::entity_list.select_all_in_page_checkbox.tooltip.select')"
                                                                />
                                                            </TooltipTrigger>
                                                            <TooltipContent side="top" :side-offset="10">
                                                                <template v-if="selectedItemsInPage.length === entityList.data.length">
                                                                    {{ __('sharp::entity_list.select_all_in_page_checkbox.tooltip.unselect') }}
                                                                </template>
                                                                <template v-else>
                                                                    {{ __('sharp::entity_list.select_all_in_page_checkbox.tooltip.select') }}
                                                                </template>
                                                            </TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider>
                                                </TableHead>
                                            </template>
                                            <template v-for="(field, fieldIndex) in visibleFields" :key="field.key">
                                                <TableHead
                                                    class="max-w-[70cqw] md:w-[var(--width,auto)]"
                                                    scope="col"
                                                    :style="{
                                                        '--width':
                                                            field.width === 'fill' ? (100 / visibleFields.length)+'%' :
                                                            field.width ? field.width :
                                                            field.type === 'state' ? 0 : null
                                                    }"
                                                >
                                                    <template v-if="field.sortable">
                                                        <Button
                                                            variant="ghost"
                                                            size="sm"
                                                            class="-ml-3 h-8 data-[state=open]:bg-accent"
                                                            @click="onSortClick(field)"
                                                            :aria-label="
                                                                nextSortDir(field) === 'asc'
                                                                    ? __('sharp::entity_list.sort_asc', { field_label: field.label })
                                                                    : nextSortDir(field) === 'desc'
                                                                        ? __('sharp::entity_list.sort_desc', { field_label: field.label })
                                                                        : __('sharp::entity_list.sort_default')
                                                            "
                                                        >
                                                            <span>{{ field.label }}</span>
                                                            <template v-if="entityList.currentSort === field.key">
                                                                <ArrowDown v-if="entityList.currentSortDir === 'desc'" class="ml-2 h-3.5 w-3.5" />
                                                                <ArrowUp v-else-if="entityList.currentSortDir === 'asc'" class="ml-2 h-3.5 w-3.5" />
                                                            </template>
                                                            <template v-else>
                                                                <ChevronsUpDown class="ml-2 h-3.5 w-3.5" />
                                                            </template>
                                                        </Button>
                                                    </template>
                                                    <template v-else>
                                                        {{ field.label }}
                                                    </template>
                                                </TableHead>
                                            </template>
                                            <template v-if="!reordering && !selecting && entityList.data.some(item => entityList.instanceHasActions(item, showEntityState))">
                                                <TableHead scope="col" class="w-2">
                                                    <span class="sr-only">Edit</span>
                                                </TableHead>
                                            </template>
                                            <template v-if="reordering">
                                                <TableHead scope="col">
                                                    <span class="sr-only">Select...</span>
                                                </TableHead>
                                            </template>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody class="group" role="rowgroup" ref="sortableTableBody">
                                        <template v-for="(item, itemIndex) in reorderedItems ?? entityList.data" :key="entityList.instanceId(item)">
                                            <TableRow
                                                :class="cn(
                                                    'group/row relative hover:bg-transparent has-[[data-row-action]:hover]:bg-muted/50 has-[[aria-expanded=true]]:bg-muted/50 lg:first:*:pl-6 lg:last:*:pr-6',
                                                    reordering ? 'cursor-move hover:bg-muted/50 group-[:has(.sortable-chosen)]:bg-background [&.sortable-chosen]:transition-none' : ''
                                                )"
                                                :data-instance-row="entityList.instanceId(item)"
                                                :data-highlighted="props.highlightedInstanceId && props.highlightedInstanceId == entityList.instanceId(item) ? true : null"
                                            >
                                                <template v-if="selecting && selectedItems">
                                                    <TableCell>
                                                        <label data-row-action role="presentation">
                                                            <span class="absolute inset-0 z-20"></span>
                                                            <Checkbox
                                                                class="block relative z-20"
                                                                :model-value="selectedItems[entityList.instanceId(item)]"
                                                                :aria-label="__('sharp::entity_list.select_checkbox.aria_label')"
                                                                @update:model-value="(checked) => selectedItems[entityList.instanceId(item)] = checked as boolean"
                                                            />
                                                        </label>
                                                    </TableCell>
                                                </template>
                                                <template v-for="(field, fieldIndex) in visibleFields" :key="field.key">
                                                    <template v-if="field.type === 'state' && entityList.config.state && showEntityState">
                                                        <TableCell class="max-w-[70cqw]">
                                                            <DropdownMenu>
                                                                <DropdownMenuTrigger as-child>
                                                                    <Button class="relative disabled:opacity-100 -mx-3" variant="ghost" size="sm" :disabled="!entityList.instanceCanUpdateState(item)" :aria-label="__('sharp::entity_list.state_dropdown.aria_label', { current_state_label: entityList.instanceStateValue(item)?.label })">
                                                                        <Badge variant="outline">
                                                                            <StateIcon class="-ml-0.5 mr-1.5" :state-value="entityList.instanceStateValue(item)" />
                                                                            {{ entityList.instanceStateValue(item)?.label }}
                                                                        </Badge>
                                                                    </Button>
                                                                </DropdownMenuTrigger>
                                                                <DropdownMenuContent align="start" :align-offset="-16">
                                                                    <template v-for="stateValue in entityList.config.state.values" :key="stateValue.value">
                                                                        <DropdownMenuCheckboxItem
                                                                            :model-value="stateValue.value == entityList.instanceState(item)"
                                                                            @update:model-value="(checked) => checked && onInstanceStateChange(stateValue.value, entityList.instanceId(item))"
                                                                        >
                                                                            <StateIcon class="mr-1.5" :state-value="stateValue" />
                                                                            <span class="truncate">{{ stateValue.label }}</span>
                                                                        </DropdownMenuCheckboxItem>
                                                                    </template>
                                                                </DropdownMenuContent>
                                                            </DropdownMenu>
                                                        </TableCell>
                                                    </template>
                                                    <template v-else>
                                                        <TableCell class="max-w-[70cqw]">
                                                            <template v-if="field.html && typeof item[field.key] === 'string'">
                                                                <Content class="break-words [&_a]:relative [&_a]:z-10"
                                                                    :class="{ '[&_a]:pointer-events-none': selecting || reordering }"
                                                                    :html="item[field.key]"
                                                                />
                                                            </template>
                                                            <template v-else>
                                                                {{ item[field.key] }}
                                                            </template>
                                                            <template v-if="fieldIndex === 0 && entityList.instanceUrl(item) && !selecting && !reordering">
                                                                <Link class="absolute inset-0 ring-ring ring-inset focus-visible:outline-none focus-visible:ring-2 focus:group-data-[highlighted]/row:ring-2"
                                                                    data-row-action
                                                                    :href="entityList.instanceUrl(item)"
                                                                ></Link>
                                                            </template>
                                                        </TableCell>
                                                    </template>
                                                </template>

                                                <template v-if="reordering">
                                                    <TableCell class="w-0 @5xl:pl-4">
                                                        <div class="flex justify-center w-10"> <!-- same size than dropdown -->
                                                            <GripVertical class="w-4 h-4 opacity-50" />
                                                        </div>
                                                    </TableCell>
                                                </template>

                                                <template v-if="!reordering && !selecting">
                                                    <template v-if="entityList.instanceHasActions(item, showEntityState)">
                                                        <TableCell class="sticky bg-background pl-1 -right-3 z-10 group-data-[scroll-arrived-right=true]/scroll-area:bg-transparent @5xl:pl-4 @5xl:bg-transparent">
                                                            <div class="absolute inset-0 -left-2 overflow-hidden" aria-hidden="true"></div>
                                                            <div class="absolute inset-0 -left-4 overflow-hidden pointer-events-none" aria-hidden="true">
                                                                <div class="absolute inset-0 left-4 shadow-l-xl dark:border-l shadow-border transition-opacity group-data-[scroll-arrived-right=true]/scroll-area:opacity-0  @5xl:opacity-0" aria-hidden="true"></div>
                                                            </div>
                                                            <DropdownMenu>
                                                                <DropdownMenuTrigger as-child>
                                                                    <Button class="[@media(hover:hover)]:pointer-events-auto relative"
                                                                        :aria-label="__('sharp::entity_list.commands.instance.label')"
                                                                        variant="ghost"
                                                                        size="icon"
                                                                    >
                                                                        <MoreHorizontal class="w-4 h-4" />
                                                                    </Button>
                                                                </DropdownMenuTrigger>
                                                                <DropdownMenuContent side="bottom" align="center">
                                                                    <template v-if="entityList.config.state && showEntityState && entityList.instanceCanUpdateState(item)">
                                                                        <DropdownMenuGroup>
                                                                            <DropdownMenuSub>
                                                                                <DropdownMenuSubTrigger>
                                                                                    {{ __('sharp::modals.entity_state.edit.title') }}
                                                                                </DropdownMenuSubTrigger>
                                                                                <DropdownMenuPortal>
                                                                                    <DropdownMenuSubContent>
                                                                                        <template v-for="stateValue in entityList.config.state.values" :key="stateValue.value">
                                                                                            <DropdownMenuCheckboxItem
                                                                                                :model-value="stateValue.value == entityList.instanceState(item)"
                                                                                                @update:model-value="(checked) => checked && onInstanceStateChange(stateValue.value, entityList.instanceId(item))"
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
                                                                        :commands="entityList.instanceCommands(item)"
                                                                        @select="(command) => onInstanceCommand(command, entityList.instanceId(item))"
                                                                    />

                                                                    <template v-if="!entityList.config.deleteHidden && entityList.instanceCanDelete(item)">
                                                                        <template v-if="entityList.instanceCommands(item)?.flat().length">
                                                                            <DropdownMenuSeparator class="first:hidden" />
                                                                        </template>
                                                                        <DropdownMenuItem class="text-destructive" @click="onDelete(entityList.instanceId(item))">
                                                                            {{ __('sharp::action_bar.form.delete_button') }}
                                                                        </DropdownMenuItem>
                                                                    </template>
                                                                </DropdownMenuContent>
                                                            </DropdownMenu>
                                                        </TableCell>
                                                    </template>
                                                    <template v-else-if="entityList.data.some(item => entityList.instanceHasActions(item, showEntityState))">
                                                        <TableCell class="w-2"></TableCell>
                                                    </template>
                                                </template>
                                            </TableRow>
                                        </template>
                                    </TableBody>
                                </Table>
                                <template #scrollbar>
                                    <UseWindowSize v-slot="{ height }">
                                        <UseElementBounding class="absolute inset-0 pointer-events-none" v-slot="{ bottom }">
                                            <ScrollBar
                                                class="z-20 [@media(hover:hover)]:bg-background pointer-events-auto will-change-transform"
                                                orientation="horizontal"
                                                :style="{
                                                    transform: `translate3d(0, ${Math.max(0, bottom - height) * -1}px, 0)`
                                                }"
                                            />
                                        </UseElementBounding>
                                    </UseWindowSize>
                                </template>
                            </ScrollArea>
                        </template>
                        <template v-else>
                            <div class="text-muted-foreground">
                                {{ __('sharp::entity_list.empty_text') }}
                            </div>
                        </template>
                    </CardContent>
                    <template v-if="entityList.meta?.prev_page_url || entityList.meta?.next_page_url">
                        <CardFooter class="px-4 pt-4 lg:px-6" v-show="!collapsed">
                            <EntityListPagination
                                :entity-list="entityList"
                                :links-openable="!inline"
                                @change="onPageChange"
                            />
                        </CardFooter>
                    </template>
                </template>
            </RootCard>
        </div>
    </WithCommands>
</template>
