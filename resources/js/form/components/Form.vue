<script setup lang="ts">
    import { FormData, FormFieldData,  LayoutFieldData } from "@/types";
    import PageAlert from "@/components/PageAlert.vue";
    import { provide, ref } from "vue";
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
    import { FieldMeta } from "@/form/types";
    import StickyTop from "@/components/StickyTop.vue";
    import StickyBottom from "@/components/StickyBottom.vue";
    import { Menu } from 'lucide-vue-next';

    const props = defineProps<{
        form: Form
        inline?: boolean,
        postFn?: (data: FormData['data']) => Promise<ApiResponse<any>>,
        showErrorAlert?: boolean,
    }>();

    provide('form', props.form);

    const loading = ref(false);
    const { selectedTabSlug } = useFormTabs(props);

    function submit() {
        const { form, postFn } = props;

        if (form.isUploading) {
            return;
        }

        loading.value = true;

        return postFn(form.serializedData)
            .catch(error => {
                console.log('handled', error);
                if (error.response?.status === 422) {
                    props.form.errors = error.response.data.errors ?? {};
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

    function onFieldInput(fieldKey: string, value: FormFieldData['value'], { force = false } = {}) {
        const data = {
            ...props.form.data,
            ...(!force ? getDependantFieldsResetData(props.form.fields, fieldKey) : null),
            [fieldKey]: value,
        };

        props.form.data = data;
        props.form.serializedData = data;
    }

    defineExpose({ submit });
</script>

<template>
    <Tabs v-model="selectedTabSlug" :unmount-on-hide="false">
        <template v-if="showErrorAlert">
            <div class="container">
                <Alert class="mb-4" variant="destructive">
                    <AlertTitle>
                        {{ __('sharp::form.validation_error.title') }}
                    </AlertTitle>
                    <AlertDescription>
                        {{ __('sharp::form.validation_error.description') }}
                    </AlertDescription>
                </Alert>
            </div>
        </template>
        <template v-if="form.locales?.length || form.layout.tabbed && form.layout.tabs.length > 1">
            <StickyTop class="@container relative group flex items-end container mb-4 pointer-events-none overflow-x-clip lg:sticky lg:top-3 data-[stuck]:z-20"
                v-slot="{ stuck, isOverflowing }"
            >
                <div class="flex-1">
                    <template v-if="form.locales?.length">
                        <Select :model-value="form.currentLocale ?? undefined" @update:model-value="onLocaleChange">
                            <LocaleSelectTrigger class="mr-4 pointer-events-auto lg:ml-[--sticky-safe-left-offset]" />
                            <SelectContent>
                                <template v-for="locale in form.locales" :key="locale">
                                    <SelectItem :value="locale">
                                        <span class="uppercase text-xs">{{ locale }}</span>
                                    </SelectItem>
                                </template>
                            </SelectContent>
                        </Select>
                    </template>
                </div>
                <template v-if="form.layout.tabbed && form.layout.tabs.length > 1">
                    <div class="">
                        <div class="hidden lg:h-8 col-start-1 row-start-1 @2xl:flex flex-col justify-end group-data-[stuck]:!hidden group-data-[overflowing]:opacity-0"
                            :inert="isOverflowing"
                        >
                            <TabsList class="pointer-events-auto">
                                <template v-for="tab in form.layout.tabs">
                                    <TabsTrigger :value="slugify(tab.title)">
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
                        <div class="@2xl:hidden group-data-[stuck]:!block group-data-[overflowing]:!block"
                            :class="{ '@2xl:absolute @2xl:bottom-0 @2xl:left-1/2 @2xl:-translate-x-1/2': isOverflowing && !stuck }"
                        >
                            <div class="flex items-center">
                                <Select v-model="selectedTabSlug">
                                    <SelectTrigger class="h-8 w-auto text-left pointer-events-auto">
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
                            </div>
                        </div>
                    </div>
                </template>
                <div class="flex-1"></div>
            </StickyTop>
        </template>

        <component :is="inline ? 'div' : RootCard">
            <template v-if="!inline">
                <CardHeader>
                    <div class="flex items-start gap-x-4">
                        <CardTitle class="-mt-0.5 flex-1 truncate text-2xl/7">
                            <slot name="title" />
                        </CardTitle>
                    </div>
                </CardHeader>
            </template>
            <CardContent :class="inline ? '!p-0' : ''">
                <template v-if="form.pageAlert">
                    <PageAlert
                        class="mb-4"
                        :page-alert="form.pageAlert"
                    />
                </template>

                <template v-for="tab in form.layout.tabs">
                    <TabsContent class="mt-0" :tabindex="form.layout.tabs.length > 1 ? 0 : -1" :value="slugify(tab.title)">
                        <div class="grid gap-6 grid-cols-1 @3xl/root-card:grid-cols-12">
                            <template v-for="column in tab.columns">
                                <div class="@3xl/root-card:col-[span_var(--size)]" :style="{ '--size': `${column.size}` }">
                                    <FieldGrid class="gap-6">
                                        <template v-for="row in column.fields">
                                            <FieldGridRow v-show="form.fieldRowShouldBeVisible(row)">
                                                <template v-for="fieldLayout in row">
                                                    <template v-if="'legend' in fieldLayout">
                                                        <FieldGridColumn v-show="form.fieldsetShouldBeVisible(fieldLayout)">
                                                            <Card>
                                                                <CardHeader>
                                                                    <CardTitle class="text-sm font-semibold">
                                                                        {{ fieldLayout.legend }}
                                                                    </CardTitle>
                                                                </CardHeader>
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
                            <div class="absolute -inset-4 -bottom-6 lg:-inset-6 transition-[border-color] border-transparent border-l border-t rounded-tl-lg rounded-br-lg -z-10 group-data-[stuck]:bg-card group-data-[stuck]:border-border"
                            ></div>
                            <slot name="footer" />
                        </div>
                    </CardFooter>
                </StickyBottom>
            </template>
        </component>
    </Tabs>
</template>
