import Vue from 'vue';
import EmbeddedForm from './components/form/EmbeddedForm';
import FieldDisplay from './components/form/FieldDisplay';

// prevent recursive components import
Vue.component(FieldDisplay.name, FieldDisplay);

new Vue({
    el:'#sharp-app',
    components: {
        [EmbeddedForm.name]:EmbeddedForm
    }
});