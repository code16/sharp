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
                     @clear="clear"
                     @alert="addAlert"
                     @alert-clear="clearAlert">
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
                stateMessage:'',
                alerts:[]
            }
        },
        watch: {
            value() {
                if(this.state === 'error')
                    this.clear();
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
            addAlert(id, type, msg) {
                if(!this.alerts.find(a=>a.id===id))
                    this.alerts.push({id, msg, type});
            },
            clearAlert(id) {
                let index = this.alerts.findIndex(a=>a.id===id);
                if(index>=0)
                    this.alerts.splice(index,1);
            },
            alertClass(type) {
               switch(type) {
                   case 'error': return 'alert-danger';
                   case 'success': return 'alert-success';
                   default: return 'alert-info';
               }
            },
            setError(error) {
                this.state = 'error';
                this.stateMessage = error;
            },
            setOk() {
                this.state = 'ok';
                this.alertMessages = [];
                this.stateMessage = '';
            },
            clear() {
                this.state = 'classic';
                this.alertMessages = [];
                this.stateMessage = '';
            }
        }
    }
</script>