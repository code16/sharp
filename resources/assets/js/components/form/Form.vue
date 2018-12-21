<template>
    <div class="SharpForm">
        <template v-if="ready">
            <div v-show="hasErrors" class="SharpNotification SharpNotification--error" role="alert">
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
    import { API_PATH, BASE_URL } from '../../consts';

    import { ActionEvents, ReadOnlyFields, Localization } from '../../mixins';

    import DynamicView from '../DynamicViewMixin';

    import SharpTabbedLayout from '../TabbedLayout'
    import SharpGrid from '../Grid';
    import SharpFieldsLayout from './FieldsLayout.vue';


    const noop = ()=>{}

    export default {
        name:'SharpForm',
        extends: DynamicView,

        mixins: [ActionEvents, ReadOnlyFields('fields'), Localization],

        components: {
            SharpTabbedLayout,
            SharpFieldsLayout,
            SharpGrid,
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
                config: null,
                authorizations: null,

                errors:{},
                locale: '',

                fieldVisible: {},
                curFieldsetId:0,

                pendingJobs: []
            }
        },
        computed: {
            apiPath() {
                let path = `form/${this.entityKey}`;
                if(this.instanceId) path+=`/${this.instanceId}`;
                return path;
            },
            localized() {
                return this.config && Array.isArray(this.config.locales);
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

            baseEntityKey() {
                return this.entityKey.split(':')[0];
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
                if(this.fields[key].localized) {
                    this.$set(this.data[key],this.locale,value);
                }
                else this.$set(this.data,key,value);
            },
            updateVisibility(key, visibility) {
                this.$set(this.fieldVisible, key, visibility);
            },
            mount({fields, layout, data={}, config={}, authorizations={}}) {
                this.fields = fields;
                this.layout = this.patchLayout(layout);
                this.data = data;
                this.config = config;
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
                    locales: null, //this.config.locales,
                    showSubmitButton: showSubmitButton && !disable,
                    showDeleteButton: !this.isCreation && this.authorizations.delete && !disable,
                    showBackButton: this.isReadOnly,
                    opType: this.isCreation ? 'create' : 'update'
                });

                if(setLocale && this.config.locales) {
                    this.actionsBus.$emit('localeChanged', this.config.locales[0]);
                }
            },
            redirectToList({ restoreContext=true }={}) {
                location.href = `${BASE_URL}/list/${this.baseEntityKey}${restoreContext?'?restore-context=1':''}`
            },
        },
        actions: {
            async submit({ entityKey, endpoint, dataFormatter=noop, postConfig }={}) {
                if(entityKey && entityKey !== this.entityKey || this.pendingJobs.length) return;

                try {
                    const response = await this.post(endpoint, dataFormatter(this), postConfig);
                    if(this.independant) {
                        this.$emit('submitted', response);
                    }
                    else if(response.data.ok) {
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