<template>
    <div class="SharpFieldContainer SharpForm__form-item" :class="formGroupClasses" :style="extraStyle">
        <div class="SharpForm__field-header" v-sticky>
            <div class="row align-items-end">
                <div class="col">
                    <template v-if="showLabel">
                        <label class="SharpForm__label form-label" @click="triggerFocus">
                            {{ label }}
                        </label>
                    </template>
                    <template v-else>
                        <div class="SharpForm__label SharpForm__label--placeholder form-label"></div>
                    </template>
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
        </div>

        <div class="SharpForm__field-content">
            <Field
                v-bind="$props"
                :unique-identifier="mergedErrorIdentifier"
                :field-config-identifier="mergedConfigIdentifier"
                @error="setError"
                @ok="setOk"
                @clear="clear"
                @blur="handleBlur"
                @locale-change="handleLocaleChanged"
                ref="field"
            />
        </div>

        <template v-if="stateMessage">
            <div class="invalid-feedback d-block">{{ stateMessage }}</div>
        </template>

        <template v-if="helpMessage">
            <div class="SharpForm__help-message form-text">{{ helpMessage }}</div>
        </template>
    </div>
</template>

<script>
    import { logError, lang } from 'sharp';
    import { Identifier, ConfigNode }  from 'sharp/mixins';
    import Field from '../Field';
    import FieldLocaleSelect from './FieldLocaleSelect';
    import { resolveTextValue, isLocalizableValueField } from '../../util';
    import { sticky } from "sharp/directives";


    export default {
        name: 'SharpFieldContainer',

        mixins: [ Identifier, ConfigNode ],

        components: {
            Field,
            FieldLocaleSelect,
        },

        inject: {
            $form: {},
            $tab: {
                default: null,
            },
        },

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
                        'SharpForm__form-item--danger': this.hasError,
                        'SharpForm__form-item--success': this.state === 'ok',
                        'SharpForm__form-item--has-label': this.showLabel,
                        'SharpForm__form-item--no-label': !this.showLabel,
                    }
                ];
            },
            extraStyle() {
                return this.fieldProps.extraStyle;
            },
            hasError() {
                return this.state === 'error' || this.errorsLocales.length > 0;
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
                    const locales = this.errorsLocales.join(', ').toUpperCase();
                    const message = lang('form.validation_error.localized').replace(':locales', locales);
                    this.setError(message);
                }
                else if(error == null) {
                    this.clear(false);
                }
                else {
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
            clear(emits = true) {
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
                    this.clear();
                }
            },
            handleLocaleChanged(locale) {
                this.$emit('locale-change', this.fieldKey, locale);
            }
        },
        directives: {
            sticky,
        },
    }
</script>
