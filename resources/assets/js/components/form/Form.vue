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
            <sharp-tabbed-layout :layout="layout">
                <!-- Tab -->
                <template scope="tab">
                    <sharp-grid :rows="[tab.columns]">
                        <!-- column -->
                        <template scope="column">
                            <sharp-fields-layout v-if="fields" :layout="column.fields">
                                <!-- field -->
                                <template scope="fieldLayout">
                                    <sharp-field-display :field-key="fieldLayout.key"
                                                         :context-fields="isReadOnly ? readOnlyFields : fields"
                                                         :context-data="data"
                                                         :field-layout="fieldLayout"
                                                         :locale="locale"
                                                         :error-identifier="fieldLayout.key"
                                                         :update-data="updateData">
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

    import { testableForm, ActionEvents, ReadOnlyFields, Localization } from '../../mixins';

    import DynamicView from '../DynamicViewMixin';

    import TabbedLayout from '../TabbedLayout'
    import Grid from '../Grid';
    import FieldsLayout from './FieldsLayout.vue';


    import axios from 'axios';

    const noop = ()=>{}

    export default {
        name:'SharpForm',
        extends: DynamicView,

        mixins: [testableForm, ActionEvents, ReadOnlyFields('fields'), Localization],

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

        inject:['actionsBus', ...DynamicView.inject],

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
                locale: ''
            }
        },
        computed: {
            apiPath() {
                let path = `${API_PATH}/form/${this.entityKey}`;
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
            mount({fields, layout, data={}, config={}, authorizations={}}) {
                this.fields = fields;
                this.layout = layout;
                this.data = data;
                this.config = config;
                this.authorizations = authorizations;
            },
            handleError({response}) {
                if(response.status===422)
                    this.errors = response.data || {};
            },
            delete() {
                axios.delete(this.apiPath);
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

            setupActionBar() {
                const showSubmitButton = this.isCreation ? this.authorizations.create : this.authorizations.update;

                this.actionsBus.$emit('setup', {
                    locales: null, //this.config.locales,
                    showSubmitButton,
                    showDeleteButton: this.authorizations.delete,
                    showBackButton: this.isReadOnly,
                    opType: this.isCreation ? 'create' : 'update'
                });

                if(this.config.locales) {
                    this.actionsBus.$emit('localeChanged', this.config.locales[0]);
                }
            },
        },
        actions: {
            submit({entityKey, endpoint, dataFormatter=noop }={}) {
                if(entityKey && entityKey !== this.entityKey) return;

                this.post(endpoint, dataFormatter(this))
                    .then(({ data })=>{
                        if(this.independant) {
                            this.$emit('submitted', data);
                        }
                        else if(data.ok) {
                            this.mainLoading.$emit('show');
                            location.href = `/sharp/list/${this.entityKey}?restore-context=1`
                        }
                    })
                    .catch(this.handleError)
            },
            cancel() {
                location.href = `/sharp/list/${this.entityKey}?restore-context=1`;
            },
            localeChanged(newLocale) {
                this.locale = newLocale;
            },
            delete: 'delete',
            reset({ entityKey }) {
                if(entityKey && entityKey !== this.entityKey) return;

                this.data = {}
            }
        },
        created() {
            this.$on('error-cleared', errorId => {
                this.$set(this.errors[errorId],'cleared',true);
            })
        },
        mounted() {
            this.init();
            console.log(this);
        }
    }
</script>