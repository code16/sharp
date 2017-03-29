<script>
    import Fields from './Fields';

    export default {
        props: {
            fieldType: {
                type: String
            },
            fieldProps:{
                type: Object
            },
            fieldAttrs: {
                type: Object
            },
            disabled: {
                type: Boolean
            }
        },
        components: Fields,
        render(createElement) {
            console.log('rendering field', this.fieldType);
            return createElement(this.fieldType,{
                props : {
                    ...this.fieldProps,
                    disabled : this.disabled
                },
                attrs : this.fieldAttrs
            });
        }
    }
</script>