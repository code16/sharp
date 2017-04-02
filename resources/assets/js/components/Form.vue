<template>
    <div>  <!--TODO système grille-->
        <sharp-field-container v-for="field in fields"
                               :field-key="field.key"
                               :field-type="field.type"
                               :field-props="field"
                               :label="field.label"
                               :help-message="field.helpMessage"
                               :read-only="field.readOnly">
        </sharp-field-container>
    </div>
</template>

<script>
    import FieldContainer from './FieldContainer';

    import Template from '../app/models/Template';
    import TemplateController from '../app/controllers/TemplateController';


    export default {
        name:'SharpForm',

        components: {
            [FieldContainer.name]:FieldContainer
        },

        data() {
            return {
                fields:null
            }
        },
        methods: {
            
        },
        computed: {
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
                   conditionalDisplay: 'advanced_search'
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