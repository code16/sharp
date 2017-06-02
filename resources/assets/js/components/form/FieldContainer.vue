<template>
    <div class="form-group" :class="formGroupClasses" :style="extraStyle">
        <label v-html="label" class="form-control-label"></label>
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
            }
        },
        methods: {
            setError(error) {
                this.state = 'error';
                this.stateMessage = error;
                this.$tab && this.$tab.setError(this.fieldKey);
            },
            setOk() {
                this.state = 'ok';
                this.stateMessage = '';
            },
            clear() {
                this.state = 'classic';
                this.stateMessage = '';
                this.$tab && this.$tab.clearError(this.fieldKey);
            }
        },
        mounted() {
            //console.log(this);
        }
    }
</script>