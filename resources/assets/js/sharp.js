import Vue from 'vue';
import Form from './components/form/Form';
import FieldDisplay from './components/form/FieldDisplay';
import ActionView from './components/ActionView';
import Dashboard from './components/dashboard/Dashboard';

// prevent recursive components import
Vue.component(FieldDisplay.name, FieldDisplay);


new Vue({
    el:"#sharp-app",

    provide() {
        return {
            isSharp:true
        }
    },

    components: {
        [ActionView.name]:ActionView,
        [Form.name]:Form,
        [Dashboard.name]:Dashboard
    }
});