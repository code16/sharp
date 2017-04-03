<template>
    <div>
        <template v-if="layout.length == 1">
            <sharp-grid :rows="[layout[0].columns]">
                <template scope="column">
                    <sharp-fields-layout v-if="fields" :fields="column.item.fields" :all="fields"></sharp-fields-layout>
                </template>
            </sharp-grid>
        </template>
        <template v-else>

        </template>
    </div>
</template>

<script>
    import util from '../util';
    import { Template } from '../mixins';
    import TemplateDefinition from '../template-definition';

    import Grid from './Grid';
    import FieldsLayout from './FieldsLayout';
    import layout from '../layout';

    export default {
        name:'SharpForm',

        mixins:[ Template ],

        components: {

            [Grid.name]:Grid,
            [FieldsLayout.name]:FieldsLayout
        },

        data() {
            return {
                fields:null,
                layout
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
            this.fields = {
                'A':{
                    type:'SharpTextInput'
                },
                'B':{
                    type:'SharpTextInput'
                },
                'C':{
                    type:'SharpTextInput'
                },
                'D':{
                    type:'SharpTextInput'
                },
                'E':{
                    type:'SharpTextInput'
                },
                'F':{
                    type:'SharpTextInput'
                },
                'G':{
                    type:'SharpTextInput'
                },
                'H':{
                    type:'SharpTextInput'
                }
            }


        }
    }
</script>