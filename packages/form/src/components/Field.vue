<script>
    import Vue from 'vue';
    import { log, logError } from 'sharp';
    import Fields from './fields/index';

    const customFieldRE = /^custom-(.+)$/;

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
            value: [String, Number, Boolean, Object, Array],
            locale: String,
            uniqueIdentifier: String,
            fieldConfigIdentifier: String,
            updateData: Function
        },
        computed: {
            isCustom() {
                return customFieldRE.test(this.fieldType);
            },
            component() {
                if(this.isCustom) {
                    let [_, name] = this.fieldType.match(customFieldRE) || [];
                    name = `SharpCustomField_${name}`;
                    return Vue.options.components[name];
                }
                return Fields[this.fieldType];
            }
        },
        render(h) {
            if(!this.component) {
                let message = this.isCustom
                    ? `unknown custom field type '${this.fieldType}', make sure you register it correctly`
                    : `unknown type '${this.fieldType}'`;
                logError(`SharpField '${this.fieldKey}': ${message}`, this.fieldProps);
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