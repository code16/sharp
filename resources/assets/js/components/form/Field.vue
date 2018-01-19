<script>
    import Fields from './fields/index';
    import * as util from '../../util';

    export default {
        name:'SharpField',
        inheritAttrs: false,

        provide() {
            return {
                $field: this
            }
        },

        props: {
            fieldKey: String,
            fieldType:  String,
            fieldProps: Object,
            fieldLayout: Object,
            value: [String, Number, Boolean, Object, Array],
            locale: String,
            uniqueIdentifier: String,
            fieldConfigIdentifier: String,
            updateData: Function
        },
        mounted() {
            //console.log(this);
        },
        render(h) {
            if(!(this.fieldType in Fields)) {
                util.error(`SharpField '${this.fieldKey}', unknown type '${this.fieldType}'`, this.fieldProps);
                return null;
            }

            let { key, ...fieldProps } = this.fieldProps;


            return h(Fields[this.fieldType],{
                props : {
                    fieldKey: this.fieldKey,
                    fieldLayout: this.fieldLayout,
                    value: this.value,
                    locale: this.locale,
                    uniqueIdentifier: this.uniqueIdentifier,
                    fieldConfigIdentifier: this.fieldConfigIdentifier,
                    ...fieldProps
                },
                on: {
                    input: (val, options={}) => {
                        let { force } = options;
                        if(this.fieldProps.readOnly && !force)
                            util.log(`SharpField '${this.fieldKey}', can't update because is readOnly`);
                        else
                            this.updateData(this.fieldKey,val);
                    },
                    blur: () => {
                        this.fieldProps.focused = false;
                    }
                }
            });
        }
    }
</script>