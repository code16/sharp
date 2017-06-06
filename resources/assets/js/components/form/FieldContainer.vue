<template>
    <div class="SharpFieldContainer form-group" :class="formGroupClasses" :style="extraStyle">
        <label class="form-control-label" v-show="label">
            {{label}} <span v-if="fieldProps.localized" class="SharpFieldContainer__label-locale">({{fieldProps.locale}})</span>
        </label>
        <template v-if="alerts.length">
            <div v-for="alert in alerts" class="alert" :class="alertClass(alert.type)" role="alert">
                {{alert.msg}}
            </div>
        </template>
        <sharp-field v-bind="$props" 
                     @error="setError" 
                     @ok="setOk" 
                     @clear="clear">
        </sharp-field>
        <div class="form-control-feedback">{{stateMessage}}</div>
        <small class="form-text text-muted">{{helpMessage}}</small>
    </div>
</template>

<script>
    import Field from './Field';

    export default {
        name: 'SharpFieldContainer',

        components: {
            [Field.name]:Field
        },

        inject:['$tab'],

        props : {
            ...Field.props,

            label: String,
            helpMessage: String,
            fieldErrors: Array
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
            fieldErrors([error]) {
                if(typeof error === 'string') {
                    this.setError(error);
                }
            }
        },
        computed: {
            formGroupClasses() {
                return {
                    'has-danger': this.state==='error',
                    'has-success': this.state==='ok'
                }
            },
            extraStyle() {
                return this.fieldProps.extraStyle;
            },
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
            }
        },
        mounted() {
            //console.log(this);
        }
    }
</script>