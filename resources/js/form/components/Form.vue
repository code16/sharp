<script setup lang="ts">
    import { FormData, FormFieldData, FormLayoutTabData, LayoutFieldData } from "@/types";
    import PageAlert from "@/components/PageAlert.vue";
    import { provide, ref } from "vue";
    import { Form } from "../Form";
    import { getDependantFieldsResetData } from "../util";
    import FieldGrid from "@/components/ui/FieldGrid.vue";
    import FieldGridRow from "@/components/ui/FieldGridRow.vue";
    import FieldGridColumn from "@/components/ui/FieldGridColumn.vue";
    import { Serializable } from "@/form/Serializable";
    import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
    import { ApiResponse } from "@/api/types";
    import { __ } from "@/utils/i18n";
    import { CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
    import RootCard from "@/components/ui/RootCard.vue";
    import LocaleSelectTrigger from "@/components/LocaleSelectTrigger.vue";
    import { useFormTabs } from "@/form/components/useFormTabs";
    import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
    import { slugify } from "@/utils";
    import { Badge } from "@/components/ui/badge";
    import { UseElementBounding } from "@vueuse/components";

    const props = defineProps<{
        form: Form
        entityKey: string,
        instanceId?: string | number,
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

        return postFn(form.data)
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
        const data = Serializable.wrap(value, value => ({
            ...props.form.data,
            ...(!force ? getDependantFieldsResetData(props.form.fields, fieldKey) : null),
            [fieldKey]: value,
        }));

        props.form.data = data.localValue;
        props.form.serializedData = data.serialized;
    }

    defineExpose({ submit });
</script>

<template>
    <Tabs v-model="selectedTabSlug">
        <template v-if="form.locales?.length">
            <div class="container mb-4">
                <template v-if="form.locales?.length">
                    <Select :model-value="form.currentLocale ?? undefined" @update:model-value="onLocaleChange">
                        <LocaleSelectTrigger />
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
        </template>
        <component :is="inline ? 'div' : RootCard">
            <template v-if="!inline">
                <CardHeader>
                    <div class="flex items-start gap-x-4">
                        <CardTitle class="-mt-0.5 flex-1 truncate text-2xl/7">
                            <slot name="title" />
                        </CardTitle>
                        <template v-if="form.layout.tabbed && form.layout.tabs.length > 1">
                            <div>
                                <div class="@3xl/root-card:hidden">
                                    <Select v-model="selectedTabSlug">
                                        <SelectTrigger>
                                            <SelectValue />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <template v-for="tab in form.layout.tabs">
                                                <SelectItem :value="slugify(tab.title)">
                                                    {{ tab.title }}
                                                </SelectItem>
                                            </template>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div class="hidden @3xl/root-card:block">
                                    <TabsList>
                                        <template v-for="tab in form.layout.tabs">
                                            <TabsTrigger :value="slugify(tab.title)">
                                                {{ tab.title }}
                                                <template v-if="form.tabErrorsCount(tab)">
                                                    <Badge variant="destructive">
                                                        {{ form.tabErrorsCount(tab) }}
                                                    </Badge>
                                                </template>
                                            </TabsTrigger>
                                        </template>
                                    </TabsList>
                                </div>
                            </div>
                            <div class="flex-1 hidden @4xl/root-card:block"></div>
                        </template>
                    </div>
                </CardHeader>
            </template>
            <CardContent :class="inline ? 'p-0' : ''">
                <template v-if="form.pageAlert">
                    <PageAlert
                        class="mb-3"
                        :page-alert="form.pageAlert"
                    />
                </template>

                <template v-if="showErrorAlert">
                    <div class="alert alert-danger SharpForm__alert" role="alert">
                        <div class="fw-bold">{{ __('sharp::form.validation_error.title') }}</div>
                        <div>{{ __('sharp::form.validation_error.description') }}</div>
                    </div>
                </template>

                <template v-for="tab in form.layout.tabs">
                    <TabsContent class="mt-0" :value="slugify(tab.title)">
                        <div class="grid gap-6 md:grid-cols-12">
                            <template v-for="column in tab.columns">
                                <div class="col-[span_var(--size)]" :style="{ '--size': `${column.size}` }">
                                    <FieldGrid class="gap-6">
                                        <template v-for="row in column.fields">
                                            <FieldGridRow>
                                                <template v-for="fieldLayout in row">
                                                    <template v-if="'legend' in fieldLayout">
                                                        <FieldGridColumn>
                                                            <fieldset v-show="form.fieldsetShouldBeVisible(fieldLayout)">
                                                                <legend>
                                                                    {{ fieldLayout.legend }}
                                                                </legend>
                                                                <div class="bg-white p-4">
                                                                    <FieldGrid class="gap-6">
                                                                        <template v-for="row in fieldLayout.fields">
                                                                            <FieldGridRow>
                                                                                <template v-for="fieldLayout in row">
                                                                                    <FieldGridColumn :layout="fieldLayout" :class="{ '!hidden': !form.fieldShouldBeVisible(fieldLayout) }">
                                                                                        <SharpFormField
                                                                                            :field="form.getField(fieldLayout.key)"
                                                                                            :field-layout="fieldLayout"
                                                                                            :field-error-key="fieldLayout.key"
                                                                                            :value="form.data[fieldLayout.key]"
                                                                                            :locale="form.getMeta(fieldLayout.key)?.locale ?? form.defaultLocale"
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
                                                                </div>
                                                            </fieldset>
                                                        </FieldGridColumn>
                                                    </template>
                                                    <template v-else>
                                                        <FieldGridColumn :layout="fieldLayout" :class="{ '!hidden': !form.fieldShouldBeVisible(fieldLayout) }">
                                                            <SharpFormField
                                                                :field="form.getField(fieldLayout.key)"
                                                                :field-layout="fieldLayout"
                                                                :field-error-key="fieldLayout.key"
                                                                :value="form.data[fieldLayout.key]"
                                                                :locale="form.getMeta(fieldLayout.key)?.locale ?? form.currentLocale"
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
                <UseElementBounding class="sticky z-30 -bottom-2 lg:-bottom-0" v-slot="{ bottom }">
                    <CardFooter class="justify-end px-0">
                        <div class="relative">
                            <div class="bg-card absolute -inset-4 -bottom-6 lg:-inset-6 transition rounded-tl-lg border border-b-0 border-r-0 rounded-br-lg -z-10"
                                :class="bottom >= window.innerHeight ? '' : 'border-transparent'" :data-b="[bottom,window.innerHeight]"></div>
                            <slot name="footer" />
                        </div>
                    </CardFooter>
                </UseElementBounding>
            </template>
        </component>
    </Tabs>
</template>
