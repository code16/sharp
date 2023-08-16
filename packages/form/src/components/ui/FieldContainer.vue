<template>
    <div class="SharpFieldContainer SharpForm__form-item" :class="formGroupClasses" :style="extraStyle">
        <div class="SharpForm__field-header" v-sticky="fieldType === 'list'">
            <div class="row align-items-end">
                <div class="col d-flex">
                    <template v-if="showLabel">
                        <label :for="fieldId" class="SharpForm__label form-label">
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
                :id="fieldId"
                :unique-identifier="mergedErrorIdentifier"
                :field-config-identifier="mergedConfigIdentifier"
                @error="setError"
                @ok="setOk"
                @clear="clear"
                @blur="handleBlur"
                @locale-change="handleLocaleChanged"
                @input="handleValueChanged"
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
    import { logError } from 'sharp';
    import { ConfigNode, Identifier } from 'sharp/mixins';
    import Field from '../Field.vue';
    import FieldLocaleSelect from './FieldLocaleSelect.vue';
    import { isLocalizableValueField, resolveTextValue } from '../../util';
    import { sticky } from "sharp/directives";
    import { __ } from '@/utils/i18n';


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
            // todo extract to common field props (cf Field.vue)
            fieldKey: String,
            fieldType: String,
            fieldProps: Object,
            fieldLayout: Object,
            value: [String, Number, Boolean, Object, Array, Date],
            locale: [Array, String],
            uniqueIdentifier: String,
            fieldConfigIdentifier: String,
            updateData: Function,
            readOnly: Boolean,
            root: Boolean,

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
            '$form.errors'(errors) {
                this.updateError(errors);
            },
            locale() {
                this.updateError(this.$form.errors);
            }
        },
        computed: {
            fieldId() {
                return `form-field_${this.mergedErrorIdentifier}`;
            },
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
                console.log(this);
                return this.fieldProps.extraStyle;
            },
            hasError() {
                return this.state === 'error' || this.errorsLocales.length > 0;
            },
            showLabel() {
                return !!this.label;
            },
            resolvedOriginalValue() {
                return resolveTextValue({ field:this.fieldProps, value:this.originalValue });
            },
            isLocaleObject() {
                return isLocalizableValueField(this.fieldProps) || this.fieldProps.type === 'editor';
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
                    .map(([key]) => {
                        const match = key.match(new RegExp(`^${this.mergedErrorIdentifier}\\.([^.]+)$`));
                        return match?.[1];
                    })
                    .filter(locale => locale && this.$form.locales?.includes(locale))
            },
        },
        methods: {
            updateError(errors) {
                const error = errors[this.mergedLocalizedErrorIdentifier] ?? errors[this.mergedErrorIdentifier];
                if(Array.isArray(error)) {
                    this.setError(error[0]);
                }
                else if(this.fieldProps.localized && this.errorsLocales.length > 0) {
                    const locales = this.errorsLocales.join(', ').toUpperCase();
                    const message = __('form.validation_error.localized', { locales });
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
                this.$form.updateFieldError(identifier, null);
            },
            triggerFocus() {
                // this.$set(this.fieldProps, 'focused', true);
            },
            handleBlur() {
                // this.$set(this.fieldProps, 'focused', false);
            },
            handleValueChanged(value, { error } = {}) {
                if(error) {
                    this.$form.updateFieldError(this.mergedLocalizedErrorIdentifier ?? this.mergedErrorIdentifier, [error]);
                } else if(this.state === 'error') {
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
