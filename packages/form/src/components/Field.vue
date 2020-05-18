<script>
    import { log, logError, isCustomField, resolveCustomField } from 'sharp';
    import Fields from './fields/index';

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
            fieldType: String,
            fieldProps: Object,
            fieldLayout: Object,
            value: [String, Number, Boolean, Object, Array, Date],
            locale: String,
            uniqueIdentifier: String,
            fieldConfigIdentifier: String,
            updateData: Function
        },
        computed: {
            isCustom() {
                return isCustomField(this.fieldType)
            },
            component() {
                if(this.isCustom) {
                    return resolveCustomField(this.fieldType);
                }
                return Fields[this.fieldType];
            }
        },
        render(h) {
            if(!this.component) {
                if(!this.custom) {
                    logError(`Unknown field type '${this.fieldType}'`, this.fieldProps);
                }
                return null;
            }

            let { key, ...fieldProps } = this.fieldProps;


            return h(this.component, {
                props : {
                    fieldKey: this.fieldKey,
                    fieldLayout: this.fieldLayout,
                    value: this.value,
                    locale: this.locale,
                    uniqueIdentifier: this.uniqueIdentifier,
                    fieldConfigIdentifier: this.fieldConfigIdentifier,
                    ...fieldProps
                },
                attrs: {
                    dynamicAttributes: fieldProps.dynamicAttributes,
                },
                on: {
                    input: (val, options={}) => {
                        if(this.fieldProps.readOnly && !options.force)
                            log(`SharpField '${this.fieldKey}', can't update because is readOnly`);
                        else
                            this.updateData(this.fieldKey, val, { forced:options.force });
                    },
                    blur: () => {
                        this.fieldProps.focused = false;
                    }
                }
            });
        }
    }
</script>