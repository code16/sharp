<template>
    <div class="Form container">
        <template v-if="ready">
            <template v-if="layout.tabbed && layout.tabs.length>1">
                <b-tabs pills v-model="tabIndex">
                    <b-tab v-for="(tab,i) in layout.tabs" :title="tab.title" :key="i">
                        <sharp-form-tab-content
                                :columns="tab.columns"
                                :fields="fields"
                                :data="data"
                                :update-data="updateData"
                        >
                        </sharp-form-tab-content>
                    </b-tab>
                </b-tabs>
            </template>
            <template v-else>
                <div v-for="tab in layout.tabs">
                    <sharp-form-tab-content
                            :columns="tab.columns"
                            :fields="fields"
                            :data="data"
                            :update-data="updateData"
                    >
                    </sharp-form-tab-content>
                </div>
            </template>
        </template>
        <template v-else>
            Form loading...
        </template>
    </div>
</template>

<script>
    import util from '../util';
    import TemplateDefinition from '../template-definition';
    import { API_PATH } from '../consts';
    import testableForm from '../mixins/testable-form';

    import Template from '../app/models/Template';
    import TemplateController from '../app/controllers/TemplateController';

    import { NameAssociation as fieldCompNames } from './fields/index';

    import FormTabContent from './FormTabContent';
    import bTabs from './vendor/bootstrap-vue/components/tabs'
    import bTab from './vendor/bootstrap-vue/components/tab'


    export default {
        name:'SharpForm',

        mixins: [
            testableForm
        ],

        components: {
            [FormTabContent.name]: FormTabContent,
            bTab, bTabs
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
                tabIndex:0,
                ready:false
            }
        },
        computed: {
            displayableFields() {
                return this.fields.filter((field, i) => {
                    if(!(field.type in fieldCompNames))
                        return util.error(`Field '${field.key}' have a unknown type (${field.type})`), false;
                    return true;
                })
            },
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
                return new Promise((resolve,reject) =>
                    axios.get(this.apiPath)
                    .then(({data: {fields, layout, data}}) => {
                        this.fields = fields;
                        this.layout = layout;
                        this.data = data;

                        this.ready=true;
                        resolve();
                    })
                );
            },
            postForm() {
                return axios.post(this.apiPath)
                    .then(response => {

                    });
            },
            parseTemplates() {
                for(let fieldKey of Object.keys(this.fields)) {
                    let field=this.fields[fieldKey];
                    for (let fieldPropName of Object.keys(field)) {

                        if (Template.isTemplateProp(fieldPropName)) {
                            TemplateController.compileAndRegisterComponent(fieldKey, {
                                templateName: fieldPropName,
                                templateValue: field[fieldPropName],
                            });
                        }
                    }
                }
            }
        },
        created() {
            if(this.entityKey != null) {
                this.getForm().then(_=>this.parseTemplates());
            }
            else util.error('no entity key provided');
            window.form = this;
        }
    }
</script>