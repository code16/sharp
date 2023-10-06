<script setup lang="ts">
    import Field from "./Field.vue";
    import FormLayout from "./FormLayout.vue";
    import { FormFieldData, FormLayoutTabData } from "@/types";
    import PageAlert from "@/components/PageAlert.vue";
    import FieldColumn from "@/components/ui/FieldColumn.vue";
    import { provide, ref } from "vue";
    import { Form } from "../Form";
    import LocaleSelect from "./ui/LocaleSelect.vue";
    import { getDependantFieldsResetData } from "../util";
    import Grid from "@/components/ui/Grid.vue";
    import GridRow from "@/components/ui/GridRow.vue";
    import GridColumn from "@/components/ui/GridColumn.vue";

    const props = defineProps<{
        form: Form,
        entityKey: string,
        instanceId: string | number,
        postFn?: Function,
    }>();

    provide('form', props.form);
    provide('$form', props.form);

    const selectedLocale = ref(props.form.locales?.[0]);
    const loading = ref(false);

    function submit() {
        const { form, postFn } = props;

        if (form.isUploading) {
            return;
        }

        loading.value = true;

        postFn(form.serialize(form.data))
            .catch(error => {
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
        selectedLocale.value = locale;
        props.form.setAllMeta({ locale });
    }

    function onFieldLocaleChange(fieldKey: string, locale: string) {
        props.form.setMeta(fieldKey, { locale });
    }

    function onFieldUploading(fieldKey: string, uploading: boolean) {
        props.form.setMeta(fieldKey, { uploading });
    }

    function onFieldInput(fieldKey: string, value: FormFieldData['value'], { force = false } = {}) {
        props.form.data = {
            ...props.form.data,
            ...(!force ? getDependantFieldsResetData(props.form.fields, fieldKey) : null),
            [fieldKey]: value,
        }
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
                <LocaleSelect
                    outline
                    right
                    :locale="form.currentLocale"
                    :locales="form.locales"
                    @change="onLocaleChange"
                />
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
            <div class="grid gap-4 md:grid-cols-12">
                <template v-for="column in tab.columns">
                    <div class="col-[span_var(--size)]" :style="{ '--size': `${column.size}` }">
                        <Grid class="gap-y-4 gap-x-4">
                            <template v-for="row in column.fields">
                                <GridRow>
                                    <template v-for="fieldLayout in row">
                                        <template v-if="'legend' in fieldLayout">
                                            <GridColumn>
                                                <fieldset v-show="form.fieldsetShouldBeVisible(fieldLayout)">
                                                    <legend>
                                                        {{ fieldLayout.legend }}
                                                    </legend>
                                                    <div class="bg-white p-4">
                                                        <Grid class="gap-y-4 gap-x-4">
                                                            <template v-for="row in fieldLayout.fields">
                                                                <GridRow>
                                                                    <template v-for="fieldLayout in row">
                                                                        <FieldColumn :layout="fieldLayout">
                                                                            <Field
                                                                                :field="form.getField(fieldLayout.key)"
                                                                                :field-layout="fieldLayout"
                                                                                :field-error-key="fieldLayout.key"
                                                                                :value="form.data[fieldLayout.key]"
                                                                                :locale="form.getMeta(fieldLayout.key)?.locale ?? selectedLocale"
                                                                                @input="(value, options) => onFieldInput(fieldLayout.key, value, options)"
                                                                                @locale-change="onFieldLocaleChange(fieldLayout.key, $event)"
                                                                                @uploading="onFieldUploading(fieldLayout.key, $event)"
                                                                            />
                                                                        </FieldColumn>
                                                                    </template>
                                                                </GridRow>
                                                            </template>
                                                        </Grid>
                                                    </div>
                                                </fieldset>
                                            </GridColumn>
                                        </template>
                                        <template v-else>
                                            <FieldColumn :layout="fieldLayout" v-show="form.fieldShouldBeVisible(fieldLayout)">
                                                <Field
                                                    :field="form.getField(fieldLayout.key)"
                                                    :field-layout="fieldLayout"
                                                    :field-error-key="fieldLayout.key"
                                                    :value="form.data[fieldLayout.key]"
                                                    :locale="form.getMeta(fieldLayout.key)?.locale ?? selectedLocale"
                                                    @input="(value, options) => onFieldInput(fieldLayout.key, value, options)"
                                                    @locale-change="onFieldLocaleChange(fieldLayout.key, $event)"
                                                    @uploading="onFieldUploading(fieldLayout.key, $event)"
                                                />
                                            </FieldColumn>
                                        </template>
                                    </template>
                                </GridRow>
                            </template>
                        </Grid>
                    </div>
                </template>
            </div>
        </FormLayout>

        <slot name="append" />
    </div>
</template>
