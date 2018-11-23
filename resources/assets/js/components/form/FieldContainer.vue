<template>
    <div class="SharpFieldContainer SharpForm__form-item" :class="formGroupClasses" :style="extraStyle">
        <label v-if="showLabel" class="SharpForm__label" @click="triggerFocus">
            {{label}} <span v-if="fieldProps.localized" class="SharpFieldContainer__label-locale">({{locale}})</span>
        </label>
        <sharp-field v-bind="exposedProps"
                     @error="setError" 
                     @ok="setOk" 
                     @clear="clear"
                     @blur="handleBlur"
                     ref="field">
        </sharp-field>
        <div class="SharpForm__form-requirement">{{stateMessage}}</div>
        <small class="SharpForm__help-message">{{helpMessage}}</small>
    </div>
</template>

<script>
    import Field from './Field';
    import {ErrorNode, ConfigNode} from '../../mixins/index';

    import * as util from '../../util';

    export default {
        name: 'SharpFieldContainer',

        mixins: [ ErrorNode, ConfigNode ],

        components: {
            [Field.name]:Field
        },

        inject:['$tab', '$form'],

        props : {
            ...Field.props,

            label: String,
            helpMessage: String,
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
            '$form.errors': {
                handler(errors) {
                    let error = errors[this.mergedErrorIdentifier];
                    if(error == null) {
                        this.clear();
                    }
                    else if(Array.isArray(error)) {
                        this.setError(error[0]);
                    }
                    else {
                        util.error(`FieldContainer : Not processable error "${this.mergedErrorIdentifier}" : `, error);
                    }
                }
            },
        },
        computed: {
            formGroupClasses() {
                return [
                    `SharpForm__form-item--type-${this.fieldType}`,
                    {
                        'SharpForm__form-item--danger': this.state==='error',
                        'SharpForm__form-item--success': this.state==='ok'
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
            }
        },
        methods: {
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
            }
        },
        mounted() {
            //console.log(this);
        }
    }
</script>