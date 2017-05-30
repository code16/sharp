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
                                                         :update-data="updateData">
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
    import testableForm from '../../mixins/testable-form';

    import { NameAssociation as fieldCompNames } from './fields/index';

    import ActionView from '../ActionView';
    import FormLayout from './FormLayout'
    import Grid from './Grid';
    import FieldsLayout from './FieldsLayout.vue';


    export default {
        name:'SharpForm',

        mixins: [
            testableForm
        ],

        components: {
            [FormLayout.name]: FormLayout,
            [FieldsLayout.name]: FieldsLayout,
            [Grid.name]: Grid
        },

        props:{
            entityKey: String,
            instanceId: String,
        },

        data() {
            return {
                fields: null,
                data: null,
                layout: null,
                ready: false,
                tabIndex: 0,
            }
        },
        computed: {
            apiPath() {
                let path = `${API_PATH}/form/${this.entityKey}`;
                if(this.instanceId) path+=`/${this.instanceId}`;
                return path;
            }
        },
        methods: {
            updateData(key,value) {
                this.data[key] = value;
            },
            getForm() {
                return axios.get(this.apiPath)
                    .then(({data: {fields, layout, data}}) => {
                        this.fields = fields;
                        this.layout = layout;
                        this.data = data;

                        this.ready=true;
                    });
            },
            postForm() {
                return axios.post(this.apiPath, this.data)
                    .then(response => {

                    });
            },
        },
        created() {
            if(this.entityKey != null) {
                this.getForm();
            }
            else util.error('no entity key provided');
            window.form = this;
        },
        mounted() {
            if(this.$parent && this.$parent.$options.name === ActionView.name) {
                this.$parent.$on('main-button-click', this.postForm);
            }
        }
    }
</script>