<template>
    <div class="SharpFieldContainer SharpForm__form-item" :class="formGroupClasses" :style="extraStyle">
        <div class="row">
            <div class="col">
                <label v-if="showLabel" class="SharpForm__label" @click="triggerFocus">
                    {{label}}
                </label>
            </div>
            <template v-if="fieldProps.localized">
                <div class="col-auto">
                    <FieldLocaleSelect
                        :locales="$form.locales"
                        :current-locale="locale"
                        :field-value="resolvedOriginalValue"
                        :is-locale-object="isLocaleObject"
                        @change="handleLocaleChanged"
                    />
                </div>
            </template>
        </div>
        <Field v-bind="exposedProps"
            @error="setError"
            @ok="setOk"
            @clear="clear"
            @blur="handleBlur"
            ref="field" 
        />
        <div class="SharpForm__form-requirement">{{stateMessage}}</div>
        <small class="SharpForm__help-message">{{helpMessage}}</small>
    </div>
</template>

<script>
    import { logError } from 'sharp';
    import { ErrorNode, ConfigNode }  from 'sharp/mixins';
    import Field from '../Field';
    import FieldLocaleSelect from './FieldLocaleSelect';
    import { resolveTextValue, isLocalizableValueField } from '../../util/locale';


    export default {
        name: 'SharpFieldContainer',

        mixins: [ ErrorNode, ConfigNode ],

        components: {
            Field,
            FieldLocaleSelect,
        },

        inject:['$tab', '$form'],

        props : {
            ...Field.props,

            label: String,
            helpMessage: String,
            originalValue: [String, Number, Boolean, Object, Array],
        },
        data() {
            return {
                state:'classic',
                stateMessage:''
            }
        },
        watch: {
            value() {
                if(this.state === 'error')
                    this.clear();
            },
            '$form.errors'(errors) {
                this.updateError(errors);
            },
            locale() {
                this.updateError(this.$form.errors);
            }
        },
        computed: {
            formGroupClasses() {
                return [
                    `SharpForm__form-item--type-${this.fieldType}`,
                    {
                        'SharpForm__form-item--danger': this.state==='error',
                        'SharpForm__form-item--success': this.state==='ok',
                        'SharpForm__form-item--no-label': !this.showLabel,
                    }
                ];
            },
            extraStyle() {
                return this.fieldProps.extraStyle;
            },
            exposedProps() {
                return {
                    ...this.$props,
                    uniqueIdentifier: this.mergedErrorIdentifier,
                    fieldConfigIdentifier: this.mergedConfigIdentifier
                };
            },
            showLabel() {
                return !!this.label || this.label === '';
            },
            resolvedOriginalValue() {
                return resolveTextValue({ field:this.fieldProps, value:this.originalValue });
            },
            isLocaleObject() {
                return isLocalizableValueField(this.fieldProps);
            }
        },
        methods: {
            updateError(errors) {
                let error = errors[this.mergedErrorIdentifier];
                if(error == null) {
                    this.clear();
                }
                else if(Array.isArray(error)) {
                    this.setError(error[0]);
                }
                else {
                    logError(`FieldContainer : Not processable error "${this.mergedErrorIdentifier}" : `, error);
                }
            },
            setError(error) {
                this.state = 'error';
                this.stateMessage = error;
                if(this.$tab) {
                    this.$tab.$emit('error', this.mergedErrorIdentifier);
                }
            },
            setOk() {
                this.state = 'ok';
                this.stateMessage = '';
            },
            clear() {
                this.state = 'classic';
                this.stateMessage = '';
                if(this.$tab) {
                    this.$tab.$emit('clear', this.mergedErrorIdentifier);
                }
                this.$form.$emit('error-cleared', this.mergedErrorIdentifier);
            },
            triggerFocus() {
                this.$set(this.fieldProps,'focused',true);
            },
            handleBlur() {
                this.$set(this.fieldProps,'focused',false);
            },
            handleLocaleChanged(locale) {
                this.$emit('locale-change', this.fieldKey, locale);
            }
        },
        mounted() {
            //console.log(this);
        }
    }
</script>