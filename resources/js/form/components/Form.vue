<script setup lang="ts">
    import { FormData, FormFieldData,  LayoutFieldData } from "@/types";
    import PageAlert from "@/components/PageAlert.vue";
    import { provide, ref, useTemplateRef } from "vue";
    import { Form } from "../Form";
    import { getDependantFieldsResetData } from "../util";
    import FieldGrid from "@/components/ui/FieldGrid.vue";
    import FieldGridRow from "@/components/ui/FieldGridRow.vue";
    import FieldGridColumn from "@/components/ui/FieldGridColumn.vue";
    import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
    import { ApiResponse } from "@/api/types";
    import { __ } from "@/utils/i18n";
    import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
    import RootCard from "@/components/ui/RootCard.vue";
    import LocaleSelectTrigger from "@/components/LocaleSelectTrigger.vue";
    import { useFormTabs } from "@/form/components/useFormTabs";
    import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
    import { slugify } from "@/utils";
    import { Badge } from "@/components/ui/badge";
    import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
    import { FieldMeta, FormFieldEmitInputOptions } from "@/form/types";
    import StickyTop from "@/components/StickyTop.vue";
    import StickyBottom from "@/components/StickyBottom.vue";
    import { Menu } from 'lucide-vue-next';
    import { Label } from "@/components/ui/label";
    import RootCardHeader from "@/components/ui/RootCardHeader.vue";
    import { vScrollIntoView } from "@/directives/scroll-into-view";
    import { useResizeObserver } from "@vueuse/core";
    import debounce from "lodash/debounce";
    import { api } from "@/api/api";
    import { route } from "@/utils/url";
    import { useParentCommands } from "@/commands/useCommands";
    import merge from 'lodash/merge';

    const props = defineProps<{
        form: Form
        modal?: boolean,
        postFn?: (data: FormData['data']) => Promise<ApiResponse<any>>,
        showErrorAlert?: boolean,
        errorAlertMessage?: string,
        persistThumbnailUrl?: boolean,
        tab?: string,
    }>();

    provide('form', props.form);

    const loading = ref(false);
    const selectedTabSlug = defineModel<string>('tab', {
        default: ''
    });

    function submit<ExtraData extends { [key: string]: any } = any>(extraData?: ExtraData) {
        const { form, postFn } = props;

        if (form.isUploading) {
            return;
        }

        loading.value = true;

        return postFn({ ...form.data, ...extraData })
            .catch(error => {
                if (error.response?.status === 422) {
                    props.form.onError(error.response.data.errors ?? {})
                }
                return Promise.reject(error);
            })
            .finally(() => {
                loading.value = false;
            });
    }

    function onLocaleChange(locale: string) {
        props.form.setAllMeta({ locale });
    }

    function onFieldLocaleChange(fieldKey: string, locale: string) {
        props.form.setMeta(fieldKey, { locale });
    }

    function onFieldUploading(fieldKey: string, uploading: boolean) {
        props.form.setMeta(fieldKey, { uploading });
    }

    const parentCommands = useParentCommands();
    const refresh = debounce((data) => {
        api.post(route('code16.sharp.api.form.refresh.update', {
            entityKey: props.form.entityKey,
            instance_id: props.form.instanceId,
            embed_key: props.form.embedKey,
            entity_list_command_key: parentCommands?.commandContainer === 'entityList' ? props.form.commandKey : null,
            show_command_key: parentCommands?.commandContainer === 'show' ? props.form.commandKey : null,
        }), data)
            .then(response => {
                merge(props.form.data, response.data.form.data);
            });
    }, 200);

    function onFieldInput(fieldKey: string, value: FormFieldData['value'], inputOptions: FormFieldEmitInputOptions = {}) {
        const data = {
            ...props.form.data,
            ...(!inputOptions.force ? getDependantFieldsResetData(props.form.fields, fieldKey) : null),
            [fieldKey]: value,
        };

        props.form.data = data;

        if((props.form.shouldRefresh(fieldKey) || inputOptions.shouldRefresh) && !inputOptions.skipRefresh) {
            refresh(data);
        }
    }

    const title = useTemplateRef<HTMLElement>('title');
    const titleScrollWidth = ref(0);

    useResizeObserver(title, (e) => {
        titleScrollWidth.value = e[0].target.scrollWidth;
    });

    defineExpose({ submit });
</script>

<template>
    <Tabs v-model="selectedTabSlug" :unmount-on-hide="false">
        <template v-if="showErrorAlert">
            <div :class="modal ? 'mb-8' : 'mb-4 container'">
                <Alert variant="destructive">
                    <template v-if="errorAlertMessage">
                        <AlertTitle v-if="!modal">
                            {{ __('sharp::modals.error.title') }}
                        </AlertTitle>
                        <AlertDescription>
                            {{ errorAlertMessage }}
                        </AlertDescription>
                    </template>
                    <template v-else-if="modal">
                        <AlertDescription>
                            {{ __('sharp::form.validation_error.title') }} {{ __('sharp::form.validation_error.description') }}
                        </AlertDescription>
                    </template>
                    <template v-else>
                        <AlertTitle>
                            {{ __('sharp::form.validation_error.title') }}
                        </AlertTitle>
                        <AlertDescription>
                            {{ __('sharp::form.validation_error.description') }}
                        </AlertDescription>
                    </template>
                </Alert>
            </div>
        </template>

        <template v-if="form.pageAlert">
            <div class="container" :class="{ 'px-0': modal }">
                <PageAlert
                    class="mb-4"
                    :page-alert="form.pageAlert"
                />
            </div>
        </template>

        <component :is="modal ? 'div' : RootCard">
            <template v-if="!modal || form.locales?.length || form.layout.tabs.length > 1">
                <component :is="modal ? 'div' : RootCardHeader"
                    :class="[
                        form.locales?.length || form.layout.tabs.length > 1 ? 'data-overflowing-viewport:sticky' : '',
                        modal ? '-mt-2 mb-6' : ''
                    ]"
                >
                    <div class="flex flex-wrap items-start gap-x-4 gap-y-4" :class="modal ? 'justify-start' : 'justify-end'">
                        <template v-if="!modal">
                            <div class="flex-1 flex min-w-[min(var(--scroll-width),min(18rem,100%))]" :style="{'--scroll-width':`${titleScrollWidth}px`}">
                                <CardTitle class="min-w-0 truncate py-1 -my-1" ref="title">
                                    <slot name="title" />
                                </CardTitle>
                            </div>
                        </template>
                        <template v-if="form.locales?.length || form.layout.tabs.length > 1">
                            <div class="flex min-w-0 gap-4" :class="!modal ? '-my-1' : ''">
                                <template v-if="form.locales?.length">
                                    <Select :model-value="form.currentLocale ?? undefined" @update:model-value="onLocaleChange">
                                        <div class="flex items-center" :class="!modal ? 'h-8' : ''">
                                            <LocaleSelectTrigger class="pointer-events-auto" />
                                        </div>
                                        <SelectContent>
                                            <template v-for="locale in form.locales" :key="locale">
                                                <SelectItem :value="locale">
                                                    <span class="uppercase text-xs">{{ locale }}</span>
                                                </SelectItem>
                                            </template>
                                        </SelectContent>
                                    </Select>
                                </template>
                                <template v-if="form.layout.tabs.length > 1">
                                    <div class="hidden @2xl/root-card:block min-w-0 overflow-auto scroll-px-2 -my-1">
                                        <TabsList>
                                            <template v-for="tab in form.layout.tabs">
                                                <TabsTrigger :value="slugify(tab.title)" v-scroll-into-view.nearest="slugify(tab.title) === selectedTabSlug">
                                                    {{ tab.title }}
                                                    <template v-if="form.tabErrorCount(tab)">
                                                        <Badge class="ml-2" variant="destructive">
                                                            {{ form.tabErrorCount(tab) }}
                                                        </Badge>
                                                    </template>
                                                </TabsTrigger>
                                            </template>
                                        </TabsList>
                                    </div>
                                    <Select v-model="selectedTabSlug">
                                        <SelectTrigger class="h-8 @2xl/root-card:hidden w-auto text-left pointer-events-auto">
                                            <Menu class="size-4 shrink-0 mr-2" />
                                            <SelectValue class="font-medium" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <template v-for="(tab, i) in form.layout.tabs">
                                                <SelectItem :value="slugify(tab.title)">
                                                    {{ tab.title }}
                                                </SelectItem>
                                            </template>
                                        </SelectContent>
                                    </Select>
                                </template>
                            </div>
                        </template>
                    </div>
                </component>
            </template>
            <CardContent :class="modal ? 'p-0!' : ''">
                <template v-for="tab in form.layout.tabs">
                    <TabsContent class="mt-0" :tabindex="form.layout.tabs.length > 1 ? 0 : -1" :value="slugify(tab.title)">
                        <div class="grid gap-8 grid-cols-1 @3xl/root-card:grid-cols-12">
                            <template v-for="column in tab.columns">
                                <div class="@3xl/root-card:col-[span_var(--size)]" :style="{ '--size': `${column.size}` }">
                                    <FieldGrid class="gap-6 gap-y-7">
                                        <template v-for="row in column.fields">
                                            <FieldGridRow v-show="form.fieldRowShouldBeVisible(row)">
                                                <template v-for="fieldLayout in row">
                                                    <template v-if="'legend' in fieldLayout">
                                                        <FieldGridColumn v-show="form.fieldsetShouldBeVisible(fieldLayout)">
                                                            <fieldset>
                                                                <Label class="mb-2.5" as="legend">
                                                                    {{ fieldLayout.legend }}
                                                                </Label>
                                                                <Card class="shadow-sm">
                                                                    <CardContent>
                                                                        <FieldGrid class="gap-6">
                                                                            <template v-for="row in fieldLayout.fields">
                                                                                <FieldGridRow v-show="form.fieldRowShouldBeVisible(row)">
                                                                                    <template v-for="fieldLayout in row">
                                                                                        <FieldGridColumn :layout="fieldLayout" v-show="form.fieldShouldBeVisible(fieldLayout)">
                                                                                            <SharpFormField
                                                                                                :field="form.getField(fieldLayout.key)"
                                                                                                :field-layout="fieldLayout"
                                                                                                :field-error-key="fieldLayout.key"
                                                                                                :value="form.data[fieldLayout.key]"
                                                                                                :locale="(form.getMeta(fieldLayout.key) as FieldMeta)?.locale ?? form.defaultLocale"
                                                                                                :parent-data="form.data"
                                                                                                :persist-thumbnail-url="props.persistThumbnailUrl"
                                                                                                :row="row"
                                                                                                root
                                                                                                @input="(value, options) => onFieldInput(fieldLayout.key, value, options)"
                                                                                                @locale-change="onFieldLocaleChange(fieldLayout.key, $event)"
                                                                                                @uploading="onFieldUploading(fieldLayout.key, $event)"
                                                                                            />
                                                                                        </FieldGridColumn>
                                                                                    </template>
                                                                                </FieldGridRow>
                                                                            </template>
                                                                        </FieldGrid>
                                                                    </CardContent>
                                                                </Card>
                                                            </fieldset>
                                                        </FieldGridColumn>
                                                    </template>
                                                    <template v-else>
                                                        <FieldGridColumn :layout="fieldLayout" v-show="form.fieldShouldBeVisible(fieldLayout)">
                                                            <SharpFormField
                                                                :field="form.getField(fieldLayout.key)"
                                                                :field-layout="fieldLayout"
                                                                :field-error-key="fieldLayout.key"
                                                                :value="form.data[fieldLayout.key]"
                                                                :locale="(form.getMeta(fieldLayout.key) as FieldMeta)?.locale ?? form.currentLocale"
                                                                :row="row as LayoutFieldData[]"
                                                                :parent-data="form.data"
                                                                :persist-thumbnail-url="props.persistThumbnailUrl"
                                                                root
                                                                @input="(value, options) => onFieldInput(fieldLayout.key, value, options)"
                                                                @locale-change="onFieldLocaleChange(fieldLayout.key, $event)"
                                                                @uploading="onFieldUploading(fieldLayout.key, $event)"
                                                            />
                                                        </FieldGridColumn>
                                                    </template>
                                                </template>
                                            </FieldGridRow>
                                        </template>
                                    </FieldGrid>
                                </div>
                            </template>
                        </div>
                    </TabsContent>
                </template>
            </CardContent>
            <template v-if="$slots.footer">
                <StickyBottom class="group sticky z-30 -bottom-2 lg:bottom-0 pointer-events-none" sentinel-class="bottom-2 lg:bottom-0">
                    <CardFooter class="justify-end px-0 pt-4">
                        <div class="relative pointer-events-auto">
                            <div class="absolute -inset-4 -bottom-6 @[64rem]:-inset-6 transition-[border-color] border-transparent border-l border-t rounded-tl-lg rounded-br-lg -z-10 group-data-[stuck]:bg-card group-data-[stuck]:border-border"
                            ></div>
                            <slot name="footer" />
                        </div>
                    </CardFooter>
                </StickyBottom>
            </template>
        </component>
    </Tabs>
</template>
