<template>
    <div class="SharpForm">
        <template v-if="ready">
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

    import { testableForm, ActionEvents, ReadOnlyFields } from '../../mixins';

    import DynamicView from '../DynamicViewMixin';

    import TabbedLayout from '../TabbedLayout'
    import Grid from '../Grid';
    import FieldsLayout from './FieldsLayout.vue';


    import axios from 'axios';

    export default {
        name:'SharpForm',
        extends: DynamicView,

        mixins: [testableForm, ActionEvents, ReadOnlyFields('fields')],
        components: {
            [TabbedLayout.name]: TabbedLayout,
            [FieldsLayout.name]: FieldsLayout,
            [Grid.name]: Grid,
        },

        props:{
            entityKey: String,
            instanceId: String,

            submitButton: String
        },

        inject:['actionsBus', 'glasspane'],

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
                return !(this.isCreation ? this.authorizations.create : this.authorizations.update);
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
            mount({fields, layout, data, config, authorizations}) {
                this.fields = fields;
                this.layout = layout;
                this.data = data || {};
                this.config = config || {};
                this.authorizations = authorizations;
            },
            handleError({response}) {
                if(response.status===422)
                    this.errors = response.data || {};
                else if(response.status===417)
                    alert(response.data.message)
            },
            delete() {
                axios.delete(this.apiPath);
            },
            init() {
                if(this.entityKey) {
                    this.get().then(_=>this.setupActionBar());
                }
                else util.error('no entity key provided');
            },

            setupActionBar() {
                const submitButtonVisible = this.isCreation ? this.authorizations.create : this.authorizations.update;

                this.actionsBus.$emit('setup', {
                    locales: null, //this.config.locales,
                    submitButtonVisible,
                    deleteButtonVisible: this.authorizations.delete,
                    backButton: this.isReadOnly
                });

                if(this.config.locales) {
                    this.actionsBus.$emit('localeChanged', this.config.locales[0]);
                }
            },
        },
        actions: {
            submit() {
                this.post().catch(this.handleError)
            },
            cancel() {
                location.href = `/sharp/list/${this.entityKey}`;
            },
            localeChanged(newLocale) {
                this.locale = newLocale;
            },
            delete: 'delete',
        },
        mounted() {
            this.init();
        }
    }
</script>