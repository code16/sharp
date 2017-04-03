<template>
    <div>  <!--TODO système grille-->
        <template v-for="field in displayableFields">
            <sharp-field-container v-if="acceptCondition(field)"
                                   :field-key="field.key"
                                   :field-type="field.type"
                                   :field-props="field"
                                   :label="field.label"
                                   :help-message="field.helpMessage"
                                   :read-only="field.readOnly">
            </sharp-field-container>
        </template>
    </div>
</template>

<script>
    import FieldContainer from './FieldContainer';

    import Template from '../app/models/Template';
    import TemplateController from '../app/controllers/TemplateController';

    import Fields from './fields';

    export default {
        name:'SharpForm',

        components: {
            [FieldContainer.name]:FieldContainer
        },

        data() {
            return {
                fields:[]
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
                        return false;
                    if(!(field.type in Fields))
                        return util.warn(`Field '${field.key}' have a unknown type (${field.type})`),false;
                    
                    return true;
                })
            }
        },
        methods: {
            acceptCondition(field) {
                if(!field.conditionalDisplay)
                    return true;
                
                let regex=/(\!)(\w+):((\w+,?)*)/;
                let matches = regex.exec(field.conditionalDisplay);
                let neg = !!matches[1];
                let key = matches[2];
                let values = matches[3] ? matches[3].split(',') : null;

                if(values) {

                }
                else {
                    
                }
                return true;
            }
        },
        mounted() {
            // GET fields
            this.fields = [
                {
                    type:'SharpAutocomplete',
                    key:'name',
                    mode:'local',
                    localValues: [
                        { value: 'Antoine', surname: 'Guingand' },
                        { value: 'Robert', surname: 'Martin' },
                        { value: 'François', surname: 'Leforestier' },
                        { value: 'Fernand', surname: 'Coli' }
                    ],
                    listItemTemplate:`
                        <span class="value">{{ item.value }}</span>
                        <span class="surname">{{ item.surname }}</span>
                    `,
                   // disabled: true
                   conditionalDisplay: '!advanced_search:red,blue,orange'
                },
                {
                    key: '!advanced_search',
                    value: true
                }
            ];
            
            for(let field of this.fields) {
                for(let fieldPropName of Object.keys(field)) {

                    if(Template.isTemplateProp(fieldPropName)) {
                        TemplateController.compileAndRegisterComponent(field.key, fieldPropName, field[fieldPropName]);
                    }
                }
            }

        }
    }
</script>