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
                                                         :context-fields="fields"
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
    import util from '../../util';
    import { API_PATH } from '../../consts';

    import { testableForm, ActionEvents } from '../../mixins/index';

    import DynamicView from '../DynamicViewMixin';

    import TabbedLayout from '../TabbedLayout'
    import Grid from '../Grid';
    import FieldsLayout from './FieldsLayout.vue';


    import axios from 'axios';

    export default {
        name:'SharpForm',
        extends: DynamicView,

        mixins: [testableForm, ActionEvents],
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
            mount({fields, layout, data, config}) {
                this.fields = fields;
                this.layout = layout;
                this.data = data || {};
                this.config = config || {};
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
                    this.get().then(_=>this.setupActions());
                }
                else util.error('no entity key provided');
            },

            setupActions(){
                console.log('setup actions')
                this.actionsBus.$emit('setupLocales', this.config.locales);

                if(this.config.locales) {
                    this.actionsBus.$emit('localeChanged', this.config.locales[0]);
                }
            },
        },
        actions: {
            submit() { this.post().catch(this.handleError) },
            localeChanged(newLocale) { this.locale = newLocale },
            delete: 'delete',
        },
        created() {
            this.init();
        }
    }
</script>