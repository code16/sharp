<template>
    <div class="SharpForm" data-popover-boundary>
        <template v-if="ready">
            <slot
                name="action-bar"
                :props="actionBarProps"
                :listeners="actionBarListeners"
            />

            <template v-if="hasErrors">
                <div class="alert alert-danger SharpForm__alert" role="alert">
                    <div class="fw-bold">{{ l('form.validation_error.title') }}</div>
                    <div>{{ l('form.validation_error.description') }}</div>
                </div>
            </template>

            <TabbedLayout :layout="layout" ref="tabbedLayout">
                <template v-if="localized" v-slot:nav-prepend>
                    <LocaleSelect
                        :locale="currentLocale"
                        :locales="locales"
                        @change="handleLocaleChanged"
                    />
                </template>
                <template v-slot:default="{ tab }">
                    <Grid :rows="[tab.columns]" ref="columnsGrid" v-slot="{ itemLayout:column }">
                        <FieldsLayout
                            :layout="column.fields"
                            :visible="fieldVisible"
                            ref="fieldLayout"
                            v-slot="{ fieldLayout }"
                        >
                            <FieldDisplay
                                :field-key="fieldLayout.key"
                                :context-fields="transformedFields"
                                :context-data="data"
                                :field-layout="fieldLayout"
                                :locale="fieldLocale[fieldLayout.key]"
                                :read-only="isReadOnly"
                                :error-identifier="fieldLayout.key"
                                :config-identifier="fieldLayout.key"
                                root
                                :update-data="updateData"
                                :update-visibility="updateVisibility"
                                @locale-change="updateLocale"
                                ref="field"
                            />
                        </FieldsLayout>
                    </Grid>
                </template>
            </TabbedLayout>
        </template>
    </div>
</template>

<script>
    import {
        getBackUrl,
        logError,
        showAlert,
    } from "sharp";

    import { TabbedLayout, Grid, Dropdown, DropdownItem, } from 'sharp-ui';
    import { Localization, DynamicView } from 'sharp/mixins';

    import FieldsLayout from './ui/FieldsLayout';
    import LocaleSelect from './ui/LocaleSelect';
    import localize from '../mixins/localize/form';

    import { getDependantFieldsResetData, transformFields } from "../util";


    const noop = ()=>{};

    export default {
        name:'SharpForm',
        extends: DynamicView,

        mixins: [ Localization, localize('fields')],

        components: {
            TabbedLayout,
            FieldsLayout,
            Grid,
            Dropdown,
            DropdownItem,
            LocaleSelect,
        },

        props:{
            entityKey: String,
            instanceId: String,

            /// Extras props for customization
            independant: {
                type:Boolean,
                default: false
            },
            ignoreAuthorizations: Boolean,
            props: Object
        },

        provide() {
            return {
                $form:this
            }
        },

        data() {
            return {
                ready: false,

                fields: null,
                authorizations: null,
                breadcrumb: null,
                config: null,

                errors:{},
                fieldLocale: {},
                locales: null,

                fieldVisible: {},
                uploadingFields: {},
                curFieldsetId:0,
            }
        },
        computed: {
            apiPath() {
                let path = `form/${this.entityKey}`;
                if(this.instanceId) path+=`/${this.instanceId}`;
                return path;
            },
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
                if(this.ignoreAuthorizations) {
                    return false;
                }
                return this.isCreation
                    ? !this.authorizations.create
                    : !this.authorizations.update;
            },
            // don't show loading on creation
            synchronous() {
                return this.independant;
            },
            hasErrors() {
                return Object.values(this.errors).some(error => !!error);
            },

            baseEntityKey() {
                return this.entityKey.split(':')[0];
            },

            downloadLinkBase() {
                return `/download/${this.entityKey}/${this.instanceId}`;
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
                if(!locales.length) {
                    return this.locales?.[0]
                }
                return locales.length === 1 ? locales[0] : null;
            },
            isUploading() {
                return Object.values(this.uploadingFields)
                    .some(uploading => !!uploading);
            },
            actionBarProps() {
                return {
                    showSubmitButton: this.isCreation
                        ? !!this.authorizations.create
                        : !!this.authorizations.update,
                    showDeleteButton: !this.isCreation && !this.isSingle && !!this.authorizations.delete,
                    showBackButton: this.isReadOnly,
                    create: !!this.isCreation,
                    uploading: this.isUploading,
                    breadcrumb: this.breadcrumb?.items,
                    showBreadcrumb: !!this.breadcrumb?.visible,
                }
            },
            actionBarListeners() {
                return {
                    'submit': this.handleSubmitClicked,
                    'delete': this.handleDeleteClicked,
                    'cancel': this.handleCancelClicked,
                }
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
                this.$set(this.fieldVisible, key, visibility);
            },
            updateLocale(key, locale) {
                this.$set(this.fieldLocale, key, locale);
            },
            handleLocaleChanged(locale) {
                this.fieldLocale = this.defaultFieldLocaleMap({ fields: this.fields, locales: this.locales }, locale);
            },
            mount({ fields, layout, data={}, authorizations={}, locales, breadcrumb, config }) {
                this.fields = fields;
                this.data = data;
                this.layout = this.patchLayout(layout);
                this.locales = locales;
                this.authorizations = authorizations;
                this.breadcrumb = breadcrumb;
                this.config = config;

                if(fields) {
                    this.fieldVisible = Object.keys(this.fields).reduce((res, fKey) => {
                        res[fKey] = true;
                        return res;
                    },{});
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
                if(localizedFields.length > 0 && !this.locales.length) {
                    alert("Some fields are localized but the form hasn't any locales configured");
                }
            },
            handleError(error) {
                if(error.response?.status === 422) {
                    this.errors = error.response.data.errors || {};
                }
                return Promise.reject(error);
            },

            patchLayout(layout) {
                if(!layout)return;
                let curFieldsetId = 0;
                let mapFields = layout => {
                    if(layout.legend)
                        layout.id = `${curFieldsetId++}#${layout.legend}`;
                    else if(layout.fields)
                        layout.fields.forEach(row => {
                            row.forEach(mapFields);
                        });
                };
                layout.tabs.forEach(t => t.columns.forEach(mapFields));
                return layout;
            },

            serialize(data = this.data) {
                return Object.fromEntries(
                    Object.entries(data ?? {})
                        .filter(([key]) => this.fields[key]?.type !== 'html')
                );
            },

            async init() {
                if(this.independant) {
                    this.mount(this.props);
                    this.ready = true;
                }
                else {
                    if(this.entityKey) {
                        await this.get();
                        this.ready = true;
                    }
                    else logError('no entity key provided');
                }
            },
            redirectForResponse(response) {
                location.href = response.data.redirectUrl;
            },
            redirectToParentPage() {
                location.href = getBackUrl(this.breadcrumb.items);
            },
            async submit({ postFn }={}) {
                if(this.isUploading) {
                    return;
                }
                this.$emit('loading', true);

                const data = this.serialize();
                const post = () => postFn
                    ? postFn(data)
                    : this.post(this.apiPath, data);

                const response = await post()
                    .catch(this.handleError)
                    .finally(() => {
                        this.$emit('loading', false);
                    });

                if(this.independant) {
                    this.$emit('submit', response);
                    return response;
                }

                this.$store.dispatch('setLoading', true);
                this.redirectForResponse(response);
            },
            handleSubmitClicked() {
                this.submit().catch(()=>{});
            },
            handleDeleteClicked() {
                this.axiosInstance.delete(this.apiPath)
                    .then(response => {
                        this.redirectForResponse(response);
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
            hasUploadingFields(fieldKey) {
                return Object.keys(this.uploadingFields)
                    .some(uploadingField => uploadingField.startsWith(`${fieldKey}.`));
            },
        },
        created() {
            this.$on('error-cleared', errorId => {
                this.errors = {
                    ...this.errors,
                    [errorId]: null,
                }
            });
        },
        mounted() {
            this.init();
        }
    }
</script>
