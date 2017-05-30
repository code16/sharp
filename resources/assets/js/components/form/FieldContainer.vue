<template>
    <div class="form-group" :class="formGroupClasses">
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
        props : {
            ...Field.props,

            label: String,
            helpMessage: Array,
            fieldErrors: Array
        },
        data() {
            return {
                state:'classic',
                stateMessage:'',
                alerts:[]
            }
        },
        watch: {
            value() {
                if(this.state === 'error')
                    this.clear();
            },
            fieldErrors([e]) {
                e && this.setError(e);
            }
        },
        computed: {
            formGroupClasses() {
                return {
                    'has-danger': this.state==='error',
                    'has-success': this.state==='ok'
                }
            }
        },
        methods: {
            setError(error) {
                this.state = 'error';
                this.stateMessage = error;
            },
            setOk() {
                this.state = 'ok';
                this.stateMessage = '';
            },
            clear() {
                this.state = 'classic';
                this.stateMessage = '';
            }
        }
    }
</script>