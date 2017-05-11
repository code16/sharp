<script>
    import Fields, { NameAssociation as fieldCompName } from './fields/index';
    import { FieldValue } from '../mixins';

    export default {
        name:'SharpField',
        mixins: [FieldValue],
        components: Fields,

        inject: ['updateData'],

        provide() {
            let provide = {};
            Vue.util.defineReactive(provide,'value',this.value);
            return provide;
        },

        props: {
            fieldKey: {
                type: String
            },
            fieldType: {
                type: String
            },
            fieldProps:{
                type: Object
            }
        },
        mounted() {
            //console.log(this);
        },
        render(h) {
            return h(fieldCompName[this.fieldType],{
                props : {
                    fieldKey:this.fieldKey,
                    fieldLayout:this.fieldLayout,
                    ...this.fieldProps
                },
                on: {
                    input: val => {
                        this.updateData(this.fieldKey,val);
                    }
                }
            });
        }
    }
</script>