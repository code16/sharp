<script>
    import Vue from 'vue';
    import Fields from './fields/index';
    import * as util from '../../util';

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
                util.error(`SharpField '${this.fieldKey}': ${message}`, this.fieldProps);
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