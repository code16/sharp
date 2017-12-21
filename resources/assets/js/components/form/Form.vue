<template>
    <div class="SharpForm">
        <template v-if="ready">
            <div v-show="hasErrors" class="SharpNotification SharpNotification__inline SharpNotification__inline--error" role="alert">
                <div class="SharpNotification__details">
                    <div class="SharpNotification__text-wrapper">
                        <p class="SharpNotification__title">{{ l('form.validation_error.title') }}</p>
                        <p class="SharpNotification__subtitle">{{ l('form.validation_error.description') }}</p>
                    </div>
                </div>
            </div>
            <sharp-tabbed-layout :layout="layout" ref="tabbedLayout">
                <!-- Tab -->
                <template slot-scope="tab">
                    <sharp-grid :rows="[tab.columns]" ref="columnsGrid">
                        <!-- column -->
                        <template slot-scope="column">
                            <sharp-fields-layout v-if="fields" :layout="column.fields" :visible="fieldVisible" ref="fieldLayout">
                                <!-- field -->
                                <template slot-scope="fieldLayout">
                                    <sharp-field-display :field-key="fieldLayout.key"
                                                         :context-fields="isReadOnly ? readOnlyFields : fields"
                                                         :context-data="data"
                                                         :field-layout="fieldLayout"
                                                         :locale="locale"
                                                         :error-identifier="fieldLayout.key"
                                                         :config-identifier="fieldLayout.key"
                                                         :update-data="updateData"
                                                         :update-visibility="updateVisibility"
                                                         ref="field">
                                    </sharp-field-display>
                                </template>
                            </sharp-fields-layout>
                        </template>
                    </sharp-grid>
                </template>
            </sharp-tabbed-layout>
        </template>
    </div>
</template>

<script>
    import * as util from '../../util';
    import { API_PATH } from '../../consts';

    import { ActionEvents, ReadOnlyFields, Localization } from '../../mixins';

    import DynamicView from '../DynamicViewMixin';

    import TabbedLayout from '../TabbedLayout'
    import Grid from '../Grid';
    import FieldsLayout from './FieldsLayout.vue';

    import localize from '../../mixins/localize/Form';
    import { testLocalizedForm } from "../../mixins/localize/test/test-mixins";

    const noop = ()=>{};

    export default {
        name:'SharpForm',
        extends: DynamicView,

        mixins: [ActionEvents, ReadOnlyFields('fields'), Localization, localize,
            //testLocalizedForm
        ],

        components: {
            [TabbedLayout.name]: TabbedLayout,
            [FieldsLayout.name]: FieldsLayout,
            [Grid.name]: Grid,
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

        inject:['actionsBus'],

        provide() {
            return {
                $form:this
            }
        },

        data() {
            return {
                fields: null,
                authorizations: null,

                errors:{},
                locale: '',
                locales: [],

                fieldVisible: {},
                curFieldsetId:0,

                pendingJobs: []
            }
        },
        computed: {
            apiPath() {
                let path = `${API_PATH}/form/${this.entityKey}`;
                if(this.instanceId) path+=`/${this.instanceId}`;
                return path;
            },
            localized() {
                return Array.isArray(this.locales);
            },
            isCreation() {
                return !this.instanceId;
            },
            isReadOnly() {
                return !this.ignoreAuthorizations && !(this.isCreation ? this.authorizations.create : this.authorizations.update);
            },
            // don't show loading on creation
            synchronous() {
                return this.independant;
            },
            hasErrors() {
                return Object.keys(this.errors).some(errorKey => !this.errors[errorKey].cleared);
            },

            downloadLinkBase() {
                return `${API_PATH}/download/${this.entityKey}/${this.instanceId}`;
            }
        },
        methods: {
            fieldErrors(key) {
                if(this.fields[key].localized) {
                    return (this.errors[key]||{})[this.locale];
                }
                return this.errors[key];
            },
            updateData(key,value) {
                let field = this.fields[key];
                if(field.localized && this.isLocalizableValueField(field)) {
                    this.$set(this.data[key],this.locale,value);
                }
                else this.$set(this.data,key,value);
            },
            updateVisibility(key, visibility) {
                this.$set(this.fieldVisible, key, visibility);
            },
            mount({fields, layout, data={}, authorizations={}, locales,}) {
                this.fields = fields;
                this.layout = this.patchLayout(layout);
                this.data = data;
                this.locales = locales;
                this.authorizations = authorizations;

                this.fieldVisible = Object.keys(this.fields).reduce((res, fKey) => {
                    res[fKey] = true;
                    return res;
                },{})
            },
            handleError({response}) {
                if(response.status===422)
                    this.errors = response.data.errors || {};
            },

            patchLayout(layout) {
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

            init() {
                if(this.independant) {
                    this.mount(this.props);
                    this.ready = true;
                }
                else {
                    if(this.entityKey) {
                        this.get().then(_=>this.setupActionBar());
                    }
                    else util.error('no entity key provided');
                }
            },

            setupActionBar({ disable=false ,setLocale=true }={}) {
                const showSubmitButton = this.isCreation ? this.authorizations.create : this.authorizations.update;

                this.actionsBus.$emit('setup', {
                    locales: this.locales,
                    showSubmitButton: showSubmitButton && !disable,
                    showDeleteButton: !this.isCreation && this.authorizations.delete && !disable,
                    showBackButton: this.isReadOnly,
                    opType: this.isCreation ? 'create' : 'update'
                });

                if(setLocale && this.locales) {
                    this.actionsBus.$emit('localeChanged', this.locales[0]);
                }
            },
            redirectToList({ restoreContext=true }={}) {
                location.href = `/sharp/list/${this.entityKey}${restoreContext?'?restore-context=1':''}`
            },
        },
        actions: {
            async submit({entityKey, endpoint, dataFormatter=noop }={}) {
                if(entityKey && entityKey !== this.entityKey || this.pendingJobs.length) return;

                try {
                    const { data } = await this.post(endpoint, dataFormatter(this));
                    if(this.independant) {
                        this.$emit('submitted', data);
                    }
                    else if(data.ok) {
                        this.mainLoading.$emit('show');
                        this.redirectToList();
                    }
                }
                catch(error) {
                    this.handleError(error);
                }
            },
            async 'delete'() {
                try {
                    await this.axiosInstance.delete(this.apiPath);
                    this.redirectToList();
                }
                catch(error) {

                }
            },
            cancel() {
                this.redirectToList();
            },
            localeChanged(newLocale) {
                this.locale = newLocale;
            },
            reset({ entityKey }={}) {
                if(entityKey && entityKey !== this.entityKey) return;

                this.data = {};
                this.errors = {};
            },

            setPendingJob({ key, origin, value:isPending }) {
                if(isPending)
                    this.pendingJobs.push(key);
                else
                    this.pendingJobs = this.pendingJobs.filter(jobKey => jobKey !== key);

                if(this.pendingJobs.length) {
                    this.actionsBus.$emit('updateActionsState', {
                        state: 'pending',
                        modifier: origin
                    })
                }
                else {
                    this.actionsBus.$emit('updateActionsState', null);
                }
            }
        },
        created() {
            this.$on('error-cleared', errorId => {
                if(this.errors[errorId])
                    this.$set(this.errors[errorId],'cleared',true);
            })
        },
        mounted() {
            this.init();
        }
    }
</script>