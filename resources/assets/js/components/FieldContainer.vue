<template>
    <div class="form-group" :class="formGroupClasses">
        <label v-html="label" class="form-control-label"></label>
        <sharp-field v-bind="$props" @error="setError" @ok="setOk"></sharp-field>
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

            label: {
                type: String
            },
            helpMessage: {
                type: Array
            }
        },
        data() {
            return {
                state:'classic',
                stateMessage:''
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
            }
        }
    }
</script>