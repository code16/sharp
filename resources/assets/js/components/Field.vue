<script>
    import Fields from './fields';

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
            return h(this.fieldType,{
                props : {
                    fieldKey:this.fieldKey,
                    ...this.fieldProps
                },
            });
        }
    }
</script>