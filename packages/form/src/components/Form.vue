<script setup lang="ts">
    import FormLayout from "./ui/FormLayout.vue";
    import { FormFieldData, FormLayoutTabData } from "@/types";
    import PageAlert from "@/components/PageAlert.vue";
    import FieldColumn from "@/components/ui/FieldColumn.vue";
    import { inject, ref } from "vue";
    import { Form } from "../Form";
    import FieldContainer from "./ui/FieldContainer.vue";
    import LocaleSelect from "./ui/LocaleSelect.vue";
    import { getDependantFieldsResetData } from "../util";
    import { showAlert } from "@/utils/dialogs";

    const props = defineProps<{
        form: Form,
        entityKey: string,
        instanceId: string | number,
        postFn: Function,
    }>();

    inject('form', props.form);
    inject('$form', props.form);

    const locale = ref(props.form.locales?.[0]);
    const loading = ref(false);

    function onLocaleChange(nextLocale: string) {
        locale.value = nextLocale;
        props.form.clearMeta('locale');
    }

    function onFieldLocaleChange(fieldKey: string, locale: string) {
        props.form.setMeta(fieldKey, { locale });
    }

    function onFieldUploading(fieldKey: string, uploading: boolean) {
        props.form.setMeta(fieldKey, { uploading });
    }

    function onInput(fieldKey: string, value: FormFieldData['value'], { force = false } = {}) {
        props.form.data = {
            ...props.form.data,
            ...(!force ? getDependantFieldsResetData(props.form.fields, fieldKey) : null),
            [fieldKey]: value,
        }
    }
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
            <div class="flex -mx-4">
                <template v-for="column in tab.columns">
                    <div class="w-[calc(12/var(--size)*100%)] px-4" :style="{ '--size': `${column.size}` }">
                        <template v-for="row in column.fields">
                            <div class="flex -mx-4">
                                <template v-for="fieldLayout in row">
                                    <template v-if="'legend' in fieldLayout">
                                        <fieldset v-show="form.fieldsetShouldBeVisible(fieldLayout)">
                                            <legend>
                                                {{ fieldLayout.legend }}
                                            </legend>

                                            <div class="bg-white p-4">
                                                <template v-for="row in fieldLayout.fields">
                                                    <div class="flex -mx-4">
                                                        <template v-for="fieldLayout in row">
                                                            <FieldColumn class="px-4" :layout="fieldLayout">
                                                                <FieldContainer
                                                                    :field="form.getField(fieldLayout.key)"
                                                                    :value="form.data[fieldLayout.key]"
                                                                    :locale="form.meta[fieldLayout.key]?.locale ?? locale"
                                                                    :field-error-key="fieldLayout.key"
                                                                    :form="form"
                                                                    @input="onInput(fieldLayout.key, $event)"
                                                                    @locale-change="onFieldLocaleChange(fieldLayout.key, $event)"
                                                                    @uploading="onFieldUploading(fieldLayout.key, $event)"
                                                                />
                                                            </FieldColumn>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </fieldset>
                                    </template>
                                    <template v-else>
                                        <FieldColumn class="px-4" :layout="fieldLayout">
                                            <FieldContainer
                                                :field="form.getField(fieldLayout.key)"
                                                :value="form.data[fieldLayout.key]"
                                                :locale="form.meta[fieldLayout.key]?.locale ?? locale"
                                                :field-error-key="fieldLayout.key"
                                                :form="form"
                                                @input="onInput(fieldLayout.key, $event)"
                                                @locale-change="onFieldLocaleChange(fieldLayout.key, $event)"
                                                @uploading="onFieldUploading(fieldLayout.key, $event)"
                                            />
                                        </FieldColumn>
                                    </template>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </FormLayout>

        <slot name="append" :loading="loading" />
    </div>
</template>

<script lang="ts">

    export default {
        methods: {
            serialize(data = this.data) {
                return Object.fromEntries(
                    Object.entries(data ?? {})
                        .filter(([key]) => this.fields[key]?.type !== 'html')
                );
            },
            async submit() {
                if (this.isUploading) {
                    return;
                }

                if(!this.postFn) {
                    this.$emit('submit', this.serialize());
                    return;
                }

                this.setLoading(true);

                const data = this.serialize();

                this.postFn(data)
                    .catch(error => {
                        if (error.response?.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                        return Promise.reject(error);
                    })
                    .finally(() => {
                        this.setLoading(false);
                    });
            },

        },
    }
</script>
