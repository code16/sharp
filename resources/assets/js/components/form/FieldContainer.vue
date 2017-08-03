<template>
    <div class="SharpFieldContainer SharpForm__form-item" :class="formGroupClasses" :style="extraStyle">
        <label class="SharpForm__label" v-show="label" @click="triggerFocus">
            {{label}} <span v-if="fieldProps.localized" class="SharpFieldContainer__label-locale">({{locale}})</span>
        </label>
        <template v-if="alerts.length">
            <div v-for="alert in alerts" class="alert" :class="alertClass(alert.type)" role="alert">
                {{alert.msg}}
            </div>
        </template>
        <sharp-field v-bind="exposedProps"
                     @error="setError" 
                     @ok="setOk" 
                     @clear="clear"
                     @blur="handleBlur">
        </sharp-field>
        <div class="SharpForm__form-requirement">{{stateMessage}}</div>

        <!--TODO help message class-->
        <small class="SharpForm__help-message">{{helpMessage}}</small>
    </div>
</template>

<script>
    import Field from './Field';
    import {ErrorNode} from '../../mixins/index';

    export default {
        name: 'SharpFieldContainer',

        mixins: [ ErrorNode ],

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
                stateMessage:'',
                alerts:[],
            }
        },
        watch: {
            value() {
                if(this.state === 'error')
                    this.clear();
            },
            '$form.errors': {
                immediate:true,
                handler(errors) {
                    let error = errors[this.mergedErrorIdentifier];
                    if(Array.isArray(error)) {
                        this.setError(error[0]);
                    }
                }
            },
        },
        computed: {
            formGroupClasses() {
                return {
                    'SharpForm__form-item--danger': this.state==='error',
                    'SharpForm__form-item--success': this.state==='ok'
                }
            },
            extraStyle() {
                return this.fieldProps.extraStyle;
            },
            exposedProps() {
                const { errorIdentifier, isErrorRoot, ...exposedProps } = this.$props;
                return exposedProps;
            }
        },
        methods: {
            setError(error) {
                this.state = 'error';
                this.stateMessage = error;
                if(this.$tab) {
                    this.$tab.$emit('error', this.fieldKey);
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
                    this.$tab.$emit('clear', this.fieldKey);
                }
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