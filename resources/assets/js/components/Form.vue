<template>
    <div class="Form container">
        <sharp-grid v-if="layout.length == 1" :rows="[layout[0].columns]">
            <template scope="column">
                <sharp-fields-layout v-if="fields" :layout="column.fields">
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
    </div>
</template>

<script>
    import util from '../util';
    import TemplateDefinition from '../template-definition';
    import { API_PATH } from '../consts';

    import Template from '../app/models/Template';
    import TemplateController from '../app/controllers/TemplateController';

    import Grid from './Grid';
    import FieldsLayout from './FieldsLayout';

    import * as testForm from '../_test-form';
    import { NameAssociation as fieldCompNames } from './fields/index';


    export default {
        name:'SharpForm',

        components: {
            [Grid.name]:Grid,
            [FieldsLayout.name]:FieldsLayout
        },

        props:{
            entityKey: String,
            instanceId: String,
        },

        data() {
            return {
                fields:testForm.fields,
                data:testForm.data,
                layout:testForm.layout
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
                return axios.get(this.apiPath)
                    .then(response => {
                        Object.assign(this, response);
                    });
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
                                templateProps: field.templateProps
                            });
                        }
                    }
                }
            }
        },
        mounted() {
            if(this.entityKey) {
                this.getForm().then(_=>this.parseTemplates());
            }
            else this.parseTemplates();
        }
    }
</script>