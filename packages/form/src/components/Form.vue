<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import FormLayout from "./ui/FormLayout.vue";
    import { FormData, FormLayoutTabData } from "@/types";
    import PageAlert from "@/components/PageAlert.vue";
    import FieldColumn from "@/components/ui/FieldColumn.vue";
    import { computed, ref } from "vue";
    import { Form } from "../Form";
    import FieldContainer from "./ui/FieldContainer.vue";

    const props = defineProps<{
        form: FormData,
        entityKey: string,
        instanceId: string | number,
        formErrors: Record<string, string[]>,
        postFn: Function,
        isPage: boolean,
    }>();

    const form = computed(() => new Form(props.form));
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
                    :locale="currentLocale"
                    :locales="form.locales"
                    @change="handleLocaleChanged"
                />
            </template>
        </div>

        <template v-if="form.pageAlert">
            <PageAlert
                class="mb-3"
                :page-alert="form.pageAlert"
            />
        </template>

        <template v-if="hasErrors && isPage">
            <div class="alert alert-danger SharpForm__alert" role="alert">
                <div class="fw-bold">{{ __('sharp::form.validation_error.title') }}</div>
                <div>{{ __('sharp::form.validation_error.description') }}</div>
            </div>
        </template>

        <FormLayout :layout="form.layout" v-slot="{ tab }: { tab: FormLayoutTabData }">
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
                                                                    :field="{ readOnly, ...fields[fieldLayout.key] }"
                                                                    :value="data[fieldLayout.key]"
                                                                    :locale="fieldLocale[fieldLayout.key]"
                                                                    :form="form"
                                                                    :update-data="updateData"
                                                                    @locale-change="updateLocale"
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
                                                :field="{ readOnly, ...fields[fieldLayout.key] }"
                                                :value="data[fieldLayout.key]"
                                                :locale="fieldLocale[fieldLayout.key]"
                                                :form="form"
                                                :update-data="updateData"
                                                @locale-change="updateLocale"
                                            />
                                        </FieldColumn>
                                    </template>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
<!--                <Grid :rows="[tab.columns]" ref="columnsGrid" v-slot="{ itemLayout:column }">-->
<!--                    <FieldsLayout-->
<!--                        :layout="column.fields"-->
<!--                        :visible="fieldVisible"-->
<!--                        ref="fieldLayout"-->
<!--                        v-slot="{ fieldLayout }"-->
<!--                    >-->
<!--                        <FieldDisplay-->
<!--                            :field-key="fieldLayout.key"-->
<!--                            :context-fields="transformedFields"-->
<!--                            :context-data="form.data"-->
<!--                            :field-layout="fieldLayout"-->
<!--                            :locale="fieldLocale[fieldLayout.key]"-->
<!--                            :read-only="isReadOnly"-->
<!--                            :error-identifier="fieldLayout.key"-->
<!--                            :config-identifier="fieldLayout.key"-->
<!--                            root-->
<!--                            :update-data="updateData"-->
<!--                            :update-visibility="updateVisibility"-->
<!--                            @locale-change="updateLocale"-->
<!--                            ref="field"-->
<!--                        />-->
<!--                    </FieldsLayout>-->
<!--                </Grid>-->
        </FormLayout>

        <template v-if="isPage">
            <div class="position-sticky bottom-0 px-4 py-3 bg-white border-top"
                :class="{ 'shadow': stuck }"
                v-sticky
                @stuck-change="stuck = $event.detail"
                style="z-index: 100; transition: box-shadow .25s ease-in-out"
            >
                <div class="row justify-content-end align-items-center gx-3">
                    <div class="col">
                        <slot name="left"></slot>
                    </div>
                    <div class="col-auto">
                        <Button :href="$page.props.breadcrumb.items.at(-2)?.url" outline>
                            <template v-if="isReadOnly">
                                {{ __('sharp::action_bar.form.back_button') }}
                            </template>
                            <template v-else>
                                {{ __('sharp::action_bar.form.cancel_button') }}
                            </template>
                        </Button>
                    </div>
                    <template v-if="isCreation ? form.authorizations.create : form.authorizations.update">
                        <div class="col-auto">
                            <Button style="min-width: 6.5em" :disabled="isUploading || loading" @click="handleSubmitClicked">
                                <template v-if="isUploading">
                                    {{ __('sharp::action_bar.form.submit_button.pending.upload') }}
                                </template>
                                <template v-else-if="isCreation">
                                    {{ __('sharp::action_bar.form.submit_button.create') }}
                                </template>
                                <template v-else>
                                    {{ __('sharp::action_bar.form.submit_button.update') }}
                                </template>
                            </Button>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</template>

<script lang="ts">
    import {
        showAlert,
    } from "sharp";

    import { Button, Dropdown, DropdownItem,  Grid } from '@sharp/ui';
    import FieldDisplay from "./FieldDisplay.vue";

    import FieldsLayout from './ui/FieldsLayout.vue';
    import LocaleSelect from './ui/LocaleSelect.vue';
    import localize from '../mixins/localize/form';

    import { getDependantFieldsResetData, transformFields } from "../util";
    import BottomBar from "./BottomBar.vue";

    const isLocal = Symbol('isLocal');

    export default {
        name: 'SharpForm',
        // extends: DynamicView,

        mixins: [localize('fields')],

        components: {
            BottomBar,
            Button,
            FieldsLayout,
            Grid,
            Dropdown,
            DropdownItem,
            LocaleSelect,
            FieldDisplay,
        },

        provide() {
            return {
                $form: this
            }
        },

        data() {
            return {
                ready: false,

                fields: null,
                authorizations: null,
                breadcrumb: null,
                config: null,

                errors: {},
                fieldLocale: {},
                locales: null,
                loading: false,

                fieldVisible: {},
                uploadingFields: {},
            }
        },
        watch: {
            formErrors() {
                this.errors = { ...this.formErrors };
            },
        },
        computed: {
            localized() {
                return Array.isArray(this.locales) && !!this.locales.length;
            },
            isSingle() {
                return this.config
                    ? this.config.isSingle
                    : false
            },
            isCreation() {
                return !this.isSingle && !this.instanceId;
            },
            isReadOnly() {
                if (!this.authorizations) {
                    return false;
                }
                return this.isCreation
                    ? !this.authorizations.create
                    : !this.authorizations.update;
            },
            hasErrors() {
                return Object.values(this.errors).some(error => !!error && !error[isLocal]);
            },
            transformedFields() {
                return transformFields(this.fields, this.data);
            },
            currentLocale() {
                const flattened = Object.values(this.fieldLocale)
                    .map(locale => Array.isArray(locale)
                        ? locale.map(itemLocale => Object.values(itemLocale))
                        : locale)
                    .flat(2);
                const locales = [...new Set(flattened)];
                if (!locales.length) {
                    return this.locales?.[0]
                }
                return locales.length === 1 ? locales[0] : null;
            },
            isUploading() {
                return Object.values(this.uploadingFields)
                    .some(uploading => !!uploading);
            },
            mergedErrorIdentifier() {
                return null;
            },
            mergedConfigIdentifier() {
                return null;
            },
        },
        methods: {
            async updateData(key, value, { forced } = {}) {
                this.data = {
                    ...this.data,
                    ...(!forced ? getDependantFieldsResetData(this.fields, key,
                        field => this.fieldLocalizedValue(field.key, null),
                    ) : null),
                    [key]: this.fieldLocalizedValue(key, value),
                }
            },
            updateVisibility(key, visibility) {
                this.fieldVisible[key] = visibility;
            },
            updateLocale(key, locale) {
                this.fieldLocale[key] = locale;
            },
            updateFieldError(key, error) { // used in FieldContainer
                if(error) {
                    error[isLocal] = true;
                }
                this.errors = {
                    ...this.errors,
                    [key]: error,
                };
            },
            handleLocaleChanged(locale) {
                this.fieldLocale = this.defaultFieldLocaleMap({ fields: this.fields, locales: this.locales }, locale);
            },
            mount({ fields, layout, data, authorizations, locales, breadcrumb, config }) {
                this.fields = fields;
                this.data = data ?? {};
                this.layout = layout;
                this.locales = locales;
                this.authorizations = authorizations ?? {};
                this.breadcrumb = breadcrumb;
                this.config = config ?? {};

                if (fields) {
                    this.fieldVisible = Object.keys(this.fields).reduce((res, fKey) => {
                        res[fKey] = true;
                        return res;
                    }, {});
                    this.fieldLocale = this.defaultFieldLocaleMap({ fields, locales });
                }
                this.validate();
            },
            validate() {
                const localizedFields = Object.keys(this.fieldLocale);
                const alert = text => showAlert(text, {
                    title: 'Data error',
                    isError: true,
                });
                if (localizedFields.length > 0 && !this.locales.length) {
                    alert("Some fields are localized but the form hasn't any locales configured");
                }
            },
            handleError(error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors || {};
                }
                return Promise.reject(error);
            },

            serialize(data = this.data) {
                return Object.fromEntries(
                    Object.entries(data ?? {})
                        .filter(([key]) => this.fields[key]?.type !== 'html')
                );
            },
            setLoading(loading) {
                this.$emit('loading', loading);
                this.loading = loading;
            },
            init() {
                this.mount(this.form);
                this.ready = true;
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
                    .catch(this.handleError)
                    .finally(() => {
                        this.setLoading(false);
                    });
            },
            handleSubmitClicked() {
                this.submit().catch(() => {
                });
            },
            handleCancelClicked() {
                this.redirectToParentPage();
            },

            // Used by VueClip as injection
            setUploading(fieldKey, uploading) {
                this.uploadingFields = {
                    ...this.uploadingFields,
                    [fieldKey]: uploading
                }
            },

            // Used by List field as injection
            hasUploadingFields(listKey) {
                return Object.entries(this.uploadingFields)
                    .some(([fieldKey, isUploading]) => {
                        return fieldKey.startsWith(`${listKey}.`) && isUploading;
                    });
            },
        },
        created() {
            this.init();
        },
    }
</script>
