<script>
    import Fields, { NameAssociation as fieldCompName } from './fields/index';
    import { FieldValue, UpdateData } from '../mixins';

    export default {
        name:'SharpField',
        mixins: [FieldValue, UpdateData],
        components: Fields,

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
                    value:this.value,
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