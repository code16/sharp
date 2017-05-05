<script>
    import Fields, { NameAssociation as fieldsCompName } from './fields/index';

    export default {
        name:'SharpField',
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
        components: Fields,
        mounted() {
            //console.log(this);
        },
        render(h) {
            return h(fieldsCompName[this.fieldType],{
                props : {
                    fieldKey:this.fieldKey,
                    ...this.fieldProps
                }
            });
        }
    }
</script>