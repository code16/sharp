<script setup lang="ts">
    import FormLayout from "./FormLayout.vue";
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
    import { Languages } from "lucide-vue-next";
    import { ApiResponse } from "@/api/types";

    const props = defineProps<{
        form: Form
        entityKey: string,
        instanceId?: string | number,
        postFn?: (data: FormData['data']) => Promise<ApiResponse<any>>,
    }>();

    provide('form', props.form);

    const loading = ref(false);

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
    <div class="SharpForm">
        <div class="flex">
            <div class="flex-1">
                <slot name="title" />
            </div>
            <template v-if="form.locales?.length">
                <Select class="w-auto" :model-value="form.currentLocale" @update:model-value="onLocaleChange">
                    <SelectTrigger>
                        <Languages class="w-4 h-4" />
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <template v-for="locale in form.locales" :key="locale">
                            <SelectItem :value="locale">
                                <span class="text-uppercase">{{ locale }}</span>
                            </SelectItem>
                        </template>
                    </SelectContent>
                </Select>
            </template>
        </div>

        <template v-if="form.pageAlert">
            <PageAlert
                class="mb-3"
                :page-alert="form.pageAlert"
            />
        </template>

        <slot name="prepend" />

        <FormLayout :form="form" v-slot="{ tab }: { tab: FormLayoutTabData }">
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
                                                                        <FieldGridColumn :layout="fieldLayout">
                                                                            <SharpFormField
                                                                                :field="form.getField(fieldLayout.key)"
                                                                                :field-layout="fieldLayout"
                                                                                :field-error-key="fieldLayout.key"
                                                                                :value="form.data[fieldLayout.key]"
                                                                                :locale="form.getMeta(fieldLayout.key)?.locale ?? form.currentLocale"
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
                                            <FieldGridColumn :layout="fieldLayout" v-show="form.fieldShouldBeVisible(fieldLayout)">
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
        </FormLayout>

        <slot name="append" />
    </div>
</template>
