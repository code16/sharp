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
            },
            disabled: {
                type: Boolean
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
                    ...this.fieldProps,
                    disabled : this.disabled,
                },
            });
        }
    }
</script>