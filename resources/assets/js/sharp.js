import Vue from 'vue';
import Form from './components/Form';
import FieldDisplay from './components/FieldDisplay';


window.Vue = Vue;

Vue.component(FieldDisplay.name, FieldDisplay);



new Vue({
    el:"#app",
    components: {
        [Form.name]:Form,
        //[List.name]:List
    }
});