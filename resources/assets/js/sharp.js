import Vue from 'vue';
import Form from './components/Form';



window.Vue = Vue;



new Vue({
    el:"#app",
    components: {
        [Form.name]:Form,
        //[List.name]:List
    }
});