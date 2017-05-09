<template>
    <div class="Form container">
        <sharp-grid v-if="layout.length == 1" :rows="[layout[0].columns]">
            <template scope="column">
                <sharp-fields-layout v-if="fields" :layout="column.fields">
                    <template scope="field">
                        <sharp-field-container v-if="acceptCondition(fields[field.key])"
                                               :field-key="field.key"
                                               :field-props="fields[field.key]"
                                               :field-type="fields[field.key].type"
                                               :value="data[field.key]"
                                               :label="fields[field.key].label"
                                               :help-message="fields[field.key].helpMessage"
                                               :read-only="fields[field.key].readOnly"
                                               :updateData="updateData">
                        </sharp-field-container>
                    </template>
                </sharp-fields-layout>
            </template>
        </sharp-grid>
    </div>
</template>

<script>
    import util from '../util';
    import TemplateDefinition from '../template-definition';

    import Template from '../app/models/Template';
    import TemplateController from '../app/controllers/TemplateController';

    import Grid from './Grid';
    import FieldsLayout from './FieldsLayout';
    import FieldContainer from './FieldContainer';

    import * as testForm from '../_test-form';
    import { NameAssociation as fieldCompNames } from './fields/index';

    export default {
        name:'SharpForm',

        components: {
            [Grid.name]:Grid,
            [FieldsLayout.name]:FieldsLayout,
            [FieldContainer.name]:FieldContainer
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
                    if(!field)
                        return util.warn(`Field at index ${i} is null or empty : `,field),false;
                    if(!field.key)
                        return util.warn(`Field at index ${i} doesn't have a key : `,field),false;
                    if(!field.type)
                        return util.warn(`Field at index ${i} doesn't have a type : `,field),false;
                    if(!(field.type in fieldCompNames))
                        return util.warn(`Field '${field.key}' have a unknown type (${field.type})`),false;
                    
                    return true;
                })
            }
        },
        methods: {
            acceptCondition(field) {
                if(!field.conditionalDisplay)
                    return true;
                
                let regex=/(\!)(\w+):?((\w+,?)*)/;
                let matches = regex.exec(field.conditionalDisplay);
                let neg = !!matches[1];
                let key = matches[2];
                let values = matches[3] ? matches[3].split(',') : null;

                if(values) {

                }
                else {
                    
                }
                return true;
            },
            updateData(key,value) {
                this.data[key] = value;
            }
        },
        mounted() {
            /** compile templates */
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

            window.form = this;
        }
    }
</script>