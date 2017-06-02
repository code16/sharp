<template>
    <div class="SharpForm container">
        <template v-if="ready">
            <sharp-form-layout :layout="layout">
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
                                                         :update-data="updateData"
                                                         :field-errors="errors[fieldLayout.key]"
                                                         :locale="locale">
                                    </sharp-field-display>
                                </template>
                            </sharp-fields-layout>
                        </template>
                    </sharp-grid>
                </template>
            </sharp-form-layout>
        </template>
        <template v-else>
            Chargement du formulaire...
        </template>
    </div>
</template>

<script>
    import util from '../../util';
    import { API_PATH } from '../../consts';

    import { testableForm } from '../../mixins/index';

    import FormLayout from './FormLayout'
    import Grid from './Grid';
    import FieldsLayout from './FieldsLayout.vue';
    import LocaleSelector from '../LocaleSelector';

    export default {
        name:'SharpForm',

        mixins: [
            testableForm,
        ],

        components: {
            [FormLayout.name]: FormLayout,
            [FieldsLayout.name]: FieldsLayout,
            [Grid.name]: Grid,
            [LocaleSelector.name]: LocaleSelector
        },

        props:{
            entityKey: String,
            instanceId: String,

            submitButton: String
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
                data: null,
                layout: null,
                config: null,

                ready: false,
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
        },
        methods: {
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

                if(this.config.locales)
                    this.locale = this.config.locales[0];
            },
            getForm() {
                return new Promise((resolve, reject)=>
                    axios.get(this.apiPath)
                    .then(({data}) => {

                        this.mount(data);
                        this.ready=true;
                        resolve();
                    })
                );
            },
            postForm() {
                return axios.post(this.apiPath, this.data)
                    .then(response => {

                    })
                    .catch(({response}) => {
                        if(response.status===422)
                            this.errors = response.data || {};
                        else if(response.status===417)
                            alert(response.data.message)
                    })
            },
            init() {
                if(this.entityKey != null) {
                    this.getForm();
                }
                else util.error('no entity key provided');

                if(this.actionsBus) {
                    this.actionsBus.$on('main-button-clicked', _=>{
                        this.postForm();
                    });
                }
            }
        },
        created() {
            this.init();
        },
    }
</script>