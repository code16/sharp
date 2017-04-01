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

    import util from '../util';
    import { Template } from '../mixins';
    import TemplateDefinition from '../template-definition';

    export default {
        name:'SharpForm',

        mixins:[ Template ],

        components: {
            [FieldContainer.name]:FieldContainer
        },

        data() {
            return {
                fields:null
            }
        },
        methods: {
            compileTemplates() {
                // loop through form descriptor
                // if key is template type && exist
                for(let field of this.fields) {
                    for(let fieldProp of Object.keys(field)) {
                        if(util.isTemplateProp(fieldProp)) {
                            let templateName = fieldProp;

                            let res=Vue.compile(field[templateName]);
                            let compName=this.template(field.key, util.parseTemplateName(templateName)).compName;

                            let mixins = [];

                            if(fieldProp in TemplateDefinition) {
                                mixins.push(TemplateDefinition[templateName]);
                            }
                            else {
                                console.warn(`${templateName} haven't any definition`);
                            }

                            Vue.component(compName, {
                                mixins,
                                render: res.render
                            });
                        }
                    }
                }
            }
        },
        mounted() {
            // GET fields
            this.fields = [
                {
                    type:'sharp-autocomplete',
                    key:'name',
                    listItemTemplate:`
                    <li>
                        <div>{{item.name}}</div>
                        <div>{{item.surname}}</div>
                    </li>
                    `
                }
            ]
            console.log(this);
            this.compileTemplates();
        }
    }
</script>