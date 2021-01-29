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
                        :errors="errorsLocales"
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
            @locale-change="handleLocaleChanged"
            ref="field"
        />
        <div class="SharpForm__form-requirement">{{stateMessage}}</div>
        <small class="SharpForm__help-message">{{helpMessage}}</small>
    </div>
</template>

<script>
    import { logError } from 'sharp';
    import { Identifier, ConfigNode }  from 'sharp/mixins';
    import Field from '../Field';
    import FieldLocaleSelect from './FieldLocaleSelect';
    import { resolveTextValue, isLocalizableValueField } from '../../util';


    export default {
        name: 'SharpFieldContainer',

        mixins: [ Identifier, ConfigNode ],

        components: {
            Field,
            FieldLocaleSelect,
        },

        inject: ['$tab', '$form'],

        props : {
            ...Field.props,

            label: String,
            helpMessage: String,
            originalValue: [String, Number, Boolean, Object, Array, Date],
            errorIdentifier: [String, Number],
            localizedErrorIdentifier: String,
        },
        data() {
            return {
                state: 'default',
                stateMessage: ''
            }
        },
        watch: {
            originalValue: {
                deep: true,
                handler: 'handleValueChanged',
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
                        'SharpForm__form-item--danger': this.state==='error' || this.errorsLocales.length > 0,
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
            },
            mergedErrorIdentifier() {
                return this.getMergedIdentifier('mergedErrorIdentifier', this.errorIdentifier);
            },
            mergedLocalizedErrorIdentifier() {
                return this.localizedErrorIdentifier
                    ? this.getMergedIdentifier('mergedErrorIdentifier', this.localizedErrorIdentifier)
                    : null;
            },
            errorsLocales() {
                return Object.entries(this.$form.errors)
                    .filter(([key, value]) => !!value)
                    .reduce((res, [key]) => {
                        const match = key.match(new RegExp(`^${this.mergedErrorIdentifier}\\.([^.]+)$`));
                        const locale = match?.[1];
                        return locale ? [...res, locale] : res;
                    }, []);
            },
        },
        methods: {
            updateError(errors) {
                const error = errors[this.mergedLocalizedErrorIdentifier] ?? errors[this.mergedErrorIdentifier];
                if(Array.isArray(error)) {
                    this.setError(error[0]);
                }
                else if(this.errorsLocales.length > 0) {
                    this.setError(`There is an error in : ${this.errorsLocales.join(', ').toUpperCase()}`);
                }
                else if(error != null) {
                    logError(`FieldContainer : Not processable error "${this.mergedErrorIdentifier}" : `, error);
                }
            },
            setError(error) {
                this.state = 'error';
                this.stateMessage = error;
                this.$tab?.$emit('error', this.mergedErrorIdentifier);
            },
            setOk() {
                this.state = 'ok';
                this.stateMessage = '';
            },
            clear(emits) {
                this.state = 'default';
                this.stateMessage = '';
                if(emits) {
                    this.emitClear(this.mergedErrorIdentifier);
                    if(this.mergedLocalizedErrorIdentifier) {
                        this.emitClear(this.mergedLocalizedErrorIdentifier);
                    }
                }
            },
            emitClear(identifier) {
                this.$tab?.$emit('clear', identifier);
                this.$form.$emit('error-cleared', identifier);
            },
            triggerFocus() {
                this.$set(this.fieldProps,'focused',true);
            },
            handleBlur() {
                this.$set(this.fieldProps,'focused',false);
            },
            handleValueChanged() {
                if(this.state === 'error') {
                    this.clear(true);
                }
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
